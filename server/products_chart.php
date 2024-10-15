<?php
require "../dbconnection.php";

$products = [];

// Fetch product names and quantities/stock from the database
$result = $conne->query("SELECT gender, product_quantity FROM products WHERE gender = 'male'  OR gender = 'unisex' OR gender = 'female' ");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Return the data as JSON 
echo json_encode($products);
?>
