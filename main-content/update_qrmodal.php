<?php
require "../dbconnection.php";

header('Content-Type: application/json'); // Ensure JSON is returned

$response = array('success' => false, 'items' => array());

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    $query = "SELECT p.product_name, i.quantity
    FROM items i
    LEFT JOIN products p ON i.product_id = p.product_id
    WHERE order_id = ?";

    //Fixed the undefined problem with qr modal
    $stmt = $conne->prepare($query);

    if ($stmt) {
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response['items'][] = array(
                'product_name' => $row['product_name'],
                'quantity' => $row['quantity']
            );
        }

        $response['success'] = true;
    } else {
        $response['error'] = 'SQL error: ' . $conne->error;
    }
} else {
    $response['error'] = 'Order ID is missing.';
}

echo json_encode($response);
