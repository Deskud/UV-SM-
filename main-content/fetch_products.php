<?php
require "../dbconnection.php";

// Fetch products that are not archived

// $query = "SELECT * FROM products WHERE is_archived = 0";

// $query_product_name = "SELECT products.product_id, products.product_name, products.size, products.gender, products.cell_num, products.price, products.product_quantity, products.date_added, products.is_archived, categories.category_id
// FROM products INNER JOIN categories ON products.product_name = categories.category_id";

// Updated na para ma check properly yung foreign keys which is yung category_id
// Fetch products that are not archived along with their cell numbers
$query = "SELECT products.*, categories.category_name, sizes.size_name, products.cell_num
          FROM products
          INNER JOIN categories ON products.category_id = categories.category_id
          INNER JOIN sizes ON products.size = sizes.size_id
          WHERE products.is_archived = 0";

$display_added = mysqli_query($conne, $query);

// Fetch all assigned cell numbers
$used_cells_query = "SELECT DISTINCT cell_num FROM products WHERE cell_num IS NOT NULL";
$used_cells_result = mysqli_query($conne, $used_cells_query);  // Fix variable name here
$used_cells = [];  // Initialize empty array for used cell numbers
while ($row = mysqli_fetch_assoc($used_cells_result)) {
    $used_cells[] = $row['cell_num'];  // Populate the array with cell numbers
}
?>
<tr>
    <th>Product Name</th>
    <th>Size</th>
    <th>Gender</th>
    <th>Price <a href="#"><i class="fa-solid fa-sort"></i></a></th>
    <th>Quantity <a href="#"><i class="fa-solid fa-sort"></i></a></th>
    <th>Date Added</th>
    <th>Action</th>
    <th>Cell Num.</th>

</tr>

<?php while ($row = mysqli_fetch_assoc($display_added)) { ?>
    <tr>
        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
        <td><?php echo htmlspecialchars($row['size_name']); ?></td>
        <td><?php echo htmlspecialchars($row['gender']); ?></td>
        <td><?php echo htmlspecialchars($row['price']); ?></td>
        <td><?php echo htmlspecialchars($row['product_quantity']); ?></td>
        <td><?php echo htmlspecialchars($row['date_added']); ?></td>
        <td style="margin-left: 10px;">
            <a class="edit-btn" data-id="<?php echo htmlspecialchars($row['product_id']); ?>"><i style="color: black;" class="fa-regular fa-pen-to-square"></i></a>
            <a class="delete-btn" data-id="<?php echo htmlspecialchars($row['product_id']); ?>"><i style="color: black;" class="fa-solid fa-box-archive"></i></a>
        </td>
        <td>
            <select class="cell-num-select" data-product-id="<?php echo htmlspecialchars($row['product_id']); ?>">
                <option value="" disabled>Select Cell Number</option>
                <option value="" <?php echo is_null($row['cell_num']) ? 'selected' : ''; ?>>NULL</option>
                <?php for ($i = 1; $i <= 24; $i++): ?>
                    <?php if (!in_array($i, $used_cells) || $i == $row['cell_num']): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i == $row['cell_num'] ? 'selected' : ''; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endif; ?>
                <?php endfor; ?>
            </select>
        </td>
    </tr>
<?php } ?>
<script>
    var usedCells = [];

    $(document).ready(function() {
        // Initial loading of used cells
        $('.cell-num-select').each(function() {
            var cellNum = $(this).val();
            if (cellNum) {
                usedCells.push(cellNum);
            }
        });

        $('.cell-num-select').change(function() {
            var productId = $(this).data('product-id');
            var newCellNum = $(this).val();

            // Update the global usedCells array
            if (newCellNum) {
                usedCells.push(newCellNum);
            }

            // Send AJAX request to update the cell_num in the database
            $.ajax({
                url: './main-content/update_cell_num.php',
                type: 'POST',
                data: {
                    product_id: productId,
                    cell_num: newCellNum
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // alert('Cell number updated successfully!');
                        loadTable(); // Reload table to update dropdowns

                        // Update the dropdowns to remove used numbers
                        $('.cell-num-select').each(function() {
                            var dropdown = $(this);
                            dropdown.find('option').each(function() {
                                var optionValue = $(this).val();
                                if (optionValue && usedCells.includes(optionValue)) {
                                    $(this).remove(); // Remove used number options
                                }
                            });
                        });
                    } else {
                        alert('Error updating cell number: ' + response.error);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error: ' + errorThrown);
                }
            });
        });
    });
</script>