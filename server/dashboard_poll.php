<?php


// di ko alam kung bakit pero pag "require '..dbconnection.php' lang nilagay ko di nag co-connect kahit ganon din naman ginawa 
// ko sa lahat ng laman ng main-content. Tinry ko require _DIR_ ngayon siya gumana."
require __DIR__ . '/../dbconnection.php';

// require "../dbconnection.php";


$query = "SELECT products.unit_num, products.product_quantity, products.product_name, 
categories.category_name, sizes.size_name
FROM products
INNER JOIN categories ON products.category_id = categories.category_id
INNER JOIN sizes ON products.size_id = sizes.size_id
WHERE products.unit_num IN (1, 2, 3, 4, 5, 6 ,7 ,8 ,9 ,10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24)
";

$result = $conne->query($query);

$cellQuantities = [
    1 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    2 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    3 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    4 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    5 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    6 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    7 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    8 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    9 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    10 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    11 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    12 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    13 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    14 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    15 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    16 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    17 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    18 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    19 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    20 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    21 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    22 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    23 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
    24 => ['quantity' => 0, 'product_name' => '', 'category_name' => '', 'size_name' => ''],
];

while ($row = $result->fetch_assoc()) {
    $cellNumber = $row['unit_num'];
    $cellData[$cellNumber] = [
        'quantity' => $row['product_quantity'],
        'product_name' => $row['product_name'],
        'category_name' => $row['category_name'],
        'size_name' => $row['size_name']

    ];
}
echo json_encode($cellQuantities);
?>