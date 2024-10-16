<?php
require "../../dbconnection.php";

if (isset($_GET['product_name'])) {
    $product_name = $_GET['product_name'];

    // Prepare the SQL query with placeholders
    $query = "SELECT p.product_id, s.size_id, s.size_name
              FROM products p 
              JOIN sizes s ON p.size_id = s.size_id
              WHERE p.product_name = ?
              AND p.is_archived = 0
              AND p.cell_num IS NOT NULL
              AND p.product_quantity != 0";

    // Prepare the statement
    $stmt = $conne->prepare($query);

    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to prepare query: ' . $conne->error]);
        exit;
    }

    // Bind the product_name parameter (as a string)
    $stmt->bind_param("s", $product_name);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    $sizes = array();  // Initialize an array to store the sizes

    if ($result && $result->num_rows > 0) {
        while ($size = $result->fetch_assoc()) {
            $sizes[] = $size;  // Add each size to the array
        }
        echo json_encode($sizes);  // Return the array as JSON
    } else {
        echo json_encode(['error' => 'Sizes not found.']);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['error' => 'No product_name provided.']);
}

// Close the database connection
$conne->close();
?>
