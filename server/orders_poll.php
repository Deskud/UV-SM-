<?php
require "../dbconnection.php";

// Fetch all orders
$orders = [];
$result = $conne->query("SELECT * FROM orders"); // Fetch all orders
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Return JSON response
echo json_encode($orders);
?>
