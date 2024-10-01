<?php
session_start();

// Define roles
define('ADMIN', 1);
define('CASHIER', 2);

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Check user role and redirect if unauthorized
function checkAccess($requiredRole) {
    if (!isset($_SESSION['access_id']) || $_SESSION['access_id'] !== $requiredRole) {
        header("Location: ../unauthorized.html"); // Redirect to an unauthorized page or error page
        exit();
    }
}
?>
