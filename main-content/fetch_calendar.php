<?php
require "../dbconnection.php";

$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

$response = ['success' => false, 'transactions' => []];

if ($startDate && $endDate) {
    // Fetch transactions for the date range
    $query = "SELECT * FROM transactions WHERE DATE(transaction_date) BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($conne, $query);

    $transactions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dateKey = date('Y-m-d', strtotime($row['transaction_date']));
        if (!isset($transactions[$dateKey])) {
            $transactions[$dateKey] = [];
        }
        $transactions[$dateKey][] = $row;
    }

    if (!empty($transactions)) {
        $response['success'] = true;
        $response['transactions'] = $transactions;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
