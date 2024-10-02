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
$qrcode = 654321;

if ($qrcode > 0) {
    // Query to find the transaction related to the QR code
    $stmt = $pdo->prepare("SELECT t.transaction_id, t.order_id,
                                  t.status, o.order_date 
                            FROM transactions t 
                            JOIN orders o ON t.order_id = o.order_id 
                            WHERE t.qr_code = :qrcode AND t.status = 'unclaimed '");
    $stmt->execute(['qrcode' => $qrcode]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($transaction) {
        // Fetch items for the found order
        $stmtItems = $pdo->prepare("SELECT i.product_id, p.product_name, p.cell_num, i.quantity 
                                     FROM items i 
                                     JOIN products p ON i.product_id = p.product_id 
                                     WHERE i.order_id = :order_id");
        $stmtItems->execute(['order_id' => $transaction['order_id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // Prepare response
        $response = [
            "status" => "success",
            "transaction_id" => $transaction['transaction_id'],
            "order_id" => $transaction["order_id"],
            "order_date" => $transaction['order_date'],
            "items" => $items
        ];
    } else {
        $response = ["status" => "error", "message" => "Transaction not found or already processed."];
    }
} else {
    $response = ["status" => "error", "message" => "Invalid QR code."];
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
