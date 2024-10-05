<?php
require 'db_connection.php'; // Include your database connection

if (isset($_POST['item_id']) && isset($_POST['order_id'])) {
    $itemId = intval($_POST['item_id']);
    $orderId = intval($_POST['order_id']);

    // Begin transaction
    $conne->begin_transaction();

    try {
        // Delete the item from the `items` table
        $stmt = $conne->prepare("DELETE FROM items WHERE item_id = ? AND order_id = ?");
        $stmt->bind_param('ii', $itemId, $orderId);
        $stmt->execute();
        $stmt->close();

        // Also delete related records from item_modifications table if necessary
        $stmt = $conne->prepare("DELETE FROM item_modifications WHERE item_id = ? AND order_id = ?");
        $stmt->bind_param('ii', $itemId, $orderId);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conne->commit();

        // Return success response
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conne->rollback();
        echo json_encode(['error' => 'Failed to remove item: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid parameters']);
}
