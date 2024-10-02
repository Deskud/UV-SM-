<?php
require "../../dbconnection.php";

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // Prepare the SQL query with a placeholder
    $query = "SELECT DISTINCT product_name
              FROM products
              WHERE category_id = ?
              AND is_archived = 0
              AND cell_num IS NOT NULL
              AND product_quantity != 0";

    // Prepare the statement
    $stmt = $conne->prepare($query);
    
    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to prepare query: ' . $conne->error]);
        exit;
    }

    // Bind the category_id parameter (assuming it's an integer)
    $stmt->bind_param("i", $category_id);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    $products = array();  // Initialize an array to store the products

    if ($result && $result->num_rows > 0) {
        while ($product = $result->fetch_assoc()) {
            $products[] = $product;  // Add each product to the array
        }
        echo json_encode($products);  // Return the array as JSON
    } else {
        echo json_encode(['error' => 'Products not found.']);
    }

    // Close the statement and connection
    $stmt->close();
} else {
    echo json_encode(['error' => 'No category_id provided.']);
}

// Close the database connection
$conne->close();
?>
