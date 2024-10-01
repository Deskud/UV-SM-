<?php
require "../dbconnection.php";
include '../session_check.php';


if (isset($_POST['orderinput'])) {

    $input = $_POST['orderinput'];

    $query = "SELECT * FROM orders  WHERE order_id LIKE '%" . mysqli_real_escape_string($conne, $input) . "%' 
    OR status LIKE '%" . mysqli_real_escape_string($conne, $input) . "%'";



    $result = mysqli_query($conne, $query);

    if (mysqli_num_rows($result) > 0) { ?>

        <table class="data-result-table">
            <thead>
                <th>Order ID</th>
                <th>Student No.</th>
                <th>Order Date</th>
                <th>Status</th>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td style="text-align: center;">
                            <button><a href="#" class="proceed-btn " data-order-id="' . htmlspecialchars($row['order_id']) . '">
                                    Proceed</button>
                            </a>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

<?php
    } else {
        echo "Product does not exist.";
    }
}
?>