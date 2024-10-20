<?php
require "../dbconnection.php";
include '../session_check.php';

?>
<h3 class="title-form">Purchase History</h3>
<hr>

<div class="download-purchase-history">
    <input type="button" class="download-data" value="Export .csv" onclick="window.location.href='download_data.php'">

</div>

<div class="transactions-table-container">
    <!-- dito lalabas tables -->
</div>


<div id="transaction-modal" class="modal">
    <div class="modal-transaction-content">
        <form method="post" enctype="multipart/form-data" id="edit-transactform">
            <span class="close-modal"><i id="close-icon" class="fa-solid fa-xmark"></i></span>

            <h1 style="color: #0454ac; text-align:center;">Edit Transaction</h3>

                <input type="hidden" name="transaction_id" id="transaction_id" value="">

                <h3 style="color: #0454ac; text-align:center;">Total Amount</h3>
                <div class="transact-amount">
                    <input type="number" class="select-total-amount" name="total-amount">
                </div>
                <h3 style="color: #0454ac; text-align:center;">Total Quantity</h3>

                <div class="transact-quantity">
                    <input type="number" class="select-total-quantity" name="total-quantity">

                </div>
                <h3 style="color: #0454ac; text-align:center;">Status</h3>
                <div class="transact-status">
                    <select class="select-transact-status" name="status">
                        <option value="" disabled selected>Status</option>
                        <option value="Unclaimed">Unclaimed</option>
                        <option value="Partially Claimed">Partially Claimed</option>
                        <option value="Fully Claimed">Fully Claimed</option>
                    </select>
                </div>
                <h3 style="color: #0454ac; text-align:center;">Quantity Dispensed</h3>
                <input type="number" min="0" oninput="validity.valid||(value='');" class="input-quantity-dispensed" name="quantity-dispensed">

                <div class="transact-submit">
                <input type="submit" name="Transaction" id="update-transaction" value="Update">
                </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="modal.js"></script>
<script>
    $(document).ready(function() {
        loadPurchases();

        $(document).off('click', '.edit-transaction').on('click', '.edit-transaction', function()  {
            let transactId = $(this).data('id');
            $('#transaction-modal').css('display', 'block');

            $.ajax({
                url: './main-content/fetch_single_transaction.php',
                type: 'GET',
                data: {
                    transaction_id: transactId
                },
                success: function(data) {
                    var transact = JSON.parse(data);

                    console.log(data);

                    if (!transact.error) {
                        $('#transaction_id').val(transact.transaction_id);
                        $('.select-total-amount[name="total-amount"]').val(transact.total_amount);
                        $('.select-total-quantity[name="total-quantity"]').val(transact.total_quantity);
                        $('select-transact-status[name="status"]').val(transact.status);
                        $('.input-quantity-dispensed[name="quantity-dispensed"]').val(transact.quantity_dispensed);
                    } else {
                        alert('BRUH: ' + transact.error);
                    }
                }
            })
        });

        $('#edit-transactform').on('submit', function(e) {
            e.preventDefault();

            var formEdit = $(this).serialize();
            $.ajax({
                url: './main-content/update_transactions.php',
                type: 'POST',
                data: formEdit,
                success: function(response) {

                    $('#transaction-modal').css('display', 'none');
                    loadPurchases();
                },
                error: function(jqXHR, textStatus, errorThrown) {

                }
            });
        });
    });


    function loadPurchases() {
        $.ajax({
            url: './main-content/fetch_transactions.php',
            type: 'GET',
            success: function(data) {
                $('.transactions-table-container').html(data);
            },
            error: function() {
                alert('Error cannot load datas. OOF');
            }
        })
    }
    setInterval(loadPurchases, 5000);
    // function purchasePoll() {
    //     $.ajax({
    //         url: './server/purchasehistory_poll.php',
    //         type: 'GET',
    //         dataType: 'json',
    //         success: function(response) {
    //             $('#transactions-table tbody').empty();

    //             // Append new rows with updated transaction data
    //             response.forEach(function(row) {
    //                 $('#transactions-table tbody').append(
    //                     `<tr>
    //                         <td>${row.transaction_id}</td>
    //                         <td>${row.order_id}</td>
    //                         <td>${row.total_quantity}</td>
    //                         <td>${row.total_amount}</td>
    //                         <td>${row.transaction_date}</td>
    //                         <td>${row.qr_code}</td>
    //                         <td>${row.quantity_dispensed}</td>
    //                         <td>${row.status}</td>
    //                     </tr>`
    //                 );
    //             });
    //         },
    //         error: function(xhr, status, error) {
    //             console.error("Failed to fetch transactions:", error);
    //         }
    //     });
    // }

    // setInterval(purchasePoll, 3000);
</script>