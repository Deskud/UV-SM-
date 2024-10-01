<?php
require "../dbconnection.php";

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $query = "SELECT product_quantity
              FROM products
              WHERE product_id = '$product_id'";

    $result = mysqli_query($conne, $query);

    $sizes = array();

    if ($result && mysqli_num_rows($result) > 0) {
        while ($size = mysqli_fetch_assoc($result)) {
            $sizes[] = $size;
        }
        echo json_encode($sizes);
    } else {
        echo json_encode(['error' => 'quantity not found.']);
    }
}

?>