<?php
require '../dbconnection.php'; // Include your database connection

if (isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    // Query to fetch product_id, date_added, gender, and sold_quantity for the specific product_id
    $stmt = $conne->prepare("SELECT product_id, date_added, gender, sold_quantity FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $stmt->bind_result($product_id, $date_added, $gender, $sold_quantity);
    $stmt->fetch();
    $stmt->close();

    // If a record is found, return the product information
    if ($product_id) {
        echo json_encode([
            'success' => true, 
            'product_id' => $product_id,
            'date_added' => $date_added,
            'gender' => $gender,
            'sold_quantity' => $sold_quantity
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
}
