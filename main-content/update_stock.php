<?php
require "../dbconnection.php";

// Check if product_id and quantity parameters are provided
if (isset($_GET['product_id']) && isset($_GET['quantity'])) {
    $product_id = intval($_GET['product_id']);
    $quantity = intval($_GET['quantity']);

    // Update stock query
    $sql = "UPDATE products SET product_quantity = product_quantity - ? WHERE product_id = ?";

    // Prepare and bind statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $product_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Stock updated successfully!";
    } else {
        echo "Error updating stock: " . $conn->error;
    }

    // Close connections
    $stmt->close();
    $conne->close();
} else {
    echo "Invalid parameters. Please provide product_id and quantity.";
}
?>
