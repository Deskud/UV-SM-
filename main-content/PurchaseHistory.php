<?php
require "../dbconnection.php";
include '../session_check.php';

?>
<h3 class="title-form">Purchase History</h3>
<hr>


<button type="button" id='transact-print' class="transact-print">
    <i class="fa-solid fa-print"></i> Print
</button>

<div class="transactions-table-container">
    <!-- Displays transaction datas here dynamically -->
</div>


<div id="print-transaction" class="modal">
    <div class="modal-transaction-print">
        <span class="close-modal"><i id="close-icon" class="fa-solid fa-xmark"></i></span>

        <h1 class="title-form">Monthly Sales Report</h1>
        <p>Filter range of date to print:</p>
        <input type="date" id="start-date" value="" placeholder="Start Date">
        <input type="date" id="end-date" value="" placeholder="End Date">
        <button id="filter-btn" class="filter-btn" onclick="printTransaction()">Filter</button>


        <!-- <div class="download-purchase-history">
            <input type="button" class="download-data" value="Download" onclick="window.location.href='download_data.php'">
        </div> -->

    </div>
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
        // On load of the document products data should load.
        loadPurchases();

        // Edit Transaction
        $(document).off('click', '.edit-transaction').on('click', '.edit-transaction', function() {
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

        // For submitting updated data transactions
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

        // Opening the printing modal
        $(document).off('click', '#transact-print').on('click', '#transact-print', function() {
            $('#print-transaction').css('display', 'block');
            console.log('working');
        });

    });


    // Function for generating monthly sales report based on date range.
    function printTransaction() {

        var startDate = document.getElementById('start-date').value;
        var endDate = document.getElementById('end-date').value;



        $.ajax({
            url: './main-content/fetch_sales_report.php',
            method: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                var data = JSON.parse(response);
                var transactions = data.transactions;
                var totalAmount = 0;
                var totalQuantity = 0;
                var totalDispensed = 0;

                var printContent = '';
                transactions.forEach(function(transactions) {
                    printContent += `
                    <tr>
                        <td>${transactions.transaction_id}</td>
                        <td>${transactions.student_no}</td>
                        <td>${transactions.total_amount}</td>
                        <td>${transactions.total_quantity}</td>
                        <td>${transactions.transaction_date}</td>
                        <td>${transactions.quantity_dispensed}</td>     
                        <td>${transactions.qr_code}</td>
                        <td>${transactions.status}</td>    
                        <td>${transactions.last_name}</td>
                    </tr>
                `;

                    //Calculates total amount, total quantity, and total dispensed based on the date range selected. 
                    totalAmount += parseFloat(transactions.total_amount);
                    totalQuantity += parseFloat(transactions.total_quantity);
                    totalDispensed += parseInt(transactions.quantity_dispensed);
                });
                //Width and heigh is equivalent to an A4 paper. Meant for printing.
                let reportWindow = window.open('', '_blank', 'width=2480,height=3508')
                reportWindow.document.write(
                    `
            <html>
                <link rel="stylesheet" href="./asset/styles.css">

                <head>
                    <img src="Images/PCU Logo.png" alt="PCU Logo" id="PCUlogo-login-print">
                    <h1 style="color:black;">Uniform Monthly Sales Report</h1>
                </head>

                 <!-- Added style here for table head font color. Too lazy to change main styling sheet.  -->
                <style>
                 table th {color:black;}
                </style>
                <body>
                    
                    <table>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Student No</th>
                            <th>Unit Amount</th>
                            <th>Unit Quantity</th>                
                            <th>Transaction Date</th>
                            <th>Quantity Dispensed</th>
                            <th>QR Code</th>
                            <th>Status</th>
                            <th>Prepared By</th>   
                        </tr>
                            ${printContent}
                     
                    <table>
                    <h4 style="color:black;">
                        Total Month Uniform Sales: ${totalAmount}
                    </h4>

                    <h4 style="color:black;">
                       Total Unit Sale: ${totalQuantity}
                    </h4>
                    <h4 style="color:black;">
                       Total Dispensed: ${totalDispensed}
                    </h4>

                </body>
            </html>
            `
                );

                reportWindow.document.close();
                reportWindow.onload = function() {
                    reportWindow.focus();
                    reportWindow.print();

                };
            }
        });
    }

    // Shows purchase data 
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

    // Loads purchase data every 5 seconds
    setInterval(loadPurchases, 5000);
</script>