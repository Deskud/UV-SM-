<?php
require "../dbconnection.php";

header('Content-Type: application/json'); // Ensure JSON is returned

$response = array('success' => false, 'items' => array());

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    $query = "SELECT product_id, quantity FROM items WHERE order_id = ?";
    $stmt = $conne->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $response['items'][] = $row;
        }
        
        $response['success'] = true;
    } else {
        $response['error'] = 'SQL error: ' . $conne->error;
    }
} else {
    $response['error'] = 'Order ID is missing.';
}

echo json_encode($response);
