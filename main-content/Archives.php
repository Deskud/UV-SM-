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
          ON products.size = sizes.size_id
          WHERE products.is_archived = 1";


$result = mysqli_query($conne, $query);
?>

<div class="archive-table-container">
    <table class="archive-table">
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Size</th>
            <th>Price</th>
            <th>Gender</th>
            <th>Sold Quantity</th>
            <th>Date Archived</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                <!-- Binago ko into category_name para ma display yung name base sa foreign key na category_id -->
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td><?php echo htmlspecialchars($row['size_name']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['sold_quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['date_archived']); ?></td>
            </tr>
        <?php } ?>
    </table>

    <!-- Pagination (example, modify as needed) -->
    <div class="pagination">
        <a href="#">&lsaquo;</a>
        <a href="#" class="active">1</a>
        <a href="#">2</a>
        <a href="#">3</a>
        <a href="#">&rsaquo;</a>
    </div>
    <div class="pagination-info">
        <p>Page 1 of #</p>
    </div>
</div>