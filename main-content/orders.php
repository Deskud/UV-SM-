<?php
require "../dbconnection.php"; // Include database connection
include '../session_check.php';

?>
<h3 class="title-form">Orders</h3>
<hr>
<div>
    <!-- Search Bar -->
    <input id="find-pending-orders" class="search-table" type="search" placeholder="Search...">

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
            document.getElementById('orderModal' + orderId).style.display = "block";
        }

        // Close modal function
        function closeModal(orderId) {
            document.getElementById('orderModal' + orderId).style.display = "none";
            // closeUpdateModal(orderId); // Close update modal if open
            // closeQRModal(orderId); // Close QR modal if open

            // // Re-enable the proceed button when the modal is closed
            // document.getElementById('proceed-btn-' + orderId).disabled = false; // Re-enable the button
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


        function updateOrder(orderId) {

            var quantities = {};

            // Use a selector that targets only the inputs for the specific order
            $('#updateModal'+orderId +' .quantity-input').each(function() {
                var itemId = $(this).data('item-id'); // Get item ID
                var quantity = $(this).val(); // Get updated quantity

                // Only add to quantities if quantity is not empty or 0
                if (quantity) {
                    quantities[itemId] = quantity; // Add to quantities object
                }
            });
            console.log(quantities);

            $.ajax({
                url: './main-content/orders_function.php', // URL to your PHP script
                type: 'POST',
                data: {
                    action: 'update', // Specify action
                    order_id: orderId, // Send the order ID
                    quantities: quantities // Send the quantities as an object
                },
                success: function(response) {
                    loadOrders();



                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error); // Show error message
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

        // // Once pinendot ito ng user may lalabas dapat na notification 
        // $('#confirm-print').on('click', function() {

        //     checkForNewTransactions();
        // });


        // function checkForNewTransactions() {

        //     $.ajax({
        //         url: './main-content/transact_notif.php', // Endpoint to check for new transactions
        //         method: 'GET',
        //         dataType: 'json',
        //         success: function(data) {
        //             if (data.newTransaction) {
        //                 showNotification(data.message);
        //             }
        //         },
        //         error: function(jqXHR, textStatus, errorThrown) {
        //             console.error("Error fetching notifications:", textStatus, errorThrown);
        //         }
        //     });
        // }

        // function showNotification(message) {
        //     var notification = $('<div class="notification">' + message + '</div>');
        //     $('#notifcations-table').append(notification);

        //     setTimeout(function() {
        //         notification.fadeOut(300, function() {
        //             $(this).remove();
        //         });
        //     }, 60000);
        // }


        // Print QR code function
        function printQRCode(orderId) {
            var qrCodeImg = document.getElementById('qr-code-display' + orderId).innerHTML;
            print(qrCodeImg);
            loadOrders();

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