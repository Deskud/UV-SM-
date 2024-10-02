<?php
include 'session_check.php';
require "dbconnection.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@706&family=Roboto:ital,wght@0,400;1,300&display=swap" rel="stylesheet">
    <title>Dashboard</title>
</head>

<body id="Main-body">

    <div id="Topnavigator-container">
        <img style="margin-right: 10px;" src="./Images/PCU Logo.png">
        <h1 style="padding:10px;margin:-2px;">Uniform Stock Monitoring System</h1>
        <div class="sign-out">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"> Sign out</i></a>
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
                            Purchase History
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
                        <a href="#" data-target="./main-content/Dashboard.php">
                            <i class="fa-solid fa-gauge"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#" data-target="./main-content/orders.php">
                            <i class="fa-solid fa-qrcode"></i>
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="#" data-target="./main-content/PurchaseHistory.php">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                            Purchase History
                        </a>
                    </li>
                    <!-- Cashiers cannot access Products and Archive -->
                <?php endif; ?>
            </ul>
        </ul>
        <hr>
        <!-- <ul>
            <li style="display: block;">
                <a href="logout.php" style="color: white;"><i class="fa-solid fa-right-from-bracket"></i> Sign out</a>
            </li>
        </ul> -->
    </div>
    <div id="Main-container">
        <?php include "./main-content/Dashboard.php"; ?>
    </div>
    <div id="Right-container">
        <h5 class="title-form">Notification</h5>

        <div id="notifcations-table">
            <!-- lalagay akong ajax dito para mag 
                     pakita mga notif real time -->
        </div>
    </div>
    <script src="./jquery/jquery-3.7.1.min.js"></script>
    <script src="Script.js"></script>
    <script src="https://kit.fontawesome.com/883b8ee9d9.js" crossorigin="anonymous"></script>
</body>

</html>