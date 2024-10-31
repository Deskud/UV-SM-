<?php
session_start(); // Start session to track notifications
include '../dbconnection.php';


// PLACE HOLDER FOR NOTIFICATIONS


$response = [];

if (!isset($_SESSION['last_order_check'])) {
    $_SESSION['last_order_check'] = '';
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


//Check if unit is low stock. If unit has low stocks will show alert notification.
$lowStockQuery = "SELECT product_name, unit_num, product_quantity 
                  FROM products 
                  WHERE product_quantity <= 5";
$lowStockResult = $conne->query($lowStockQuery);

if ($lowStockResult && $lowStockResult->num_rows > 0) {
    $response['lowStock'] = true;
    $response['lowStockMessage'] = "Low stocks!";
    
    //Store items for displaying details.
    $lowStockItems = [];
    while ($row = $lowStockResult->fetch_assoc()) {
        $lowStockItems[] = $row;
    }
    $response['lowStockItems'] = $lowStockItems;
} else {
    $response['lowStock'] = false;
}


echo json_encode($response);
