<?php

// Sobrang haba dapat i loop ko na lang

// di ko alam kung bakit pero pag "require '..dbconnection.php' lang nilagay ko di nag co-connect kahit ganon din naman ginawa 
// ko sa lahat ng laman ng main-content. Tinry ko require _DIR_ ngayon siya gumana."
require __DIR__ . '/../dbconnection.php';

// require "../dbconnection.php";


$query = "SELECT products.cell_num, products.product_quantity, products.product_name, 
categories.category_name, sizes.size_name
FROM products
INNER JOIN categories ON products.category_id = categories.category_id
INNER JOIN sizes ON products.size_id = sizes.size_id
WHERE products.cell_num IN (1, 2, 3, 4, 5, 6 ,7 ,8 ,9 ,10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24)
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
    $cellNumber = $row['cell_num'];
    $cellData[$cellNumber] = [
        'quantity' => $row['product_quantity'],
        'product_name' => $row['product_name'],
        'category_name' => $row['category_name'],
        'size_name' => $row['size_name']

    ];
}

?>
<h3 class="title-form">Dashboard</h3>
<hr>
<div class="dash-container">
    <?php
    for ($cell = 1; $cell <= 24; $cell++) {
    ?>
        <div class="unit-containers">
            <div class="unit-number">
                <h2>UNIT <?php echo $cell; ?></h2>
            </div>
            <svg class="cell-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                <path d="M211.8 0c7.8 0 14.3 5.7 16.7 13.2C240.8 51.9 277.1 80 320 80s79.2-28.1 91.5-66.8C413.9 5.7 420.4 0 428.2 0l12.6 0c22.5 0 44.2 7.9 61.5 22.3L628.5 127.4c6.6 5.5 10.7 13.5 11.4 22.1s-2.1 17.1-7.8 23.6l-56 64c-11.4 13.1-31.2 14.6-44.6 3.5L480 197.7 480 448c0 35.3-28.7 64-64 64l-192 0c-35.3 0-64-28.7-64-64l0-250.3-51.5 42.9c-13.3 11.1-33.1 9.6-44.6-3.5l-56-64c-5.7-6.5-8.5-15-7.8-23.6s4.8-16.6 11.4-22.1L137.7 22.3C155 7.9 176.7 0 199.2 0l12.6 0z" />
            </svg>
            <p><?php echo isset($cellData[$cell]) ? $cellData[$cell]['product_name'] : 'No Data'; ?></p>
            <p>Size: <?php echo isset($cellData[$cell]) ? $cellData[$cell]['size_name'] : 'No Data'; ?></p>
            <p>Quantity: <?php echo isset($cellData[$cell]) ? $cellData[$cell]['quantity'] : 0; ?></p>
        </div>
    <?php
    }
    ?>
</div>