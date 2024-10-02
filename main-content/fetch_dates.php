<?php
include './dbconnection.php';

// Handle GET request to fetch transactions for a given month and year
if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];

    // Fetch transactions for the specified month and year
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?");
    $stmt->execute([$month, $year]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare the transactions data
    $transactionData = [];
    foreach ($transactions as $transaction) {
        $dateParts = explode('-', $transaction['transaction_date']);
        $transactionData[] = [
            'transaction_name' => $transaction['transaction_name'],
            'transaction_amount' => $transaction['transaction_amount'],
            'day' => (int)$dateParts[2], // Extract the day part
            'month' => (int)$dateParts[1], // Extract the month part
            'year' => (int)$dateParts[0]  // Extract the year part
        ];
    }

    // Return the data as JSON
    echo json_encode($transactionData);
}
?>
