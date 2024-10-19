<?php
require "../dbconnection.php";
include '../session_check.php';

$query = "SELECT * FROM transactions";
$display_purchase_history = mysqli_query($conne, $query);
?>

<h3 class="title-form">Purchase History</h3>
<hr>

<div class="download-purchase-history">
    <input type="button" class="download-data" value="Export .csv" onclick="window.location.href='download_data.php'">
</div>

<div class="transactions-table-container">
    <table id="transactions-table">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Order ID</th>
                <th>Total Quantity</th>
                <th>Total Amount</th>
                <th>Transaction Date</th>
                <th>QR Code</th>
                <th>Quantity Dispensed</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($display_purchase_history)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['qr_code']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity_dispensed']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    
    function purchasePoll() {
        $.ajax({
            url: './server/purchasehistory_poll.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#transactions-table tbody').empty();

                // Append new rows with updated transaction data
                response.forEach(function(row) {
                    $('#transactions-table tbody').append(
                        `<tr>
                            <td>${row.transaction_id}</td>
                            <td>${row.order_id}</td>
                            <td>${row.total_quantity}</td>
                            <td>${row.total_amount}</td>
                            <td>${row.transaction_date}</td>
                            <td>${row.qr_code}</td>
                            <td>${row.quantity_dispensed}</td>
                            <td>${row.status}</td>
                        </tr>`
                    );
                });
            },
            error: function(xhr, status, error) {
                console.error("Failed to fetch transactions:", error);
            }
        });
    }

    setInterval(purchasePoll, 3000);
</script>