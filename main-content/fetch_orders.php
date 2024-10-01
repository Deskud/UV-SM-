<?php
require "../dbconnection.php"; // Include database connection
include '../session_check.php';

// Check connection
if ($conne->connect_error) {
    die("Connection failed: " . $conne->connect_error);
}

// Fetch pending orders

$orders = [];
$result = $conne->query("SELECT * FROM orders WHERE status = 'pending'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
// Fetch all items for pending orders
$orderIds = array_column($orders, 'order_id');
$itemsQuery = "SELECT * FROM items WHERE order_id IN (" . implode(',', $orderIds) . ")";
$itemsResult = $conne->query($itemsQuery);

// Group items by order_id
$groupedItems = [];
while ($item = $itemsResult->fetch_assoc()) {
    $groupedItems[$item['order_id']][] = $item;
}
?>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Student ID</th>
            <th>Order Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr id="order-row-<?php echo $order['order_id']; ?>">
                <td><?php echo $order['order_id']; ?></td>
                <td><?php echo $order['student_id']; ?></td>
                <td><?php echo $order['order_date']; ?></td>
                <td>
                    <button class="proceed-btn" onclick="openModal(<?php echo $order['order_id']; ?>)">
                        Proceed
                    </button>
                </td>
            </tr>

            <!-- Order Modal -->
            <div id="orderModal<?php echo $order['order_id']; ?>" class="modal">
                <div class="modal-order-content">
                    <span class="close-modal" onclick="closeModal(<?php echo $order['order_id']; ?>)">
                        <i id="close-icon" class="fa-solid fa-xmark"></i>
                    </span>
                    <h2>Order Details (ID: <?php echo $order['order_id']; ?>)</h2>
                    <h3>Student ID: <?php echo $order['student_id']; ?></h3>
                    <h3>Order Date: <?php echo $order['order_date']; ?></h3>
                    <h3>Items:</h3>
                    <ul>
                        <?php
                        if (isset($groupedItems[$order['order_id']])) {
                            foreach ($groupedItems[$order['order_id']] as $item) {
                                echo "<h4>Item ID: {$item['item_id']} - Quantity: {$item['quantity']}</h4>";
                            }
                        } else {
                            echo "<h4>No items found for this order.</h4>";
                        }
                        ?>
                    </ul>
                    <form id="order-form-<?php echo $order['order_id']; ?>" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        <button type="button" class="submit-update-order" onclick="openUpdateModal(<?php echo $order['order_id']; ?>)">Update</button>
                        <button type="button" class="submit-finish-order" onclick="finishOrder(<?php echo $order['order_id']; ?>)">Finish</button>
                    </form>
                </div>
            </div>

            <!-- QR Code Modal -->
            <div id="qrCodeModal<?php echo $order['order_id']; ?>" class="modal">
                <div class="modal-receipt-content">
                    <?php
                    echo "<p>-------------------------------------------------------</p>
                                <h4>Philippine Christian University - Dasmariñas</h4>
                    <p>-------------------------------------------------------</p>
                       <p>PCU College Building, Dasmariñas, 4114 Cavite</p>
                    <p>-------------------------------------------------------</p>";
                    echo "<h5>Order ID:{$order['order_id']}</h5>";
                    if (isset($groupedItems[$order['order_id']])) {
                        foreach ($groupedItems[$order['order_id']] as $item) {
                            echo "<h5>Item ID: {$item['item_id']}--x{$item['quantity']}</h5>";
                        }
                    }
                    ?>
                    <h5>-------------------------Thank you!-------------------------</h5>
                    <div id="qr-code-display<?php echo $order['order_id']; ?>">
                        <!-- QR code will be displayed here -->
                    </div>
                    <button class="print" onclick="printQRCode(<?php echo $order['order_id']; ?>)">Print QR Code</button>
                </div>
            </div>

            <!-- Update Modal -->
            <div id="updateModal<?php echo $order['order_id']; ?>" class="modal">
                <div class="modal-order-content">
                    <span class="close-modal" onclick="closeUpdateModal(<?php echo $order['order_id']; ?>)">
                        <i id="close-icon" class="fa-solid fa-xmark"></i>
                    </span>
                    <h3 class="title-form">Update Order</h3>
                    <h5>Update Quantity for Order ID: <?php echo $order['order_id']; ?></h5>

                    <form class="update-items" id="update-quantity-form-<?php echo $order['order_id']; ?>" method="POST">
                        <ul>
                        <?php
                            if (isset($groupedItems[$order['order_id']])) {
                                foreach ($groupedItems[$order['order_id']] as $item) {
                                    echo "<h4>Item ID: " . $item['item_id'] . " - Quantity: <input type='number' class='quantity-input' data-item-id='" . $item['item_id'] . "' value='" . $item['quantity'] . "'></h4>";
                                }
                            }
                            ?>
                        </ul>
                        <button type="button" class="update-order" onclick="updateOrder(<?php echo $order['order_id']; ?>)">Update Changes</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" src="modal.js"></script>