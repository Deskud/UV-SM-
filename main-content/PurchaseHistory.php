<?php
require "../dbconnection.php"; // Include database connection
include '../session_check.php';

$query = "SELECT * FROM transactions";

$display_purchase_history = mysqli_query($conne, $query);

?>

<h3 class="title-form">Purchase History</h3>
<hr>

<div class="download-purchase-history">

        <input type="button" class="download-data" value="Download .csv" onclick="window.location.href='download_data.php'">
    </form>
</div>
<div class="transactions-table-container">
    <table>
        <tr>
            <th>transaction_ID</th>
            <th>order_ID</th>
            <th>user_ID</th>
            <th>total_quantity</th>
            <th>total_amount</th>
            <th>transaction_date</th>
            <th>qr_code</th>
        </tr>
        <tr>
            <?php while ($row = mysqli_fetch_assoc($display_purchase_history)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
            <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
            <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
            <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
            <td><?php echo htmlspecialchars($row['qr_code']); ?></td>

        </tr>
    <?php
            }
    ?>
    </table>
</div>