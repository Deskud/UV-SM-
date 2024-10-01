<?php
require "../dbconnection.php";

// Get data sent from Arduino
$student_no = $_POST['student_no'];
$product_ids = [];
$quantities = [];

// Extract the product IDs and quantities from POST data (up to 6 products)
for ($i = 1; $i <= 6; $i++) {
    if (isset($_POST['product_id' . $i]) && isset($_POST['quantity' . $i])) {
        $product_ids[] = $_POST['product_id' . $i];
        $quantities[] = $_POST['quantity' . $i];
    }
}

// Check if the student exists
$checkStudentSQL = "SELECT student_id FROM students WHERE student_no = ?";
$stmt = $conne->prepare($checkStudentSQL);
$stmt->bind_param("s", $student_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Student exists, get student_id
    $student = $result->fetch_assoc();
    $student_id = $student['student_id'];
} else {
    // Insert new student
    $insertStudentSQL = "INSERT INTO students (student_no) VALUES (?)";
    $stmt = $conne->prepare($insertStudentSQL);
    $stmt->bind_param("s", $student_no);
    $stmt->execute();
    $student_id = $conne->insert_id;
}

// Insert the order
$insertOrderSQL = "INSERT INTO orders (student_id, order_date, status) VALUES (?, NOW(), 'pending')";
$stmt = $conne->prepare($insertOrderSQL);
$stmt->bind_param("i", $student_id);

if ($stmt->execute()) {
    $order_id = $conne->insert_id;

    // Insert each product in the order_products table
    for ($i = 0; $i < count($product_ids); $i++) {
        $insertOrderProductSQL = "INSERT INTO items (order_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conne->prepare($insertOrderProductSQL);
        $stmt->bind_param("iii", $order_id, $product_ids[$i], $quantities[$i]);
        $stmt->execute();
    }
    echo "Order placed successfully!";
} else {
    echo "Failed to place order.";
}

$conne->close();
?>
