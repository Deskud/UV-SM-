<?php
require "../dbconnection.php";

$query = "SELECT*FROM transactions";

$displayDates = mysqli_query($conne, $query);

?>

<h2 style="text-align: center;">Calendar of Transactions</h2>

<div>
    <label for="filter-date">Filter by Date:</label>
    <input type="date" id="filter-date">
    <button id="filter-btn">Filter</button>
</div>
<div>
    <table>
        <thead>

            <th>Sun</th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
        </thead>
        <tbody id="calendar-dates">

        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        // Load transactions for today on page load
        let today = new Date().toISOString().split('T')[0];
        loadTransactions(today);

        // AJAX to add transaction
        $('#transaction-form').submit(function(event) {
            event.preventDefault(); // Prevent form submission
            const transactionData = {
                transaction_name: $('#transaction_name').val(),
                transaction_amount: $('#transaction_amount').val(),
                transaction_date: $('#transaction_date').val()
            };

            $.ajax({
                url: '',
                type: 'POST',
                data: transactionData,
                success: function(response) {
                    alert(response);
                    // Reload transactions for the selected date
                    loadTransactions(transactionData.transaction_date);
                }
            });
        });

        // Load transactions via AJAX for a specific date
        function loadTransactions(date) {
            $.ajax({
                url: 'filter_date.php',
                type: 'GET',
                data: {
                    date: date
                },
                success: function(response) {
                    $('#transactions-list').html(response);
                }
            });
        }

        // Generate simple calendar for current month (example)
        generateCalendar(new Date().getMonth(), new Date().getFullYear());

        function generateCalendar(month, year) {
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const firstDay = new Date(year, month, 1).getDay();

            let tableBody = document.getElementById('calendar-dates');
            let date = 1;
            tableBody.innerHTML = ""; // Clear previous table

            for (let i = 0; i < 6; i++) { // 6 weeks in a month (max)
                let row = document.createElement('tr');
                for (let j = 0; j < 7; j++) {
                    let cell = document.createElement('td');
                    if (i === 0 && j < firstDay) {
                        cell.innerText = '';
                    } else if (date > daysInMonth) {
                        break;
                    } else {
                        cell.innerText = date;
                        date++;
                    }
                    row.appendChild(cell);
                }
                tableBody.appendChild(row);
            }
        }
    });
</script>