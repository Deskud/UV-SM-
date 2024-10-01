<?php
require "../dbconnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['cell_num'])) {
        $product_id = intval($_POST['product_id']);
        $cell_num = $_POST['cell_num'] === '' ? NULL : intval($_POST['cell_num']); // Set to NULL if empty

        $update_query = "UPDATE products SET cell_num = ? WHERE product_id = ?";
        $stmt = $conne->prepare($update_query);
        
        if ($stmt === false) {
            echo json_encode(['success' => false, 'error' => 'Failed to prepare statement.']);
            exit();
        }

        $stmt->bind_param('ii', $cell_num, $product_id);
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
