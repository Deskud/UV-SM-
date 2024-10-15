<?php
require "../dbconnection.php"; // Include database connection
include '../session_check.php';

// Check connection
if ($conne->connect_error) {
    die("Connection failed: " . $conne->connect_error);
}

// Fetch pending orders

$orders = [];
$result = $conne->query("SELECT * FROM orders WHERE status = 'pending' OR status = 'processing'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
// Fetch all items for pending orders
$orderIds = array_column($orders, 'order_id');
$groupedItems = [];
if (!empty($orderIds)) {
    $itemsQuery = "
    SELECT i.*, p.product_name 
    FROM items i
    JOIN products p ON i.product_id = p.product_id
    WHERE i.order_id IN (" . implode(',', $orderIds) . ")";
    $itemsResult = $conne->query($itemsQuery);

    // Group items by order_id
    while ($item = $itemsResult->fetch_assoc()) {
        $groupedItems[$item['order_id']][] = $item;
    }
}
?>

<table class="order-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr id="order-row-<?php echo $order['order_id']; ?>">
                <td><?php echo $order['order_id']; ?></td>
                <td><?php echo $order['order_date']; ?></td>
                <td><?php echo $order['status']; ?></td>
                <td>
                    <div class="prc-btn-container">
                        <button class="proceed-btn" id="proceed-btn-<?php echo $order['order_id']; ?>" onclick="openModal(<?php echo $order['order_id']; ?>)">
                            Proceed
                        </button>
                    </div>
                </td>
            </tr>

        <?php endforeach; ?>
    </tbody>
</table>





<!-- MODALS END -->
<!-- ------------------------------------------------------------------------------------------------------------------------------------------------------------------ -->



<script type="text/javascript" src="modal.js"></script>