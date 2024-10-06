<?php
require "../dbconnection.php"; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = intval($_POST['order_id']);
    
    // Check the current status of the order
    $result = $conne->query("SELECT status FROM orders WHERE order_id = $orderId");
    
    if ($result && $row = $result->fetch_assoc()) {
        $currentStatus = $row['status'];
        
        // Toggle the order status
        if ($currentStatus === 'pending') {
            // Update status to 'processed'
            $newStatus = 'processed';
        } else {
            // Revert status back to 'pending'
            $newStatus = 'pending';
        }

        $stmt = $conne->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
        $stmt->execute();
        $stmt->close();
        
        echo $newStatus; // Return the new status
    }
}
?>
