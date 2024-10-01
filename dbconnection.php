<?php
$servername = "localhost";  // Usually 'localhost' when using XAMPP
$username = "root";         // Default username for XAMPP
$password = "";             // Default password is empty for XAMPP
$dbname = "uvm_db";  // Replace with your actual database name

// Create connection
$conne = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conne->connect_error) {
    die("Connection failed: " . $conne->connect_error);
}
?>
