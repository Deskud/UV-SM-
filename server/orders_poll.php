<?php
require "../dbconnection.php";

// Fuck ajax polling. Masyado heavy ang load. Dapat nalaman ko kagad ang websocket stuff, too late.
// Fetch all orders
$orders = [];
$result = $conne->query("SELECT * FROM orders"); // Fetch all orders
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

echo json_encode($orders);
?>

