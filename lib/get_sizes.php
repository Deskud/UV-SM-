<?php
require "../dbconnection.php";

if (isset($_GET['product_name'])) {
    $product_name = $_GET['product_name'];

    $query = "SELECT p.product_id, s.size_id, s.size_name
              FROM products p 
              JOIN sizes s
              ON p.size = s.size_id
              WHERE product_name = '$product_name'
              AND is_archived = 0
              AND cell_num IS NOT NULL
              AND product_quantity != 0";

    $result = mysqli_query($conne, $query);

    $sizes = array();

    if ($result && mysqli_num_rows($result) > 0) {
        while ($size = mysqli_fetch_assoc($result)) {
            $sizes[] = $size;
        }
        echo json_encode($sizes);
    } else {
        echo json_encode(['error' => 'sizes not found.']);
    }
}

?>