<?php
require "../dbconnection.php";
// Check if there's a filter date in the request
$filterDate = isset($_GET['filter_date']) ? $_GET['filter_date'] : null;

$response = ['success' => false, 'transactions' => []];

if ($filterDate) {
    // Fetch transactions for the filtered date
    $query = "SELECT transaction_id, transaction_date FROM transactions WHERE DATE(transaction_date) = '" . $filterDate . "'";
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

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
