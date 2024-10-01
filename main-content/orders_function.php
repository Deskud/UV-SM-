<?php
require "../dbconnection.php"; // Include database connection
include('../phpqrcode/qrlib.php');
include '../session_check.php';


header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $orderId = $_POST['order_id'] ?? '';

    if (!$orderId) {
        echo json_encode(['error' => 'Invalid Order ID']);
        exit;
    }

    switch ($action) {

        case 'update':
            // Fetch the order_id from the POST data
            $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
            $quantities = $_POST['quantities'] ?? []; // Make sure to match the key 'quantities'

            // Debug the incoming values
            if (empty($quantities)) {
                echo json_encode(['error' => 'No quantities provided.']);
                exit;
            }
            if ($orderId <= 0) {
                echo json_encode(['error' => 'Invalid order ID provided.']);
                exit;
            }

            // Proceed with updating the quantities if valid
            foreach ($quantities as $itemId => $quantity) {
                $itemId = intval($itemId);
                $quantity = intval($quantity);

                $stmt = $conne->prepare("UPDATE items SET quantity = ? WHERE item_id = ? AND order_id = ?");
                $stmt->bind_param('iii', $quantity, $itemId, $orderId);

                if (!$stmt->execute()) {
                    echo json_encode(['error' => 'Failed to update item with ID ' . $itemId]);
                    exit;
                }
            }
            // If all updates were successful
            echo json_encode(['success' => 'Order updated successfully']);
            break;

        case 'finish':

            $itemsQuery = "SELECT item_id, quantity FROM items WHERE order_id = ?";
            $stmt = $conne->prepare($itemsQuery);
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalItems = 0;
            $itemDetails = [];

            while ($row = $result->fetch_assoc()) {
                $itemId = $row['item_id'];
                $quantity = $row['quantity'];
                $totalItems += $quantity; // Sum up the total quantity
                // Add item details for QR code
                $itemDetails[] = "Item ID: $itemId, Quantity: $quantity";
            }

            $stmt->close();

            // Finish order and generate QR code
            // $qrData = "Transaction ID:" . $orderId . "\nTotal Items:" . $totalItems . "\n" . implode("\n", $itemDetails);
            $qrData = generateRandCode();
            $filePath = '../qrcodes/order_' . $orderId . '.png';
            QRcode::png($qrData, $filePath);


            // Update order status to 'completed'
            $stmt = $conne->prepare("UPDATE orders SET status = 'pending' WHERE order_id = ?");
            $stmt->bind_param('i', $orderId);

            if ($stmt->execute()) {
                //  Script para ma insert ang mga data (item_id, quantity, order_id, etc..) sa transactions table.
                $userId = $_SESSION['user_id'];
                $status = 'unclaimed'; // Unclaimed muna hanggang di pa na re-recieve ng customer yung uniform.
                $transactionDate = date('Y-m-d H:i:s');
                $totalAmount = rand(300, 1200); //place holder just to add a value sa total amount column Eksdee

                $insertQuery = "INSERT INTO transactions (order_id, user_id, total_quantity, total_amount, transaction_date, qr_code, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtTrans = $conne->prepare($insertQuery);
                $stmtTrans->bind_param('iiidsss', $orderId, $userId, $totalItems, $totalAmount, $transactionDate, $qrData, $status);

                if ($stmtTrans->execute()) {
                    echo json_encode(['success' => 'Order completed', 'qrcode' => $filePath, 'message' => 'New transaction added!']);
                } else {
                    echo json_encode(['error' => 'Failed to insert transaction data']);
                }

                $stmtTrans->close();
            } else {
                echo json_encode(['error' => 'Failed to finish order']);
            }

            $stmt->close();
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

// Generate rand code for qr generation
function generateRandCode()
{
    return random_int(100000, 999999);
}
