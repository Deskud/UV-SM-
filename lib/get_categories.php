<?php
require "../dbconnection.php";

$query = "SELECT DISTINCT c.category_id, c.category_name
          FROM products p 
          JOIN categories c
          ON p.category_id = c.category_id
          WHERE is_archived = 0
          AND cell_num IS NOT NULL
          AND product_quantity != 0
          ORDER BY c.category_id ASC";

$result = mysqli_query($conne, $query);

$categories = array();  // Initialize an array to hold multiple categories

if ($result && mysqli_num_rows($result) > 0) {
    while ($category = mysqli_fetch_assoc($result)) {
        $categories[] = $category;  // Add each category to the array
    }
    echo json_encode($categories);  // Return the entire array as JSON
} else {
    echo json_encode(['error' => 'Category not found.']);
}
?>
