<?php
require "../../dbconnection.php";

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

// Get the QR code from the request
$qrcode = isset($_GET['qrcode']) ? intval($_GET['qrcode']) : 0;

if ($qrcode > 0) {
    // Query to find the transaction related to the QR code
    $stmt = $pdo->prepare("SELECT t.transaction_id, t.order_id,
                                  t.status, o.order_date 
                            FROM transactions t 
                            JOIN orders o ON t.order_id = o.order_id 
                            WHERE t.qr_code = :qrcode");
    $stmt->execute(['qrcode' => $qrcode]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($transaction) {
        if ($transaction['status'] === 'fully claimed') {
            // Transaction already processed
            $response = ["status" => "error", "message" => "Transaction was already processed."];
        } else {
            // Fetch items for the found order and calculate remaining quantities
            $stmtItems = $pdo->prepare("SELECT i.product_id, p.product_name, p.unit_num, 
                                               i.quantity, COALESCE(i.quantity_dispensed, 0) AS quantity_dispensed,
                                               s.size_name
                                        FROM items i 
                                        JOIN products p ON i.product_id = p.product_id 
                                        LEFT JOIN sizes s ON p.size_id = s.size_id
                                        WHERE i.order_id = :order_id
                                          AND (i.status = 'unclaimed' OR i.status = 'partially claimed')");
            $stmtItems->execute(['order_id' => $transaction['order_id']]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            // Update item quantities based on remaining quantities
            foreach ($items as &$item) {
                $item['quantity_remaining'] = $item['quantity'] - $item['quantity_dispensed'];
            }

            // Prepare response
            $response = [
                "status" => "success",
                "transaction_id" => $transaction['transaction_id'],
                "order_id" => $transaction["order_id"],
                "order_date" => $transaction['order_date'],
                "items" => $items
            ];
        }
    } else {
        $response = ["status" => "error", "message" => "Transaction not found."];
    }
} else {
    $response = ["status" => "error", "message" => "Invalid QR code."];
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
