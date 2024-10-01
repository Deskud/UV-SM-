<?php
include '../dbconnection.php';

$query = "SELECT COUNT(*) as count FROM transactions "; //Pag may bagong nadagdaga may lalabas na notif
$result = $conne->query($query);
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    echo json_encode(['newTransaction' => true, 'message' => 'Transaction Recorded.']);
} else {
    echo json_encode(['newTransaction' => false]);
}

?>
