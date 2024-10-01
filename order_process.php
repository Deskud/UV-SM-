<?php
require "dbconnection.php";
include 'session_check.php'; // Ensure user is authenticated

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}


// Make sure user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error
    header("Location: login.html");
    exit();
}

// Functions to handle order actions
function updateOrder($orderId, $items) {
    global $pdo;

    // Fetch existing items to check their stock
    $existingItems = $pdo->prepare("SELECT item_id, quantity FROM items WHERE order_id = :orderId");
    $existingItems->execute(['orderId' => $orderId]);
    $existingItemsData = $existingItems->fetchAll(PDO::FETCH_KEY_PAIR);

    // Delete items that were removed
    $stmt = $pdo->prepare("DELETE FROM items WHERE order_id = :orderId AND item_id NOT IN (" . implode(',', array_keys($items)) . ")");
    $stmt->execute(['orderId' => $orderId]);

    // Update quantities for existing items or insert new items
    foreach ($items as $itemId => $quantity) {
        if ($quantity > 0) {
            // Check available stock
            $stockCheck = $pdo->prepare("SELECT product_quantity FROM products WHERE product_id = (SELECT product_id FROM items WHERE item_id = :itemId)");
            $stockCheck->execute(['itemId' => $itemId]);
            $product = $stockCheck->fetch(PDO::FETCH_ASSOC);

            if ($product && $quantity <= $product['product_quantity']) {
                $stmt = $pdo->prepare("INSERT INTO items (order_id, item_id, quantity) VALUES (:orderId, :itemId, :quantity)
                    ON DUPLICATE KEY UPDATE quantity = :quantity");
                $stmt->execute(['orderId' => $orderId, 'itemId' => $itemId, 'quantity' => $quantity]);
            } else {
                echo "Insufficient stock for item ID: $itemId. Update failed.";
                return;
            }
        }
    }

    echo "Order updated successfully.";
}

function cancelOrder($orderId) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE orders SET status = 'pending' WHERE order_id = :orderId");
    $stmt->execute(['orderId' => $orderId]);
    echo "Order cancelled successfully.";   
}

function generateUniqueQRCode() {
    global $pdo;
    do {
        $qrCode = rand(100000, 999999); // Generate a random 6-digit number
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE qr_code = :qrCode");
        $stmt->execute(['qrCode' => $qrCode]);
        $exists = $stmt->fetchColumn();
    } while ($exists > 0);

    return $qrCode;
}

function finishOrder($orderId, $userId) {
    global $pdo;

    // Calculate total amount and quantity
    $stmt = $pdo->prepare("SELECT SUM(i.quantity) AS total_quantity, SUM(i.quantity * p.price) AS total_amount
                            FROM items i JOIN products p ON i.product_id = p.product_id WHERE i.order_id = :orderId");
    $stmt->execute(['orderId' => $orderId]);
    $orderData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($orderData['total_quantity'] == 0) {
        echo "No items to finish the order.";
        return;
    }

    // Generate a unique QR code
    $qrCode = generateUniqueQRCode();

    // Insert into transactions table
    $stmt = $pdo->prepare("INSERT INTO transactions (order_id, user_id, total_quantity, total_amount, status, qr_code)
                            VALUES (:orderId, :userId, :totalQuantity, :totalAmount, 'pending', :qrCode)");
    $stmt->execute([
        'orderId' => $orderId,
        'userId' => $userId,
        'totalQuantity' => $orderData['total_quantity'],
        'totalAmount' => $orderData['total_amount'],
        'qrCode' => $qrCode
    ]);

    // Mark order as completed
    $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE order_id = :orderId");
    $stmt->execute(['orderId' => $orderId]);

    echo "Order finished successfully with QR code: $qrCode";
}

// Handling requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $orderId = intval($_POST['order_id']);
        
        switch ($_POST['action']) {
            case 'update':
                // Expecting an associative array of itemId => quantity
                $items = $_POST['quantity']; // Adjust the key name as per your form's input
                updateOrder($orderId, $items);
                break;

            case 'cancel':
                cancelOrder($orderId);
                break;

            case 'finish':
                $userId = intval($_POST['user_id']); // Use the user ID from the session
                finishOrder($orderId, $userId);
                break;

            default:
                echo "Invalid action.";
                break;
        }
    } else {
        echo "No action specified.";
    }
} else {
    echo "Invalid request method.";
}
?>
