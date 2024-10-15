<?php
require "../dbconnection.php";

$products = [];

// Fetch product names and quantities/stock from the database
$result = $conne->query("SELECT gender, price FROM products");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Return the data as JSON
echo json_encode($products);
?>
