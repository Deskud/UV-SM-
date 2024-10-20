<?php
include 'session_check.php';
require "dbconnection.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <script src="./jquery/jquery-3.7.1.min.js"></script>


    <link rel="stylesheet" href="./asset/styles.css">

    <!-- Font awesome -->
    <link href="./asset/css/fontawesome.css" rel="stylesheet" />
    <link href="./asset/css/brands.css" rel="stylesheet" />
    <link href="./asset/css/solid.css" rel="stylesheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@706&family=Roboto:ital,wght@0,400;1,300&display=swap" rel="stylesheet">

    <title>Dashboard</title>
</head>


<body id="Main-body">
    <div class="notifications-container">
        <!-- dito lalabas pop up -->
    </div>



    <div id="Topnavigator-container">
        <img style="margin-right: 20px;" src="./Images/PCU Logo.png">
        <h1 style="padding:10px;margin:-5px;">Uniform Stock Monitoring System</h1>
        <div class="sign-out">

            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Sign out</a>

        </div>
    </div>


    </div>
    <div id="Sidebar-container">
        <ul class="Sidebar-links">
            <ul>
                <div class="user-name">
                    <h3><?php echo $_SESSION['username']; ?></h3>
                </div>
                <hr>
                <?php if ($_SESSION['access_id'] === 1): ?>
                    <li>
                        <a href="#" class="page-nav" data-target="./main-content/Dashboard.php">
                            <i class="fa-solid fa-gauge"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#" class="page-nav" data-target="./main-content/PurchaseHistory.php">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                            Transactions
                        </a>
                    </li>
                    <li>
                        <a href="#" class="page-nav" data-target="./main-content/Products.php">
                            <i class="fa-solid fa-shirt"></i>
                            Products
                        </a>
                    </li>
                    <li>
                        <a href="#" class="page-nav" data-target="./main-content/orders.php">
                            <i class="fa-solid fa-qrcode"></i>
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="#" class="page-nav" data-target="./main-content/Archives.php">
                            <i class="fa-solid fa-box-archive"></i>
                            Archive
                        </a>
                    </li>
                    <li>
                        <a href="#" class="page-nav" data-target="./main-content/Calendar.php">
                            <i class="fa-solid fa-calendar"></i>
                            Calendar
                        </a>
                    </li>
                <?php elseif ($_SESSION['access_id'] === 2): ?>
                    <li>
                        <a href="#" class="page-nav" data-target="./main-content/Dashboard.php">
                            <i class="fa-solid fa-gauge"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#" class="page-nav" data-target="./main-content/orders.php">
                            <i class="fa-solid fa-qrcode"></i>
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="#" class="page-nav" data-target="./main-content/PurchaseHistory.php">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                            Purchase History
                        </a>
                    </li>
                    <!-- Cashiers cannot access Products and Archive -->
                <?php endif; ?>
            </ul>
        </ul>
        <hr>
    </div>
    <div id="Main-container">
        <?php include "./main-content/Dashboard.php"; ?>
    </div>

    </div>

    <script src="Script.js"></script>
</body>

</html>
<script>
    // Notifications for actions
    function notifTrigger() {
        $.ajax({
            url: './server/notifications.php', // Endpoint to check for new records
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // Check for new transactions
                if (data.newTransaction) {
                    showNotification(data.transactionMessage);
                }

                // Check for new items
                if (data.newItem) {
                    showNotification(data.itemMessage);
                }

                // Check for new orders
                if (data.newOrder) {
                    showNotification(data.orderMessage);
                }

                // Check for new products
                if (data.newProduct) {
                    showNotification(data.productMessage);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error fetching notifications:", textStatus, errorThrown);
            }
        });
    }

    function showNotification(message) {
        var notification = $('<div class="notification-popup">' + message + '</div>');
        $('.notifications-container').append(notification);

        notification.fadeIn(300);

        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    setInterval(notifTrigger, 5000);
</script>