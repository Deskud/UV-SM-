<?php
require "../dbconnection.php";
include '../session_check.php';

// https://www.youtube.com/watch?v=Yggrlux69MQ
// Noice tutorial


if (isset($_POST['input'])) {

    $input = $_POST['input'];

    $query = "SELECT* FROM products  
    WHERE product_name LIKE '%" . mysqli_real_escape_string($conne, $input) . "%' 
    OR price LIKE '%" . mysqli_real_escape_string($conne, $input) . "%'";

    $result = mysqli_query($conne, $query);

    if (mysqli_num_rows($result) > 0) { ?>

        <table class="data-result-table">
            <thead>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Size</th>
                <th>Gender</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Date Added</th>
                <th>Is Archived</th>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['size_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_added']); ?></td>
                        <td><?php echo htmlspecialchars($row['is_archived']); ?></td>

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