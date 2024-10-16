<?php
require "../dbconnection.php";

$products = [];

// Fetch product names and quantities/stock where unit_num is between 1 and 12
$result = $conne->query("SELECT unit_num, product_quantity FROM products WHERE unit_num BETWEEN 1 AND 12 AND product_quantity > 0");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
echo json_encode($products);
