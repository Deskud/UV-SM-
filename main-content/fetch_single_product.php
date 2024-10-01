<?php
require "../dbconnection.php";

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch the product details from the database
    // $query = "SELECT * FROM products WHERE product_id = '$product_id'";

    $query = "SELECT products.*, categories.category_name
          FROM products
          INNER JOIN categories
          ON products.category_id = categories.category_id
          WHERE products.product_id = '$product_id'";

    $result = mysqli_query($conne, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        echo json_encode($product); // Return product data as JSON
    } else {
        echo json_encode(['error' => 'Product not found.']);
    }
}
?>
