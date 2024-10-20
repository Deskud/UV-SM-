<?php

require "../dbconnection.php";
include '../session_check.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transact_id = $_POST['transaction_id'];
    $transact_total_quantity = $_POST['total-quantity'];
    $transact_total_amount = $_POST['total-amount'];
    $transact_status = $_POST['status'];
    $transact_quantity_dispensed = $_POST['quantity-dispensed'];


    // Kunin previous tada
    $stmt = $conne->prepare("
        SELECT total_quantity, total_amount, quantity_dispensed 
        FROM transactions 
        WHERE transaction_id = ?");
    $stmt->bind_param('i', $transact_id);
    $stmt->execute();
    $stmt->bind_result($prev_total_quantity, $prev_total_amount, $prev_quantity_dispensed);
    $stmt->fetch();
    $stmt->close();

    
    // Prepare data
    $update_transactions = "
        UPDATE transactions 
        SET total_quantity = ?, total_amount = ?, status = ?, quantity_dispensed = ? 
        WHERE transaction_id = ?";

    

    $stmt = mysqli_prepare($conne, $update_transactions);

    if ($stmt) {
        // Bind the parameters to the placeholders
        mysqli_stmt_bind_param($stmt, 'idsii', $transact_total_quantity, $transact_total_amount, $transact_status, $transact_quantity_dispensed, $transact_id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "Transaction updated.";

            $modification_reason = "Updated transaction details";
            $modified_by = $_SESSION['user_id']; 
            $modification_timestamp = date('Y-m-d H:i:s');

            $insert_modification = "
                INSERT INTO transaction_modifications 
                (transaction_id, prev_total_quantity, new_total_quantity, prev_total_amount, new_total_amount, prev_quantity_dispensed, new_quantity_dispensed, modification_reason, modified_by, modification_timestamp)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conne->prepare($insert_modification);
            if ($stmt) {
                $stmt->bind_param('iddddiisis', $transact_id, $prev_total_quantity, $transact_total_quantity, $prev_total_amount, $transact_total_amount, $prev_quantity_dispensed, $transact_quantity_dispensed, $modification_reason, $modified_by, $modification_timestamp);
                $stmt->execute();
                $stmt->close();
                echo "Modification logged.";
            } else {
                echo "Error preparing modification statement: " . mysqli_error($conne);
            }

        } else {
            echo "Error updating transaction: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conne);
    }
}


