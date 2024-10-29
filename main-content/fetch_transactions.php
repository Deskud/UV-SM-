<?php
require "../dbconnection.php";
include '../session_check.php';

$query = " SELECT  t.*, s.student_no 
    FROM transactions t
    LEFT JOIN students s ON t.student_id = s.student_id";
$display_purchase_history = mysqli_query($conne, $query);

?>
<table id="transactions-table">
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Total Quantity</th>
            <th>Total Amount</th>
            <th>Transaction Date</th>
            <th>Student ID</th>
            <th>QR Code</th>
            <th>Quantity Dispensed</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <?php if (mysqli_num_rows($display_purchase_history)===0): ?>
        <tr>
            <td colspan="11" style="text-align: center;">
                <h1 style="color:black;">No transactions available</h1>
            </td>
        </tr>
    <?php else: ?>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($display_purchase_history)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['qr_code']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity_dispensed']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <a class="edit-transaction" data-id="<?php echo htmlspecialchars($row['transaction_id']); ?>"><i style="color: black;" class="fa-regular fa-pen-to-square fa-lg"></i></a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    <?php endif; ?>
</table>