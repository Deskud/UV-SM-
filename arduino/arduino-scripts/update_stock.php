<?php
require "../../dbconnection.php";

// Check if product_id and quantity parameters are provided
if (isset($_GET['product_id']) && isset($_GET['quantity'])) {
    $product_id = intval($_GET['product_id']);
    $quantity = intval($_GET['quantity']);

    // Update stock query
    $sql = "UPDATE products p
            JOIN items i ON p.product_id = i.product_id
            JOIN transactions t ON i.order_id = t.order_id
            SET 
                -- Reduce product quantity in the products table
                p.product_quantity = p.product_quantity - ?,

                -- Increase quantity dispensed in items and transactions tables
                i.quantity_dispensed = i.quantity_dispensed + ?,
                t.quantity_dispensed = t.quantity_dispensed + ?,

                -- Check if quantity_dispensed in items matches the required quantity, and update is_fully_fulfilled
                i.is_fully_fulfilled = CASE 
                    WHEN i.quantity_dispensed + ? >= i.quantity THEN TRUE
                    ELSE FALSE
                END,

                -- Check if quantity_dispensed in transactions matches the required quantity, and update is_fully_fulfilled
                t.is_fully_fulfilled = CASE 
                    WHEN t.quantity_dispensed + ? >= t.total_quantity THEN TRUE
                    ELSE FALSE
                END
            WHERE p.product_id = ?
            AND i.product_id = ?
            AND t.order_id = ?";

    // Prepare and bind statement
    $stmt = $conne->prepare($sql);
    $stmt->bind_param("iiiiiiii", $quantity, $quantity, $quantity, $quantity, $quantity, $product_id, $product_id, $product_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Stock updated successfully!";
    } else {
        echo "Error updating stock: " . $conne->error;
    }

    // Close connections
    $stmt->close();
    $conne->close();
} else {
    echo "Invalid parameters. Please provide product_id and quantity.";
}
