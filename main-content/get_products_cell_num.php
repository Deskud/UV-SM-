<?php
require "../dbconnection.php";

// Query to get the cell numbers that are already in use
$query = "SELECT cell_num FROM products";
$result = $conne->query($query);

$usedCells = [];
while ($row = $result->fetch_assoc()) {
    $usedCells[] = $row['cell_num'];
}

// Return the list of used cell numbers as JSON
echo json_encode($usedCells);
?>