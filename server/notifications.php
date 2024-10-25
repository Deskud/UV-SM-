<?php
session_start(); // Start session to track notifications
include '../dbconnection.php';


// PLACE HOLDER FOR NOTIFICATIONS



$response = [];

if (!isset($_SESSION['last_transaction_check'])) {
    $_SESSION['last_transaction_check'] = '';
}
if (!isset($_SESSION['last_item_check'])) {
    $_SESSION['last_item_check'] = '';
}
if (!isset($_SESSION['last_order_check'])) {
    $_SESSION['last_order_check'] = '';
}
if (!isset($_SESSION['last_product_check'])) {
    $_SESSION['last_product_check'] = '';
}


//Item update
$query = "SELECT COUNT(*) as count, MAX(updated_at) as last_date FROM items WHERE updated_at > NOW() - INTERVAL 5 MINUTE";
$result = $conne->query($query);
$row = $result->fetch_assoc();
if ($row['count'] > 0 && $row['last_date'] != $_SESSION['last_item_check']) {
    $_SESSION['last_item_check'] = $row['last_date']; // Update session
    $response['newItem'] = true;
    $response['itemMessage'] = 'Item updated!';
} else {
    $response['newItem'] = false;
}


// Check if new order
// Fixed: before it uses update_date in the query, which spams the notification pop up. 
// Changed to order_date to check if its actually newly added.
$queryNewOrders = "SELECT COUNT(*) as count, MAX(order_date) as last_date FROM orders WHERE order_date > NOW() - INTERVAL 5 MINUTE";

$resultNewOrders = $conne->query($queryNewOrders);
$rowNewOrders = $resultNewOrders->fetch_assoc();

if ($rowNewOrders['count'] > 0 && $rowNewOrders['last_date'] != $_SESSION['last_order_check']) {
    $_SESSION['last_order_check'] = $rowNewOrders['last_date']; // Update session
    $response['newOrder'] = true;
    $response['orderMessage'] = 'New order placed!';
} else {
    $response['newOrder'] = false;
}


echo json_encode($response);
