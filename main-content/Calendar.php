<?php
require "../dbconnection.php";

$query = "SELECT*FROM transactions";

$displayDates = mysqli_query($conne, $query);

?>

<h2 style="text-align: center;">Calendar of Transactions</h2>

<div>
    <input id="dateTrans" type="date">
    <input type="submit" value="View...">
</div>
<div>
    <table>
        <thead>

            <th class="text-center">Date</th>
            <th class="text-center">Order_ID</th>
            <th class="text-center">User_ID</th>
            <th class="text-center">Total Amount</th>

        </thead>
        <tbody>
            <tr>
                <?php while ($row = mysqli_fetch_assoc($displayDates)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
            </tr>
        <?php
                }
        ?>
        </tr>
        </tbody>
    </table>
</div>