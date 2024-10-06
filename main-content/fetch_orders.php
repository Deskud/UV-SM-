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
$groupedItems = [];
if (!empty($orderIds)) {
    $itemsQuery = "
    SELECT i.*, im.modification_timestamp
    FROM items i
    LEFT JOIN (
        SELECT item_id, MAX(modification_timestamp) as modification_timestamp
        FROM item_modifications
        GROUP BY item_id
    ) im ON i.item_id = im.item_id
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
                        <button class="proceed-btn" onclick="openModal(<?php echo $order['order_id']; ?>)">
                            Proceed
                        </button>
                    </div>
                </td>
            </tr>

            <!-- Order Modal -->
            <div id="orderModal<?php echo $order['order_id']; ?>" class="modal">
                <div class="modal-order-content">
                    <span class="close-modal" onclick="closeModal(<?php echo $order['order_id']; ?>)">
                        <i id="close-icon" class="fa-solid fa-xmark"></i>
                    </span>
                    <h2 style="color: black;">Order Details ID: <?php echo $order['order_id']; ?></h2>
                    <h3>Order Date: <?php echo $order['order_date']; ?></h3>
                    <h3 class="date-modified">Date modified: <?php echo $order['updated_at']; ?></h3>
                    <h3>Items:</h3>
                    <ul>
                        <?php
                        if (isset($groupedItems[$order['order_id']])) {
                            foreach ($groupedItems[$order['order_id']] as $item) {
                                echo "<h6>Item ID: {$item['item_id']} - Quantity: {$item['quantity']} </h6>";
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
                    echo "<h6>------------------------------------------------------------</h6>
                            <h6>Philippine Christian University - Dasmariñas</h6>
                    <h6>------------------------------------------------------------</h6>
                    <h6>PCU College Building, Dasmariñas, 4114 Cavite</h6>
                   <h6>------------------------------------------------------------</h6>";
                    echo "<h6>Order ID:{$order['order_id']}</h6>";
                    if (isset($groupedItems[$order['order_id']])) {
                        foreach ($groupedItems[$order['order_id']] as $item) {
                            echo "<h6>Item ID: {$item['item_id']}......x {$item['quantity']}</h6>";
                        }
                    }
                    ?>
                    <h6>-------------------------Thank you!-------------------------</h6>
                    <div id="qr-code-display<?php echo $order['order_id']; ?>">
                        <!-- QR code will be displayed here -->
                    </div>
                    <button class="print" onclick="printQRCode(<?php echo $order['order_id']; ?>)">Print QR Code</button>
                </div>
            </div>


            <!--New  Update Modal -->
            <div id="updateModal<?php echo $order['order_id']; ?>" class="modal">
                <div class="modal-order-content">
                    <span class="close-modal" onclick="closeUpdateModal(<?php echo $order['order_id']; ?>)">
                        <i id="close-icon" class="fa-solid fa-xmark"></i>
                    </span>
                    <h3 class="title-form">Update Order</h3>
                    <h5 style="text-align: center;">Update Quantity for Order ID: <?php echo $order['order_id']; ?></h5>

                    <form class="update-items" id="update-quantity-form-<?php echo $order['order_id']; ?>" method="POST">
                        <ul>
                            <?php
                            if (isset($groupedItems[$order['order_id']])) {
                                foreach ($groupedItems[$order['order_id']] as $item) {
                                    echo "
                                    <li>
                                        <h6>
                                        Item ID: " . $item['item_id'] . " - Quantity: 
                                        <input type='number' class='quantity-input' 
                                        data-item-id='" . $item['item_id'] . "' 
                                        value='" . $item['quantity'] . "' min='0'>
                                          <button class='remove-item' 
                                            data-item-id='" . $item['item_id'] . "' 
                                            data-order-id='" . $order['order_id'] . "'>Remove</button>
                                        </h6>
                                    </li>";
                                }
                            }
                            ?>
                        </ul>
                        <div class="order-upd-container">
                            <button type="button" class="update-order" onclick="updateOrder(<?php echo $order['order_id']; ?>)">Update Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" src="modal.js"></script>