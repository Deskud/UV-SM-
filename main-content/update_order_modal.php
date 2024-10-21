<?php
require "../dbconnection.php";

$order_id = $_GET['order_id'];

$response = array("success" => false, "items" => array());

if (isset($order_id)) {
    $query = "SELECT p.product_name, i.quantity 
              FROM items i
              JOIN products p ON i.product_id = p.product_id
              WHERE i.order_id = '$order_id'";
    $result = mysqli_query($conne, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response['items'][] = array(
                'product_name' => $row['product_name'], // Make sure product_name is available
                'quantity' => $row['quantity']
            );
        }

        if (count($response['items']) > 0) {
            $response['success'] = true;
        }
    } else {
        // Handle query failure
        $response['error'] = 'Failed to retrieve items.';
    }
} else {
    $response['error'] = 'Order ID not provided.';
}

echo json_encode($response);
