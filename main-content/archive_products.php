<?php
require "../dbconnection.php";
include '../session_check.php';
checkAccess(ADMIN);

//dito yung process para ma archive yung laman ng table. Nakabase sa product id ang pag archive. 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']); // Sanitize input

        $archive_query = "UPDATE products SET is_archived = 1, date_archived = NOW(), cell_num = NULL WHERE product_id = ?";
        $stmt = $conne->prepare($archive_query);

        if ($stmt === false) {
            error_log("Failed to prepare statement: " . $conne->error);
            http_response_code(500);
            echo "Error preparing query.";
            exit();
        }

        $stmt->bind_param('i', $product_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {

                echo "Product archived successfully!";
            } else {
                http_response_code(404);
                echo "Archiving failed.";
            }
        } else {
            error_log("Failed to execute statement: " . $stmt->error);
            http_response_code(500);
            echo "Error deleting product.";
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo "Product ID not provided.";
    }
} else {
    http_response_code(405);
    echo "Invalid request method.";
}

$conne->close();
exit();
