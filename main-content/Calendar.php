<?php
require "../dbconnection.php";
?>

<h3 class="title-form">Calendar of Transactions</h3>
<hr>
<div class="filter-container">
    <!-- Fiters date range and displays the data based on that range -->
    <input type="date" id="start-date" value="" placeholder="Start Date">
    <input type="date" id="end-date" value="" placeholder="End Date">
    <button id="filter-btn" class="filter-btn">Filter</button>

</div>
<div>
    <table class="calendar">
        <thead>
            <th>Date</th>
            <th>Transactions</th>
        </thead>
        <tbody id="transaction-table-body">

        </tbody>
    </table>
</div>
<script>
    // Para sa pag generate ng dates
    $(document).ready(function() {

        // Check if there's a previously selected date range in localStorage
        let storedStartDate = localStorage.getItem('startDate');
        let storedEndDate = localStorage.getItem('endDate');

        let today = new Date();
        let currentStartDate = storedStartDate ? new Date(storedStartDate) : today;
        let currentEndDate = storedEndDate ? new Date(storedEndDate) : today;

        // Set the filter input values to the stored dates or today's date
        $('#start-date').val(currentStartDate.toISOString().slice(0, 10));
        $('#end-date').val(currentEndDate.toISOString().slice(0, 10));

        // Automatically load transactions using stored dates on page load
        generateTransactionTable(currentStartDate, currentEndDate);

        // Function to generate the transaction table
        function generateTransactionTable(startDate, endDate, transactions = {}) {
            let tableBody = document.getElementById('transaction-table-body');
            tableBody.innerHTML = ""; // Clear previous table data

            // Iterate through each day in the date range and display transactions
            for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
                let formattedDate = date.toISOString().slice(0, 10); // Format date YYYY-MM-DD
                let row = document.createElement('tr');

                // Create a cell for the date
                let dateCell = document.createElement('td');
                dateCell.innerText = formattedDate;
                row.appendChild(dateCell);

                // Check for transactions on this date
                if (transactions[formattedDate]) {
                    let transactionDetails = transactions[formattedDate].map(function(transaction) {
                        return `Transaction ID: ${transaction.transaction_id}`;
                    }).join('<br/>');

                    // Create a cell for the transaction details
                    let transactionsCell = document.createElement('td');
                    transactionsCell.innerHTML = transactionDetails;
                    row.appendChild(transactionsCell);
                } else {
                    // If no transactions, show an empty cell
                    let emptyCell = document.createElement('td');
                    emptyCell.innerText = 'No transactions';
                    row.appendChild(emptyCell);
                }

                tableBody.appendChild(row);
            }
        }

        // Handle filtering with AJAX
        $('#filter-btn').click(function() {
            let startDate = $('#start-date').val();
            let endDate = $('#end-date').val();

            // Check if start and end dates are valid
            if (startDate && endDate) {
                // Save the selected start and end dates in localStorage
                localStorage.setItem('startDate', startDate);
                localStorage.setItem('endDate', endDate);

                // Send AJAX request to fetch transactions within the date range
                $.ajax({
                    url: './main-content/fetch_calendar.php',
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Generate table with the transaction data
                            generateTransactionTable(new Date(startDate), new Date(endDate), response.transactions);
                        } else {
                            alert('No transactions found for the selected date range.');
                        }
                    },
                    error: function() {
                        alert('Error retrieving transactions.');
                    }
                });
            } else {
                alert('Please select a valid start and end date.');
            }
        });

        // Automatically save today's date if no start and end dates are found
        if (!storedStartDate || !storedEndDate) {
            localStorage.setItem('startDate', today.toISOString().slice(0, 10));
            localStorage.setItem('endDate', today.toISOString().slice(0, 10));
        } else {
            // Send AJAX request to load transactions for the previously stored date range
            $.ajax({
                url: './main-content/fetch_calendar.php',
                type: 'GET',
                data: {
                    start_date: storedStartDate,
                    end_date: storedEndDate
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Generate table with the transaction data
                        generateTransactionTable(new Date(storedStartDate), new Date(storedEndDate), response.transactions);
                    } else {
                        alert('No transactions found for the selected date range.');
                    }
                },
                error: function() {
                    alert('Error retrieving transactions.');
                }
            });
        }
    });
</script>