<?php
require "../dbconnection.php";

$order_id = $_GET['order_id'] ?? null;

$response = array("success" => false, "items" => array());

if ($order_id) {
    $query = "SELECT p.product_name, i.quantity 
              FROM items i
              LEFT JOIN products p ON i.product_id = p.product_id
              WHERE i.order_id = ?";


// Changed to bind params for security. 
    $stmt = $conne->prepare($query);
    $stmt->bind_param('s', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $response['items'][] = array(
                'product_name' => $row['product_name'], 
                'quantity' => $row['quantity']
            );
        }

        if (count($response['items']) > 0) {
            $response['success'] = true;
        }
    } else {
        $response['error'] = 'Failed to retrieve items.';
    }
    
    $stmt->close();
} else {
    $response['error'] = 'Order ID not provided.';
}

echo json_encode($response);
?>
