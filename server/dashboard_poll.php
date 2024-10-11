<?php
require "../dbconnection.php";

$query = "SELECT products.unit_num, products.product_quantity, products.product_name, 
categories.category_name, sizes.size_name
FROM products
INNER JOIN categories ON products.category_id = categories.category_id
INNER JOIN sizes ON products.size_id = sizes.size_id
WHERE products.unit_num IN (1, 2, 3, 4, 5, 6 ,7 ,8 ,9 ,10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24)";

$result = $conne->query($query);

$cellQuantities = [];

while ($row = $result->fetch_assoc()) {
    $cellNumber = $row['unit_num'];
    $cellQuantities[$cellNumber] = [
        'quantity' => $row['product_quantity'],
        'product_name' => $row['product_name'],
        'category_name' => $row['category_name'],
        'size_name' => $row['size_name']
    ];
}

echo json_encode($cellQuantities);
?>
