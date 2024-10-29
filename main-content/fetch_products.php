<?php
require "../dbconnection.php";

// Updated na para ma check properly yung foreign keys which is yung category_id
// Fetch products that are not archived along with their cell numbers
$query = "SELECT products.*, categories.category_name, sizes.size_name, products.unit_num
          FROM products
          INNER JOIN categories ON products.category_id = categories.category_id
          INNER JOIN sizes ON products.size_id = sizes.size_id
          WHERE products.is_archived = 0";

$display_added = mysqli_query($conne, $query);

// Fetch all assigned cell numbers
$used_cells_query = "SELECT DISTINCT unit_num FROM products WHERE unit_num IS NOT NULL";
$used_cells_result = mysqli_query($conne, $used_cells_query);
$used_cells = [];
while ($row = mysqli_fetch_assoc($used_cells_result)) {
    $used_cells[] = $row['unit_num'];
}
?>
<tr>
    <th>Unit Num.</th>
    <th>Product Name</th>
    <th>Size</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Sold Quantity</th>
    <th>Action</th>
</tr>
<?php if (mysqli_num_rows($display_added) === 0): ?>
    <tr>
        <td colspan="11" style="text-align: center;">
            <h1 style="color:black;">No products added</h1>
        </td>
    </tr>
<?php else: ?>
    <?php while ($row = mysqli_fetch_assoc($display_added)) { ?>
        <tr>
            <td>
                <select class="cell-num-select" data-product-id="<?php echo htmlspecialchars($row['product_id']); ?>">
                    <option value="" disabled style="color: white;">Select Cell Number</option>
                    <option value="" <?php echo is_null($row['unit_num']) ? 'selected' : ''; ?>>NULL</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <?php if (!in_array($i, $used_cells) || $i == $row['unit_num']): ?>
                            <option value="<?php echo $i; ?>" <?php echo $i == $row['unit_num'] ? 'selected' : ''; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php endif; ?>
                    <?php endfor; ?>
                </select>
            </td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['size_name']); ?></td>
            <td><?php echo htmlspecialchars($row['price']); ?></td>
            <td><?php echo htmlspecialchars($row['product_quantity']); ?></td>
            <td><?php echo htmlspecialchars($row['sold_quantity']); ?></td>
            <div class="action-container">
                <td style="margin-left: 10px;">
                    <a class="edit-btn" data-id="<?php echo htmlspecialchars($row['product_id']); ?>"><i style="color: black;" class="fa-regular fa-pen-to-square fa-lg"></i></a>
                    <a class="delete-btn" data-id="<?php echo htmlspecialchars($row['product_id']); ?>"><i style="color: black;" class="fa-solid fa-box-archive fa-lg"></i></a>
                    <a class="info-btn" data-id="<?php echo htmlspecialchars($row['product_id']); ?>"><i style="color: black;" class="fa-solid fa-circle-info fa-lg"></i></a>
                </td>
            </div>
        </tr>
    <?php } ?>
<?php endif; ?>

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

            if (newCellNum) {
                usedCells.push(newCellNum);
            }

            $.ajax({
                url: './main-content/update_unit_num.php',
                type: 'POST',
                data: {
                    product_id: productId,
                    unit_num: newCellNum
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // alert('Cell number updated successfully!');
                        loadTable();

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