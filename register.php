<?php
include "dbconnection.php";

// Check if the form is submitted
if (isset($_POST['register'])) {
    $last_name = htmlspecialchars($_POST['last_name']);
    $first_name = htmlspecialchars($_POST['first_name']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $access_id = intval($_POST['access_id']);

    // Check for empty fields
    if (empty($last_name) || empty($first_name) || empty($email) || empty($username) || empty($password) || empty($access_id)) {
        echo '<script>alert("All fields are required.");</script>';
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Invalid email format.");</script>';
        exit();
    }

    // Password strength validation
    if (strlen($password) < 8 || !preg_match("/[0-9]/", $password) || !preg_match("/[A-Z]/", $password)) {
        echo '<script>alert("Password must be at least 8 characters long, contain at least one number and one uppercase letter."); window.location.href = "register.html";</script>';
        exit();
    }

    // Check if email or username already exists
    $check_sql = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $conne->prepare($check_sql);
    $stmt->bind_param('ss', $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo '<script>alert("Email or username already exists."); window.location.href = "register.html";</script>';
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $sql = "INSERT INTO users (last_name, first_name, email, username, access_id, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conne->prepare($sql);
    $stmt->bind_param('ssssis', $last_name, $first_name, $email, $username, $access_id, $hashed_password);

    if ($stmt->execute()) {
        echo '<script>alert("Registration successful!"); window.location.href = "login.html";</script>';
        exit();
    } else {
        echo '<script>alert("Error: Could not register user.");</script>';
    }

    $stmt->close();
    $conne->close();
}
?>