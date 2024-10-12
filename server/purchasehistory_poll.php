<?php
require "../dbconnection.php"; 
include '../session_check.php';

$query = "SELECT * FROM transactions";

$display_purchase_history = mysqli_query($conne, $query);

while($row = mysqli_fetch_assoc($display_purchase_history)){
echo"<td>{$row['transaction_id']}</td>";
echo"<td>{$row['order_id']}</td>";
echo"<td>{$row['total_quantity']}</td>";
echo"<td>{$row['transaction_id']}</td>";
echo"<td>{$row['transaction_id']}</td>";
echo"<td>{$row['transaction_id']}</td>";
echo"<td>{$row['transaction_id']}</td>";
}

echo json_encode($display_purchase_history);
?>