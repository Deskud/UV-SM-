<?php
require "../dbconnection.php";

$order_id = $_GET['order_id'];

$response = array("success" => false, "items" => array());

// Validate the order_id to prevent SQL injection
if (isset($order_id)) {
    // Prepare and execute the query to fetch order details
    $orderQuery = "SELECT
                   FROM orders o 
                   JOIN order_items i ON o.order_id = i.order_id 
                   JOIN products p ON i.product_id = p.product_id 
                   WHERE o.order_id = '$order_id'";

    $result = mysqli_query($conne, $orderQuery);

    // Fetch the order details
    if ($result) {
        // Get the first row for the student_id and QR code
        if ($row = mysqli_fetch_assoc($result)) {
            $response['student_id'] = $row['student_id'];

            // Add the first item's details
            $response['items'][] = [
                'product_name' => $row['product_name'],
                'quantity' => $row['quantity']
            ];

            // Fetch remaining items
            while ($row = mysqli_fetch_assoc($result)) {
                $response['items'][] = [
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity']
                ];
            }
            
            $response['success'] = true; // Indicate success
        }
    } else {
        // Log any errors (optional)
        error_log("Database Query Failed: " . mysqli_error($conne));
    }
}

// Return the JSON response
echo json_encode($response);
