<?php
require "../dbconnection.php";

// Prepare the SQL query
$query = "SELECT DISTINCT c.category_id, c.category_name
          FROM products p 
          JOIN categories c
          ON p.category_id = c.category_id
          WHERE is_archived = 0
          AND cell_num IS NOT NULL
          AND product_quantity != 0
          ORDER BY c.category_id ASC";

// Prepare the statement
$stmt = $conne->prepare($query);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare the query: ' . $conne->error]);
    exit;
}

// Execute the prepared statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

$categories = array();  // Initialize an array to hold multiple categories

if ($result && $result->num_rows > 0) {
    while ($category = $result->fetch_assoc()) {
        $categories[] = $category;  // Add each category to the array
    }
    echo json_encode($categories);  // Return the entire array as JSON
} else {
    echo json_encode(['error' => 'No categories found.']);
}

// Close the prepared statement and the connection
$stmt->close();
$conne->close();
?>
