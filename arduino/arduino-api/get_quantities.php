<?php
require "../dbconnection.php";

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Prepare the SQL query with a placeholder
    $query = "SELECT product_quantity
              FROM products
              WHERE product_id = ?";

    // Prepare the statement
    $stmt = $conne->prepare($query);
    
    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to prepare query: ' . $conne->error]);
        exit;
    }

    // Bind the product_id parameter (assuming it's an integer)
    $stmt->bind_param("i", $product_id);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    $sizes = array();  // Initialize an array to store the quantities

    if ($result && $result->num_rows > 0) {
        while ($size = $result->fetch_assoc()) {
            $sizes[] = $size;
        }
        echo json_encode($sizes);  // Return the array as JSON
    } else {
        echo json_encode(['error' => 'Quantity not found.']);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['error' => 'No product_id provided.']);
}

// Close the database connection
$conne->close();
?>
