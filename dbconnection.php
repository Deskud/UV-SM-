<?php
$servername = "localhost"; 
$username = "root";      
$password = "";           
$dbname = "uvm_db"; 

$conne = new mysqli($servername, $username, $password, $dbname);

// Check connction
if ($conne->connect_error) {
    die("connction failed: " . $conn->connect_error);
}
?>
