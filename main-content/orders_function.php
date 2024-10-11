<?php
require "../dbconnection.php";
include '../phpqrcode/qrlib.php';
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
            $quantities = $_POST['quantities'] ?? []; // Match key 'quantities'

            // Check if quantities and order ID are valid
            if (empty($quantities)) {
                echo json_encode(['error' => 'No quantities provided.']);
                exit;
            }
            if ($orderId <= 0) {
                echo json_encode(['error' => 'Invalid order ID provided.']);
                exit;
            }

            // Get previous total quantity and amount
            $stmt = $conne->prepare("
                SELECT SUM(i.quantity) as total_quantity, SUM(i.quantity * p.price) as total_amount 
                FROM items i
                JOIN products p ON i.product_id = p.product_id
                WHERE i.order_id = ?");
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $stmt->bind_result($prevTotalQuantity, $prevTotalAmount);
            $stmt->fetch();
            $stmt->close();

            // Initialize new total quantity and amount
            $newTotalQuantity = $prevTotalQuantity;
            $newTotalAmount = $prevTotalAmount;

            // Prepare for item_modifications insertion
            $modificationReason = 'Updated item quantities';
            $modifiedBy = $_SESSION['user_id'];
            $modificationTimestamp = date('Y-m-d H:i:s');

            
            $conne->begin_transaction();

            try {
                foreach ($quantities as $itemId => $newQuantity) {
                    $itemId = intval($itemId);
                    $newQuantity = intval($newQuantity);

                    //Kinukuha previous quantity
                    $stmt = $conne->prepare("SELECT quantity, product_id FROM items WHERE item_id = ? AND order_id = ?");
                    $stmt->bind_param('ii', $itemId, $orderId);
                    $stmt->execute();
                    $stmt->bind_result($prevQuantity, $productId);
                    $stmt->fetch();
                    $stmt->close();

                    // Checks kung modified yung item
                    if ($prevQuantity != $newQuantity) {
                        // Get the price from the products table
                        $stmt = $conne->prepare("SELECT price FROM products WHERE product_id = ?");
                        $stmt->bind_param('i', $productId);
                        $stmt->execute();
                        $stmt->bind_result($productPrice);
                        $stmt->fetch();
                        $stmt->close();

                        // Calculate previous and new price based on quantity
                        $prevPrice = $prevQuantity * $productPrice;
                        $newPrice = $newQuantity * $productPrice;

                        // Adjust total quantity and amount by the difference between new and old quantities
                        $quantityDifference = $newQuantity - $prevQuantity;
                        $newTotalQuantity += $quantityDifference; // Adjust by difference
                        $newTotalAmount += ($newPrice - $prevPrice); // Adjust by price difference

                        // Insert into item_modifications table (only for modified items)
                        $stmt = $conne->prepare("
                INSERT INTO item_modifications 
                (modification_id, item_id, order_id, prev_quantity, new_quantity, prev_price, new_price, modification_reason, modified_by, modification_timestamp)
                VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param('iiiiiisss', $itemId, $orderId, $prevQuantity, $newQuantity, $prevPrice, $newPrice, $modificationReason, $modifiedBy, $modificationTimestamp);
                        $stmt->execute();
                        $stmt->close();

                        // Update or remove items in the items table based on quantity
                        if ($newQuantity > 0) {
                            // Update item with new quantity
                            $stmt = $conne->prepare("UPDATE items SET quantity = ? WHERE item_id = ? AND order_id = ?");
                            $stmt->bind_param('iii', $newQuantity, $itemId, $orderId);
                            $stmt->execute();
                            $stmt->close();
                        } else {
                            // Remove the item if the quantity is 0
                            $stmt = $conne->prepare("DELETE FROM items WHERE item_id = ? AND order_id = ?");
                            $stmt->bind_param('ii', $itemId, $orderId);
                            $stmt->execute(); // Run the delete query
                            $stmt->close(); // Close after delete
                        }
                    }
                }

                // Only insert into order_modifications if there is a change in total quantity or amount
                if ($prevTotalQuantity != $newTotalQuantity || $prevTotalAmount != $newTotalAmount) {
                    // Insert into order_modifications table to reflect overall order changes
                    $stmt = $conne->prepare("
            INSERT INTO order_modifications 
            (modification_id, order_id, prev_total_quantity, new_total_quantity, prev_total_amount, new_total_amount, modification_reason, modified_by, modification_timestamp)
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param('iiiiisss', $orderId, $prevTotalQuantity, $newTotalQuantity, $prevTotalAmount, $newTotalAmount, $modificationReason, $modifiedBy, $modificationTimestamp);
                    $stmt->execute();
                    $stmt->close();
                }

                // Commit the transaction
                $conne->commit();

                // Return success response
                echo json_encode(['success' => 'Order updated successfully']);
            } catch (Exception $e) {
                // Rollback if any error occurs
                $conne->rollback();
                echo json_encode(['error' => 'Failed to update order: ' . $e->getMessage()]);
            }

            break;



        case 'finish':

            $itemsQuery = "
            SELECT i.item_id, i.quantity, p.price 
            FROM items i 
            JOIN products p ON i.product_id = p.product_id 
            WHERE i.order_id = ?";

            $stmt = $conne->prepare($itemsQuery);
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalItems = 0;
            $totalAmount = 0.0; // Initialize total amount
            $itemDetails = [];

            while ($row = $result->fetch_assoc()) {
                $itemId = $row['item_id'];
                $quantity = $row['quantity'];
                $price = $row['price']; // Price from the products table
                $totalItems += $quantity; // Sum up the total quantity
                $itemAmount = $price * $quantity; // Calculate item total amount
                $totalAmount += $itemAmount; // Add to total amount

                // Add item details for QR code
                $itemDetails[] = "Item ID: $itemId, Quantity: $quantity, Price: $price, Item Total: $itemAmount";
            }

            $stmt->close();

            // Finish order and generate QR code
            $qrData = generateRandCode();
            $filePath = '../qrcodes/order_' . $orderId . '.png';
            QRcode::png($qrData, $filePath);

            // Update order status to 'processed'
            $stmt = $conne->prepare("UPDATE orders SET status = 'completed' WHERE order_id = ?");
            $stmt->bind_param('i', $orderId);

            if ($stmt->execute()) {
                // Insert data into the transactions table
                $userId = $_SESSION['user_id'];
                $status = 'unclaimed'; // Initial status
                $transactionDate = date('Y-m-d H:i:s');

                // Prepare the insert query for the transaction
                $insertQuery = "
                INSERT INTO transactions (order_id, user_id, total_quantity, total_amount, transaction_date, qr_code, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

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

        case 'remove':
            if (isset($_POST['item_id']) && isset($_POST['order_id'])) {
                $itemId = intval($_POST['item_id']);
                $orderId = intval($_POST['order_id']);
                $modificationReason = "Item removed.";

                $modifiedBy = $_SESSION['user_id'];
                $modificationTime = date('Y-m-d H:i:s');

                // Begin transaction
                $conne->begin_transaction();

                try {
                    $stmt = $conne->prepare("SELECT quantity, product_id FROM items WHERE item_id = ? AND order_id = ?");
                    $stmt->bind_param('ii', $itemId, $orderId);
                    $stmt->execute();
                    $stmt->bind_result($prevQuantity, $productId);
                    $stmt->fetch();
                    $stmt->close();

                    if ($prevQuantity === null) {
                        throw new Exception("Item not found or already removed.");
                    }

                    $stmt = $conne->prepare("SELECT price FROM products WHERE product_id = ?");
                    $stmt->bind_param('i', $productId);
                    $stmt->execute();
                    $stmt->bind_result($productPrice);
                    $stmt->fetch();
                    $stmt->close();

                    $prevPrice = $prevQuantity * $productPrice;

                    $stmt = $conne->prepare("SELECT SUM(quantity) AS prev_total_quantity, SUM(quantity * price) AS prev_total_amount
                                                      FROM items 
                                                      JOIN products ON items.product_id = products.product_id
                                                      WHERE order_id = ?");
                    $stmt->bind_param('i', $orderId);
                    $stmt->execute();
                    $stmt->bind_result($prevTotalQuantity, $prevTotalAmount);
                    $stmt->fetch();
                    $stmt->close();

                    // Calculate new totals
                    $newTotalQuantity = $prevTotalQuantity - $prevQuantity;
                    $newTotalAmount = $prevTotalAmount - $prevPrice;

                    $stmt = $conne->prepare("
                                    INSERT INTO item_modifications 
                                    (modification_id, item_id, order_id, prev_quantity, new_quantity, prev_price, new_price, modification_reason, modified_by, modification_timestamp)
                                    VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param('iiiiiisss', $itemId, $orderId, $prevQuantity, $newQuantity, $prevPrice, $newPrice, $modificationReason, $modifiedBy, $modificationTime);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to insert into item_modifications: " . $stmt->error);
                    }
                    $stmt->close();


                    // Insert into order_modifications for updated totals
                    $stmt = $conne->prepare("
                                    INSERT INTO item_modifications 
                                    (modification_id, item_id, order_id, prev_quantity, new_quantity, prev_price, new_price, modification_reason, modified_by, modification_timestamp)
                                    VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param('iiiiiisss', $itemId, $orderId, $prevQuantity, $newQuantity, $prevPrice, $newPrice, $modificationReason, $modifiedBy, $modificationTimestamp);
                    $stmt->execute();
                    $stmt->close();

                    // Delete the item from the items table
                    $stmt = $conne->prepare("DELETE FROM items WHERE item_id = ? AND order_id = ?");
                    $stmt->bind_param('ii', $itemId, $orderId);
                    $stmt->execute();
                    $stmt->close();

                    // Commit the transaction
                    $conne->commit();
                    echo json_encode(['success' => true]);
                } catch (Exception $e) {
                    // Rollback on failure
                    $conne->rollback();
                    echo json_encode(['error' => 'Failed to remove item: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['error' => 'Item ID or Order ID not provided']);
            }
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
