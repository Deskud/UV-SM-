<?php
require "../dbconnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['unit_num'])) {
        $product_id = intval($_POST['product_id']);
        $unit_num = $_POST['unit_num'] === '' ? NULL : intval($_POST['unit_num']); // Set to NULL if empty

        $update_query = "UPDATE products SET unit_num = ? WHERE product_id = ?";
        $stmt = $conne->prepare($update_query);
        
        if ($stmt === false) {
            echo json_encode(['success' => false, 'error' => 'Failed to prepare statement.']);
            exit();
        }

        $stmt->bind_param('ii', $unit_num, $product_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error executing query.']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing parameters.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

$conne->close();
?>
