<?php
require "../dbconnection.php"; // Include database connection
include '../session_check.php';
checkAccess(ADMIN);


//updated na para ma check yung category foreign keys para ma display yung category_name instead na category_id lang
$query = "SELECT products.*, categories.category_name, sizes.size_name
          FROM products
          INNER JOIN categories
          ON products.category_id = categories.category_id
          INNER JOIN sizes
          ON products.size_id = sizes.size_id
          WHERE products.is_archived = 1";


$result = mysqli_query($conne, $query);
?>
<h3 class="title-form">Archived Products</h3>
<hr>
<div class="archive-table-container">
    <table class="archive-table">
        <tr>
            <th>Product</th>
            <th>Size</th>
            <th>Price</th>
            <th>Sold Quantity</th>
            <th>Date Archived</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <!-- Binago ko into category_name para ma display yung name base sa foreign key na category_id -->
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td><?php echo htmlspecialchars($row['size_name']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['sold_quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['date_archived']); ?></td>
            </tr>
        <?php } ?>
    </table>

</div>