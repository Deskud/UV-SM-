<?php
require "../dbconnection.php"; // Include database connection
include '../session_check.php';

?>


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


    <!-- Refresh table data -->
    <button id="reloadTable" onclick="loadOrders()"><i class="fa-solid fa-rotate-right"></i>
    </button>

    <div id="order-table-container">
        <!-- Dito mag di-display order table -->
    </div>


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
        $(document).ready(function() {
            loadOrders();
        })



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


            document.getElementById('orderModal' + orderId).style.display = "block";

            $.ajax({
                url: './main-content/order_status.php',
                type: 'POST',
                data: {
                    order_id: orderId
                },
                success: function(response) {
                    document.getElementById(`order-row-${orderId}`).cells[2].innerText = response;
                    proceedButtonVisibility(orderId, status);
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
                    proceedButtonVisibility(orderId, status);
                },
                error: function() {
                    alert('Error reverting the order status.');
                }
            });
        }

        function proceedButtonVisibility(orderId, status) {
            const proceedButton = document.querySelector(`#proceed-btn-${orderId}`);
            const buttonContainer = proceedButton.closest('.prc-btn-container');
            if (!proceedButton || !buttonContainer) return;

            if (status === 'pending') {
                proceedButton.style.display = 'block';
                proceedButton.disabled = false; // 
            } else if (status === 'processing' || status === 'complete') {
                proceedButton.style.display = 'none';
            }

            // Force a reflow (just in case) to ensure the button remains centered
            buttonContainer.style.justifyContent = 'center';
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
                    quantities: quantities,
                },
                success: function(response) {
                    console.log(response);
                    loadOrderDetails(orderId);
                    $('#updateModal' + orderId).css('display', 'none');
                    loadQrDetails(orderId);
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        }

        function finishOrder(orderId) {
            console.log("Order Id:", orderId);

            var studentNo = $('#student-id-' + orderId).val();

            // Check if the input has data; if none, it will not proceed to finishing the order
            if (!studentNo || studentNo.trim() === '') {
                alert('Please enter a valid student number before proceeding.');
                return;
            }

            $('#confirmation-modal').css('display', 'block');


            // Ngayon ko lang narinig .one function wow

            $('#confirm-print').off('click').one('click', function() {
                $('#confirmation-modal').css('display', 'none');

                $.ajax({
                    url: './main-content/orders_function.php',
                    method: 'POST',
                    data: {
                        action: 'finish',
                        order_id: orderId,
                        student_no: studentNo
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("AJAX request successful. Response:", response);
                        loadOrderDetails(orderId);
                        if (response.qrcode) {
                            console.log("QR Code generated successfully for orderId:", orderId);

                            $('#qr-student-id-' + orderId).text(studentNo);

                            var qrDisplay = document.getElementById('qr-code-display' + orderId);
                            if (qrDisplay) {
                                qrDisplay.innerHTML = "<img src='./main-content/" + response.qrcode + "' alt='QR Code' />";
                            }

                            // Open the receipt in a new window and trigger print
                            openReceiptWindow(orderId, studentNo, response.qrcode);
                        } else {
                            console.error("Error in response: ", response.error);
                            alert(response.error || 'Failed to generate QR code');
                        }


                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX request failed. Status:", textStatus, "Error:", errorThrown);
                        alert('Error processing the request.');
                    }
                });
            });
        }


        // Function to open a new window and load receipt content
        function openReceiptWindow(orderId, studentNo, qrCodeUrl) {
            // Get the content of the modal
            var modalContent = `
        <div>
            <h4 style = "text-align:center;">Philippine Christian University - Dasmariñas</h6>
            <h6 style = "text-align:center;" >PCU New Building, Dasmariñas, 4114 Cavite</h6>
            <h6 style = "text-align:center;">********************</h6>
            <title>Order Receipt</title>
            <h6  style = "text-align:center;" >Order ID: ${orderId}</h6>
            <h6  style = "text-align:center;">Student Number: ${studentNo}</h6>
            <div style = "text-align:center;" id="itemContainer">
                ${$('#itemContainer' + orderId).html()}
            </div>
            <h6 style = "text-align:center;">********************</h6>
            <div id="qr-code-display" style:"text-align:center;">
                <img id='qrCodeImage'  src='./main-content/${qrCodeUrl}' alt='QR Code' />
            </div>
        </div>
    `;
            // Opens new window and triggers print window.
            var printWindow = window.open('', '_blank', 'width=600,height=400');
            printWindow.document.write(
                `
                    <html>
                    <head>
                        <link rel="stylesheet" href="./asset/styles.css">
                    </head>
                    <img src="Images/PCU Logo.png" alt="PCU Logo" id="PCUlogo-login-receipt">
                    <body>
                        ${modalContent}
                    </body>
                    </html>
                `
            );

            printWindow.document.close();
            printWindow.onload = function() {
                printWindow.focus();
                printWindow.print();
                loadOrders();
            };
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

                            $('#item-id-' + itemId).remove();
                            loadOrderDetails(orderId);
                            loadQrDetails(orderId);

                        } else {
                            // Display error message
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.'); 
                    }
                });
            }

            $(document).off('click', '.remove-item');

            $(document).on('click', '.remove-item', function(event) {
                var itemId = $(this).data('item-id');
                var orderId = $(this).data('order-id');
                var itemElement = $('#item-id-' + itemId);

                removeItem(itemId, orderId, itemElement);
            });
        });

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

        // Load yung laman sa qr para dynamically mag update kapag nag re-remove o nag dagdag
        function loadQrDetails(orderId) {
            $.ajax({
                url: './main-content/update_qrmodal.php',
                type: 'GET',
                data: {
                    order_id: orderId
                },
                dataType: 'json',
                success: function(response) {

                    console.log("Response:", response);
                    if (response.success) {
                        var itemContainer = $('#itemContainer' + orderId);
                        itemContainer.empty(); // Clear existing items

                        if (response.items.length > 0) {
                            response.items.forEach(function(item) {
                                var itemHtml = `<h6>Item: ${item.product_name}......x ${item.quantity}</h6>`;
                                itemContainer.append(itemHtml);
                            });
                        } else {
                            itemContainer.append('<h6>No items found for this order.</h6>');
                        }
                    } else {
                        alert('Failed to load order items.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading order items:', error);
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

        function pollOrderStatus() {
            $.ajax({
                url: './server/orders_poll.php',
                type: 'GET',
                dataType: 'json',
                success: function(orders) {

                    orders.forEach(order => {
                        const row = document.getElementById(`order-row-${order.order_id}`);
                        if (row) {
                            row.cells[2].innerText = order.status; // Update status
                            proceedButtonVisibility(order.order_id, order.status);

                        }
                    });
                },
                error: function() {
                    console.error('Error fetching orders.');
                }
            });
        }
        setInterval(pollOrderStatus, 3000);
    </script>