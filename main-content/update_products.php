<?php
require "../dbconnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $product_category = $_POST['category'];
    $product_name = $_POST['name'];
    $product_quantity = $_POST['quantity'];
    $product_price = $_POST['price'];

    // Update the product details in the database
    $update_product = "UPDATE products 
    SET  category_id = '$product_category', product_name = '$product_name', product_quantity = '$product_quantity', price = '$product_price' 
    WHERE product_id = '$product_id'";

    if (mysqli_query($conne, $update_product)) {
        echo "Product updated successfully!";
    } else {
        echo "Error updating product: " . mysqli_error($conne);
    }
}
?>
