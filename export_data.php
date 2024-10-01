<?php
require('dbconnection.php');
header('Content-Type: text/csv; charset=utf-8');

$filename = 'data_export_' . date('Ymd') . '.csv';

header('Content-Disposition: attachment; filename='.$filename);

$output = fopen('php://output', 'w');

fputcsv($output, array('Transaction_ID', 'Order_ID', 'User_ID', 'Total_Quantity', 'Total Amount', 'Transaction_Date', 'QR_Code', 'Status'));

$query = "SELECT * FROM transactions ";

$export_result = $conne->query($query);

while ($row = $export_result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;