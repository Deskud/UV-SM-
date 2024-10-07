<?php
require "../dbconnection.php"; // Include database connection
include '../session_check.php';

?>
<h3 class="title-form">Orders</h3>
<hr>
<div>
    <div class="order-search-bar">
        <!-- Search Bar -->
        <input id="find-pending-orders" class="search-table" type="search" placeholder="Search...">
    </div>


    <table id="search-order-table" class="search-result-table"></table>
    <!-- Dito mag di-display yung table sa sinearch na order. -->

    <div id="order-table-container"></div>
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


    <script type="text/javascript" src="modal.js"></script>
    <script>
        $(document).ready(function() {
            loadOrders();
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
        });
        // Open modal function
        function openModal(orderId) {
            // Disable the button for other users while the modal is open
            document.querySelector(`#proceed-btn-${orderId}`).disabled = true;

            // Show the modal window
            document.getElementById('orderModal' + orderId).style.display = "block";

            // Make an AJAX call to update the order status to 'processed'
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
                    document.querySelector(`#proceed-btn-${orderId}`).disabled = false;
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
            document.getElementById('qrCodeModal' + orderId).style.display = "block";
        }

        // Close QR code modal
        function closeQRModal(orderId) {
            document.getElementById('qrCodeModal' + orderId).style.display = "none";
        }


        // function updateOrder(orderId) {

        //     var quantities = {};

        //     // Use a selector that targets only the inputs for the specific order
        //     $('#updateModal' + orderId + ' .quantity-input').each(function() {
        //         var itemId = $(this).data('item-id'); // Get item ID
        //         var quantity = $(this).val(); // Get updated quantity

        //         // Only add to quantities if quantity is not empty or 0
        //         if (quantity) {
        //             quantities[itemId] = quantity; // Add to quantities object
        //         }
        //     });
        //     console.log(quantities);

        //     $.ajax({
        //         url: './main-content/orders_function.php', // URL to your PHP script
        //         type: 'POST',
        //         data: {
        //             action: 'update', // Specify action
        //             order_id: orderId, // Send the order ID
        //             quantities: quantities // Send the quantities as an object
        //         },
        //         success: function(response) {
        //             loadOrders()
        //             console.log(response);



        //         },
        //         error: function(xhr, status, error) {
        //             alert('Error: ' + error); // Show error message
        //         }
        //     });
        // }

        function updateOrder(orderId) {
            var quantities = {};

            // Gather all updated quantities from the modal
            $('#updateModal' + orderId + ' .quantity-input').each(function() {
                var itemId = $(this).data('item-id');
                var quantity = $(this).val();

                if (quantity) {
                    quantities[itemId] = quantity;
                }
            });

            console.log(quantities);

            $.ajax({
                url: './main-content/orders_function.php',
                type: 'POST',
                data: {
                    action: 'update',
                    order_id: orderId,
                    quantities: quantities
                },
                success: function(response) {
                    console.log(response);

                    loadOrderDetails(orderId);

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
            // Function to remove an item from the order
            function removeItem(itemId, orderId, itemElement) {
                $.ajax({
                    url: './main-content/orders_function.php', // Correct path to your orders_function.php
                    type: 'POST',
                    data: {
                        action: 'remove', // Call the 'remove' case in orders_function.php
                        item_id: itemId,
                        order_id: orderId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove the item from the DOM
                            itemElement.remove();
                            loadOrders();
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

            $(document).on('click', '.remove-item', function() {
                var itemId = $(this).data('item-id');
                var orderId = $(this).data('order-id');
                var itemElement = $('#item-' + itemId); // Use the ID to find the correct item in the DOM

                removeItem(itemId, orderId, itemElement);
            });
        });


        // Print QR code function
        function printQRCode(orderId) {
            var qrCodeImg = document.getElementById('qr-code-display' + orderId).innerHTML;
            print(qrCodeImg);
            loadOrders();

        }


        function loadOrderDetails(orderId) {
            $.ajax({
                url: './main-content/update_order_modal.php', // Your PHP script to fetch order details
                type: 'GET',
                data: {
                    order_id: orderId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let itemList = '';
                        $.each(response.items, function(index, item) {
                            itemList += `<h6>Item ID: ${item.item_id} - Quantity: ${item.quantity}</h6>`;
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


        // Function to fetch and display orders
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
    </script>