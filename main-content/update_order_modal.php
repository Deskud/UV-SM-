<?php
require "../dbconnection.php";

$order_id = $_GET['order_id'];

$response = array("success" => false, "items" => array());

if (isset($order_id)) {
    // Query to get the updated items for the order
    $query = "SELECT item_id, quantity FROM items WHERE order_id = '$order_id'";
    $result = mysqli_query($conne, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $response['items'][] = $row;
    }

    if (count($response['items']) > 0) {
        $response['success'] = true;
    }
}

echo json_encode($response);
?>
