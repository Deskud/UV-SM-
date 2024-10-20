<?php
require "../dbconnection.php";

if (isset($_GET['transaction_id'])) {
    $transaction_id = $_GET['transaction_id'];

    // Fetch the product details from the database
    // $query = "SELECT * FROM products WHERE product_id = '$product_id'";

    $query = "SELECT * FROM transactions WHERE transaction_id = '$transaction_id'";

    $result = mysqli_query($conne, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $transactions = mysqli_fetch_assoc($result);
        echo json_encode($transactions); // Return product data as JSON
    } else {
        echo json_encode(['error' => 'Transaction not found.']);
    }
}
?>
