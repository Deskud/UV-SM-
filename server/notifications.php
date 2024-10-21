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

// Check if there are new transactions
$query = "SELECT COUNT(*) as count, MAX(transaction_date) as last_date FROM transactions WHERE transaction_date > NOW() - INTERVAL 5 MINUTE";
$result = $conne->query($query);
$row = $result->fetch_assoc();
if ($row['count'] > 0 && $row['last_date'] != $_SESSION['last_transaction_check']) {
    $_SESSION['last_transaction_check'] = $row['last_date']; // Update session
    $response['newTransaction'] = true;
    $response['transactionMessage'] = 'New transaction added!';
} else {
    $response['newTransaction'] = false;
}

// Check if there are new items
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

// Check if there are new orders
// $query = "SELECT COUNT(*) as count, MAX(updated_at) as last_date FROM orders WHERE updated_at > NOW() - INTERVAL 5 MINUTE";
// $result = $conne->query($query);
// $row = $result->fetch_assoc();
// if ($row['count'] > 0 && $row['last_date'] != $_SESSION['last_order_check']) {
//     $_SESSION['last_order_check'] = $row['last_date']; // Update session
//     $response['newOrder'] = true;
//     $response['orderMessage'] = 'New order placed!';
// } else {
//     $response['newOrder'] = false;
// }

// Check if new addition
$queryNewOrders ="SELECT COUNT(*) as count, MAX(updated_at) as last_date FROM orders WHERE updated_at > NOW() - INTERVAL 5 MINUTE";

$resultNewOrders = $conne->query($queryNewOrders);
$rowNewOrders = $resultNewOrders->fetch_assoc();

if ($rowNewOrders['count'] > 0 && $rowNewOrders['last_date'] != $_SESSION['last_order_check']) {
    $_SESSION['last_order_check'] = $rowNewOrders['last_date']; // Update session
    $response['newOrder'] = true;
    $response['orderMessage'] = 'New order placed!';
} else {
    $response['newOrder'] = false;
}

// Check for completed orders
$queryCompletedOrders = "
    SELECT COUNT(*) as count 
    FROM orders 
    WHERE status = 'completed' AND updated_at > NOW() - INTERVAL 5 MINUTE
";
$resultCompletedOrders = $conne->query($queryCompletedOrders);
$rowCompletedOrders = $resultCompletedOrders->fetch_assoc();

if ($rowCompletedOrders['count'] > 0) {
    $response['completedOrder'] = true;
    $response['completedMessage'] = 'An order has been completed!';
} else {
    $response['completedOrder'] = false;
}


// Check if there are new products
$query = "SELECT COUNT(*) as count, MAX(date_added) as last_date FROM products WHERE date_added > NOW() - INTERVAL 5 MINUTE";
$result = $conne->query($query);
$row = $result->fetch_assoc();
if ($row['count'] > 0 && $row['last_date'] != $_SESSION['last_product_check']) {
    $_SESSION['last_product_check'] = $row['last_date']; // Update session
    $response['newProduct'] = true;
    $response['productMessage'] = 'New product added!';
} else {
    $response['newProduct'] = false;
}

echo json_encode($response);
