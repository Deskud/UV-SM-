<?php
require "../dbconnection.php";

// Unused but will not remove...for now.

$query = "SELECT * FROM transactions";
$result = mysqli_query($conne, $query);

$transactions = [];

while ($row = mysqli_fetch_assoc($result)) {
    $transactions[] = $row;
}

echo json_encode($transactions);
?>
