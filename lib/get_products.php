<?php
require "../dbconnection.php";

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    $query = "SELECT DISTINCT product_name
              FROM products
              WHERE category_id = '$category_id'
              AND is_archived = 0
              AND cell_num IS NOT NULL
              AND product_quantity != 0";

    $result = mysqli_query($conne, $query);

    $products = array();

    if ($result && mysqli_num_rows($result) > 0) {
        while ($product = mysqli_fetch_assoc($result)) {
            $products[] = $product;
        }
        echo json_encode($products);
    } else {
        echo json_encode(['error' => 'products not found.']);
    }
}
?>