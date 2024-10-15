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

<?php foreach ($orders as $order): ?>

    <div id="orderModal<?php echo $order['order_id']; ?>" class="modal">
        <div class="modal-order-content">
            <span class="close-modal" onclick="closeModal(<?php echo $order['order_id']; ?>)">
                <i id="close-icon" class="fa-solid fa-xmark"></i>
            </span>
            <div class="order-details">
                <h2 style="color: black;">Order Details ID: <?php echo $order['order_id']; ?></h2>
                <h3>Order Date: <?php echo $order['order_date']; ?></h3>
                <h3>Student Number: <input type="text" name="student-id"></h3>
                <h3>Items:</h3>
                <ul>
                    <?php
                    if (isset($groupedItems[$order['order_id']])) {
                        foreach ($groupedItems[$order['order_id']] as $item) {
                            echo "<h6 class='order-items'>{$item['product_name']} - Quantity:{$item['quantity']} </h6>";
                        }
                    } else {
                        echo "<h4>No items found for this order.</h4>";
                    }
                    ?>
                </ul>
            </div>
            <div class="order-modal-btn">
                <form id="order-form-<?php echo $order['order_id']; ?>" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <button type="button" class="submit-update-order" onclick="openUpdateModal(<?php echo $order['order_id']; ?>)">Update</button>
                    <button type="button" class="submit-finish-order" onclick="finishOrder(<?php echo $order['order_id']; ?>)">Finish</button>
                </form>
            </div>
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
            echo "<h6>Student Number:<span id='qr-student-id-{$order['order_id']}'></span></h6>";
            if (isset($groupedItems[$order['order_id']])) {
                foreach ($groupedItems[$order['order_id']] as $item) {
                    echo "<h6>Item: {$item['product_name']}......x {$item['quantity']}</h6>";
                }
            }
            ?>
            <h6>------------------------------------------------------------</h6>
            <div id="qr-code-display<?php echo $order['order_id']; ?>">
                <!-- QR code will be displayed here -->
            </div>
            <button id="print" class="print" onclick="printQRCode(<?php echo $order['order_id']; ?>)">Print QR Code</button>
        </div>
    </div>

    <!--New  Update Modal -->
    <div id="updateModal<?php echo $order['order_id']; ?>" class="modal">
        <div class="modal-order-content">
            <span class="close-modal" onclick="closeUpdateModal(<?php echo $order['order_id']; ?>)">
                <i id="return-icon" class="fa-solid fa-arrow-left"></i>
            </span>
            <h3 class="title-form">Update Order</h3>
            <h5 style="text-align: center;">Update Quantity for Order ID: <?php echo $order['order_id']; ?></h5>

            <form class="update-items" id="update-quantity-form-<?php echo $order['order_id']; ?>" method="POST">
                <div class="update-details">
                    <ul>
                        <?php
                        if (isset($groupedItems[$order['order_id']])) {
                            foreach ($groupedItems[$order['order_id']] as $item) {
                                echo "
                        <li id='item-id-" . $item['item_id'] . "'>
                            <h6 style='text-align: center;'>
                                " . $item['product_name'] . " x 
                                <input type='number' class='quantity-input' 
                                       data-item-id='" . $item['item_id'] . "' 
                                       value='" . $item['quantity'] . "' min='0'>
                                <button type='button' class='remove-item' 
                                        data-item-id='" . $item['item_id'] . "' 
                                        data-order-id='" . $order['order_id'] . "'> X </button>
                            </h6>
                        </li>";
                            }
                        }
                        ?>
                    </ul>
                </div>
                <div class="order-upd-container">
                    <button type="button" class="update-order" onclick="updateOrder(<?php echo $order['order_id']; ?>)">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<!-- FUCKING TABLES AND STUFF -->
<h3 class="title-form">Orders</h3>
<hr>
<div>
    <div class="order-search-bar">
        <!-- Search Bar -->
        <input id="find-pending-orders" class="search-table" type="search" placeholder="Search...">
    </div>


    <table id="search-order-table" class="search-result-table"></table>
    <!-- Dito mag di-display yung table sa sinearch na order. -->

    <div id="order-table-container">

    </div>

    <!-- Dito mag di-display order table -->


    <div id=confirmation-modal class="modal">
        <div class="modal-confirm-content">
            <h3 class="title-form">Confirm Action</h3>
            <p>Are you sure you want to proceed to the QR generation?
                This action cannot be undone.</p>
            <button type="button" id="confirm-print" class="confirm-btn">Yes</button>
            <button type="button" id="cancel-print" class="cancel-btn">No</button>
        </div>
    </div>

    <!-- SCRIPT START -->
    <script type="text/javascript" src="modal.js"></script>
    <script>
        // mag lo-load dapat yung mga data sa table ng orders
        loadOrders();


        // ito pang search lang ng mga orders
        $('#find-pending-orders').keyup(function() {
            var orderinput = $(this).val();
            if (orderinput != '') {

                $.ajax({

                    url: './main-content/search_result_Orders.php',
                    method: 'POST',
                    data: {

                        orderinput: orderinput

                    },
                    success: function(data) {

                        $('#search-order-table').html(data);
                        $('#search-order-table').css('display', 'block');
                    }

                });
            } else {
                $('#search-order-table').css('display', 'none');
            }
        });


        // Open Modal Function
        function openModal(orderId) {
            const proceedButton = document.querySelector(`#proceed-btn-${orderId}`);
            if (proceedButton) {
                proceedButton.disabled = true;
            }

            // Open the modal window
            document.getElementById('orderModal' + orderId).style.display = "block";

            $.ajax({
                url: './main-content/order_status.php',
                type: 'POST',
                data: {
                    order_id: orderId
                },
                success: function(response) {
                    document.getElementById(`order-row-${orderId}`).cells[2].innerText = response;

                },
                error: function() {
                    alert('Error processing the order.');
                }
            });
        }



        // Close modal function
        function closeModal(orderId) {
            document.getElementById('orderModal' + orderId).style.display = "none";

            $.ajax({
                url: './main-content/order_status.php',
                type: 'POST',
                data: {
                    order_id: orderId
                },
                success: function(response) {
                    document.getElementById(`order-row-${orderId}`).cells[2].innerText = response;

                    // Re-enable the "Proceed" button
                    const proceedButton = document.querySelector(`#proceed-btn-${orderId}`);
                    if (proceedButton) {
                        proceedButton.disabled = false; // Re-enable the button
                    }
                },
                error: function() {
                    alert('Error reverting the order status.');
                }
            });
        }


        // Open update modal
        function openUpdateModal(orderId) {
            document.getElementById('updateModal' + orderId).style.display = "block";

        }

        // Close update modal
        function closeUpdateModal(orderId) {
            document.getElementById('updateModal' + orderId).style.display = "none";

        }

        // Open QR code modal
        function openQRModal(orderId) {
            var studentId = $('#orderModal' + orderId + ' input[name="student-id"]').val();
            $('#qr-student-id-' + orderId).text(studentId);

            document.getElementById('qrCodeModal' + orderId).style.display = "block";


        }

        // Close QR code modal
        function closeQRModal(orderId) {
            document.getElementById('qrCodeModal' + orderId).style.display = "none";
        }




        function updateOrder(orderId) {
            var quantities = {};
            var productNames = {}; // New object to hold product names

            $('#updateModal' + orderId + ' .quantity-input').each(function() {
                var itemId = $(this).data('item-id');
                var quantity = $(this).val();
                var productName = $(this).closest('li').find('h6').text().split(' x ')[0];

                if (quantity) {
                    quantities[itemId] = quantity;
                    productNames[itemId] = productName;
                }
            });

            console.log(quantities);
            console.log(productNames);

            $.ajax({
                url: './main-content/orders_function.php',
                type: 'POST',
                data: {
                    action: 'update',
                    order_id: orderId,
                    quantities: quantities,
                    product_names: productNames
                },
                success: function(response) {
                    console.log(response);
                    loadOrderDetails(orderId);
                    $('#updateModal' + orderId).css('display', 'none');
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        }




        // Finish order function
        function finishOrder(orderId) {
            console.log("Order Id:", orderId); // Debugging

            $('#confirmation-modal').css('display', 'block');

            $(document).on('click', '#confirm-print', function() {
                $('#confirmation-modal').css('display', 'none');
                $.ajax({
                    url: './main-content/orders_function.php',
                    method: 'POST',
                    data: {
                        action: 'finish',
                        order_id: orderId
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("AJAX request successful. Response:", response); // Debugging

                        if (response.qrcode) {
                            console.log("QR Code generated successfully for orderId:", orderId); // Debugging
                            openQRModal(orderId);

                            // Ensure element exists before trying to insert HTML
                            var qrDisplay = document.getElementById('qr-code-display' + orderId);
                            if (qrDisplay) {
                                qrDisplay.innerHTML = "<img src='./main-content/" + response.qrcode + "' alt='QR Code' />";


                            } else {
                                console.error("QR code display element not found for orderId:", orderId);
                            }
                        } else {
                            console.error("Error in response: ", response.error); // Debugging
                            alert(response.error || 'Failed to generate QR code');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX request failed. Status:", textStatus, "Error:", errorThrown);
                        console.log("Response Text:", jqXHR.responseText); // Log response from the server
                        alert('Error processing the request.');
                    }
                });
            });
        }

        $(document).ready(function() {
            function removeItem(itemId, orderId, itemElement) {
                $.ajax({
                    url: './main-content/orders_function.php',
                    type: 'POST',
                    data: {
                        action: 'remove',
                        item_id: itemId,
                        order_id: orderId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove the item from the order list
                            itemElement.remove();

                            $('#item-id-' + itemId).remove(); // Adjusted to match the correct ID format

                            loadOrderDetails(orderId);
                        } else {
                            // Display error message
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.'); // Handle AJAX error
                    }
                });
            }

            $(document).on('click', '.remove-item', function(event) {
                var itemId = $(this).data('item-id');
                var orderId = $(this).data('order-id');
                var itemElement = $('#item-id-' + itemId);

                removeItem(itemId, orderId, itemElement);
            });
        });



        // Print QR code function
        function printQRCode(orderId) {
            var qrCodeImg = document.getElementById('qr-code-display' + orderId).innerHTML;
            print(qrCodeImg);
            $('#qrCodeModal' + orderId).css('display', 'none');

        }


        function loadOrderDetails(orderId) {
            $.ajax({
                url: './main-content/update_order_modal.php',
                type: 'GET',
                data: {
                    order_id: orderId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let itemList = '';
                        $.each(response.items, function(index, item) {
                            itemList += `<h6>${item.product_name} - Quantity: ${item.quantity}</h6>`;
                        });
                        // Insert the new item list into the modal
                        $('#orderModal' + orderId + ' ul').html(itemList);
                    } else {
                        $('#orderModal' + orderId + ' ul').html('<h4>No items found for this order.</h4>');
                    }
                },
                error: function() {
                    alert('Error retrieving order details.');
                }
            });
        }

        function loadOrders() {
            $.ajax({
                url: './main-content/fetch_orders.php',
                method: 'GET',
                success: function(data) {
                    $('#order-table-container').html(data);
                },
                error: function() {
                    alert('Failed to load orders.');
                }
            });
        }

        function pollOrders() {
            $.ajax({
                url: './server/orders_poll.php',
                type: 'GET',
                dataType: 'json',
                success: function(orders) {
                    let shouldReload = false;

                    orders.forEach(order => {
                        const row = document.getElementById(`order-row-${order.order_id}`);
                        if (row) {
                            row.cells[2].innerText = order.status; // Update status

                            // If yung status ay completed tatawagin ang loadOrders function 
                            //para ma reload yung table para sa bagong update
                            if (order.status === 'completed') {
                                shouldReload = true;
                            }
                        }
                    });

                    // If any order has a status of "Completed," reload the orders table
                    if (shouldReload) {
                        loadOrders();
                    }
                },
                error: function() {
                    console.error('Error fetching orders.');
                }
            });
        }

        setInterval(pollOrders, 3000);
        setInterval(loadOrders, 3000);




        // Polling function to load orders and update the table
        // function pollOrders() {
        //     $.ajax({
        //         url: './server/orders_poll.php', // The PHP script to fetch orders
        //         method: 'GET',
        //         success: function(data) {
        //             $('#order-table-container tbody').html(data); // Replace the table body with new rows
        //         },
        //         error: function() {
        //             alert('Failed to load orders.');
        //         }
        //     });

        //     setTimeout(pollOrders, 5000);
        // }
    </script>