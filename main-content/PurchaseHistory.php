<?php
require "../dbconnection.php"; // Include database connection
include '../session_check.php';

$query = "SELECT * FROM transactions";

$display_purchase_history = mysqli_query($conne, $query);

?>

<h3 class="title-form">Purchase History</h3>
<hr>

<div class="download-purchase-history">

    <input type="button" class="download-data" value="Export .csv" onclick="window.location.href='download_data.php'">
    </form>
</div>
<div class="transactions-table-container">
    <table>
        <tr>
            <th>Transaction ID</th>
            <th>Order ID</th>
            <th>Total Quantity</th>
            <th>Total Amount</th>
            <th>Transaction Date</th>
            <th>QR Code</th>
            <th>Status</th>
        </tr>
        <tr>
            <?php while ($row = mysqli_fetch_assoc($display_purchase_history)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                <td><?php echo htmlspecialchars($row['qr_code']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>

            </tr>
            <?php
            }
            ?>
    </table>
</div>