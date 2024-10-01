<?php
require "../dbconnection.php";

// Retrieve the JSON data sent by Arduino
$data = file_get_contents("php://input");

// Decode the JSON data
$orderDetails = json_decode($data, true);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON"]);
    exit();
}

// Check if 'order_details' key exists
if (!isset($orderDetails['order_details']) || !is_array($orderDetails['order_details'])) {
    echo json_encode(["status" => "error", "message" => "No order details provided"]);
    exit();
}

// Generate a new order
$order_status = 'pending';
$sql = "INSERT INTO orders (status) VALUES ('$order_status')";

if ($conne->query($sql) === TRUE) {
    $order_id = $conne->insert_id; // Get the generated order ID

    // Insert order items
    foreach ($orderDetails['order_details'] as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];

        // Prepare and execute the SQL statement
        $stmt = $conne->prepare("INSERT INTO items (product_id, order_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $product_id, $order_id, $quantity);

        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Failed to insert item: " . $stmt->error]);
            exit();
        }
        $stmt->close();
    }

    // Return success message with order_id
    echo json_encode(["status" => "success", "message" => "Order placed successfully", "order_id" => $order_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to create order"]);
}

$conne->close();
?>
