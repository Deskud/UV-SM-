<?php

require "../dbconnection.php";
include '../session_check.php';

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Increment one date because of MySQL handles date comparison.
    //It does not precisely pick the date range. 

    $endDatePlusOne = date('Y-m-d', strtotime($endDate . ' +1 day'));

    $query = " SELECT
    t.transaction_id,
    s.student_no,
    t.total_amount,
    t.total_quantity,
    t.transaction_date,
    t.quantity_dispensed,
    t.qr_code,
    t.status,
    u.last_name
    FROM transactions t
    LEFT JOIN students s ON t.student_id = s.student_id
    LEFT JOIN users u ON t.user_id = u.user_id
    WHERE DATE (t.transaction_date) >= '$startDate' 
    AND DATE (t.transaction_date) < '$endDatePlusOne'";

    $result = $conne->query($query);

    $transactions = array();
  

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
    }

    // Return the data as JSON
    echo json_encode(array('transactions' => $transactions));
}
