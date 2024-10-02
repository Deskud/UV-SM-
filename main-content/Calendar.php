<?php
require "../dbconnection.php";
?>

<h3 class="title-form">Calendar of Transactions</h3>
<div>
    <hr>
    <input type="date" id="filter-date">
    <button class="filter-btn" id="filter-btn">Filter</button>
</div>
<div>
    <table class="calendar">
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
    // Para sa pag generate ng dates
    $(document).ready(function() {
        // Check if there's a previously selected date in localStorage, else use today's date
        let storedDate = localStorage.getItem('selectedDate');
        let today = new Date();
        let currentDate = storedDate ? new Date(storedDate) : today;

        // Set the filter input to the stored date or today's date
        $('#filter-date').val(currentDate.toISOString().slice(0, 10));

        // Initially generate the calendar based on stored date or today's date
        generateCalendar(currentDate.getMonth(), currentDate.getFullYear());

        // Function to generate the calendar
        function generateCalendar(month, year, transactions = {}) {
            const daysInMonth = new Date(year, month + 1, 0).getDate(); // Total days in the month
            const firstDay = new Date(year, month, 1).getDay(); // Day of the week for the 1st of the month

            let tableBody = document.getElementById('calendar-dates');
            let date = 1;
            tableBody.innerHTML = ""; // Clear previous table

            for (let i = 0; i < 6; i++) { // Max 6 weeks in a month
                let row = document.createElement('tr');
                for (let j = 0; j < 7; j++) {
                    let cell = document.createElement('td');
                    if (i === 0 && j < firstDay) {
                        cell.innerText = ''; // Empty cell for days of the previous month
                    } else if (date > daysInMonth) {
                        cell.innerText = ''; // Empty cell after last day of the month
                    } else {
                        let currentDate = new Date(year, month, date);
                        let formattedDate = currentDate.toISOString().slice(0, 10); // Format date YYYY-MM-DD

                        // Display the day number in the cell
                        cell.innerHTML = `<strong>${date}</strong><br/>`;

                        if (transactions[formattedDate]) {
                            // Add transaction details in the cell
                            transactions[formattedDate].forEach(function(transaction) {
                                cell.innerHTML += `Transaction ID: ${transaction.transaction_id}<br/>`;
                            });
                        }

                        date++;
                    }
                    row.appendChild(cell);
                }
                tableBody.appendChild(row);
            }
        }

        // Handle filtering with AJAX
        $('#filter-btn').click(function() {
            let selectedDate = $('#filter-date').val();
            if (selectedDate) {
                let selectedMonth = new Date(selectedDate).getMonth();
                let selectedYear = new Date(selectedDate).getFullYear();

                // Save the selected date in localStorage
                localStorage.setItem('selectedDate', selectedDate);

                // Send AJAX request to fetch transactions for the selected date
                $.ajax({
                    url: './main-content/fetch_calendar.php',
                    type: 'GET',
                    data: {
                        filter_date: selectedDate
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Generate calendar with the correct month/year and transactions
                            generateCalendar(selectedMonth, selectedYear, response.transactions);
                        } else {
                            alert('No transactions found for the selected date.');
                        }
                    },
                    error: function() {
                        alert('Error retrieving transactions.');
                    }
                });
            }
        });

        // Automatically generate the calendar for today's date or the selected date on page load
        if (!storedDate) {
            localStorage.setItem('selectedDate', today.toISOString().slice(0, 10));
        }
    });
</script>