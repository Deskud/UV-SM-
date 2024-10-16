<?php
require "../dbconnection.php";
include '../session_check.php';
checkAccess(ADMIN); // Ensure only admins can access this page

// ADD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve posted data
    $product_category = $_POST['category'];
    $product_name = $_POST['name'];
    $product_sizes = $_POST['size'];
    $product_gender = $_POST['gender'];
    $product_quantity = $_POST['quantity'];
    $product_price = $_POST['price'];

    if (!empty($product_sizes)) {
        foreach ($product_sizes as $size) {
            // Insert a row for each selected size and set unit_num to NULL
            $insert_new_product = "INSERT INTO products (category_id, product_name, size_id, gender, product_quantity, price, unit_num) 
                                   VALUES ('$product_category','$product_name', '$size', '$product_gender', '$product_quantity', '$product_price', NULL)";

            $add_new_product = mysqli_query($conne, $insert_new_product);

            if ($add_new_product) {
                echo "Product with size $size added successfully!<br>";
            } else {
                echo "Error adding product with size $size: " . mysqli_error($conne) . "<br>";
            }
        }
    } else {
        echo "Please select at least one size.";
    }
}
?>

<h3 class="title-form">Products</h3>
<hr>
<!-- Add Products Modal -->
<div id="add-product-modal" class="modal">
    <div class="modal-content">

        <span class="close-modal"><i id="close-icon" class="fa-solid fa-xmark"></i></span>


        <!-- Add Products Part -->
        <h3 class="title-form-product">Add Product</h3>
        <form action="" method="POST" enctype="multipart/form-data" id="input-form">
            <input type="hidden" name="product_id" id="product_id" value="">
            <h3 style="color: #0454ac; text-align:center;">Uniform Type</h3>
            <select class="select-uniform-name" name="category" required>
                <option value="" disabled selected>...</option>
                <option value="1">Regular Uniform</option>
                <option value="2">P.E. Uniform</option>
                <option value="3">Others</option>
            </select>


            <div class="product-name">
                <h3 style="color: #0454ac;">Name</h3>
                <input type="text" name="name">
            </div>

            <div class="sizes-container">
                <h3 style="color: #0454ac;">Sizes</h3>
                <input type="checkbox" value="1" name="size[]">Small
                <input type="checkbox" value="2" name="size[]">Medium
                <input type="checkbox" value="3" name="size[]">Large
                <input type="checkbox" value="4" name="size[]">Extra Large
            </div>
            <div class="gender-container">
                <h3 style="color: #0454ac;">Gender</h3>
                <select class="select-gender" name="gender" required>
                    <option value="" disabled selected>Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="unisex">Unisex</option>
                </select>
            </div>

            <!-- 
                minimum input ay 0 tapos bawal maglagay ng negative number LESGOOOOO BABY (THANKS GOOGLE) 
            -->
            <input type="number" min="0" oninput="validity.valid||(value='');" placeholder="Enter Quantity" name="quantity" class="input-quantity" required>
            <input type="number" min="0" oninput="validity.valid||(value='');" placeholder="Enter Price" name="price" class="input-price" required>

            <input type="submit" name="Add" class="submit-add-product" id="submit-btn" value="Add">
        </form>


    </div>
</div>

<!-- Add Products Part End -->

<!-- Modal window for confirmation of archiving -->
<div id="confirmation-modal" class="modal">
    <div class="modal-confirm-content">
        <h3 class="title-form">Confirm Action</h3>
        <p>Are you sure you want to archive this product?</p>
        <div class="confirmation-btn">
            <button type="button" id="confirm-archive" class="confirm-btn">Yes</button>
            <button type="button" id="cancel-archive" class="cancel-btn">No</button>
        </div>
    </div>
</div>

<!-- Modal window para sa information ng products -->
<div id="info-modal" class="modal">
    <div class="modal-info-content">
        <span class="close-modal"><i id="close-icon" class="fa-solid fa-xmark"></i></span>
        <h3 class="title-form">Product Information</h3>
        <div class="product-info">

        </div>
        </p>
    </div>
</div>


<!-- Start Table Display -->

<div class=" products-table-container">

    <!-- Button for add product modal window -->
    <div class="open-product-modal">
        <!-- Search bar -->
        <input id="find-data" class="search-table" type="search" placeholder="Search...">
        <button type="button" id="add-product-btn"><i id="icon-plus" class="fa-solid fa-plus"></i>Add Product</button>

    </div>
    <!-- Search bar -->
    <!-- <input id="find-data" class="search-table" type="search" placeholder="Search..."> -->

    <!-- Table para mapakita yung data na sinearch ng user -->
    <table class="search-result-table" id="search-result-table"></table>


    <!-- Table para ma display yung changes sa table. -->
    <table class="products-table" id="products-table">
        <!-- Dito mag di-display products -->
    </table>
    <!-- Previous page || Next page -->
</div>
<!-- Table Display End -->
<!-- ------------------------------------------------------------------------------------------------------- -->
<!-- Para sa modal windows jscript -->
<script type="text/javascript" src="modal.js"></script>
<script>
    $(document).ready(function() {
        loadTable();


        $(document).off('click', '.edit-btn').on('click', '.edit-btn', function() {
            var productId = $(this).data('id');


            // Fetch product details
            $.ajax({
                url: './main-content/fetch_single_product.php',
                type: 'GET',
                data: {
                    product_id: productId
                },
                success: function(data) {
                    var product = JSON.parse(data);

                    if (!product.error) {
                        // Populate the form fields with the fetched data

                        $('#product_id').val(product.product_id);
                        $('input[name="name"]').val(product.product_name);
                        $('select[name="category"]').val(product.category_id);
                        $('select[name="size"]').val(product.size);
                        $('select[name="gender"]').val(product.gender);
                        $('input[name="quantity"]').val(product.product_quantity);
                        $('input[name="price"]').val(product.price);


                        $('.sizes-container').css('display', 'none');
                        $('.gender-container').css('display', 'none');

                        $('.title-form-product').text('Edit Product');
                        $('#submit-btn').val('Update'); // Change button label to 'Update'
                        $('#add-product-modal').css('display', 'block');


                    } else {
                        alert('Error: ' + product.error);
                    }
                }

            });
        });

        // ADD and EDIT
        $('#input-form').off('submit').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();
            $('#submit-btn').prop('disabled', true);

            $.ajax({
                url: $('#submit-btn').val() === 'Update' ? './main-content/update_products.php' : './main-content/Products.php',
                type: 'POST',
                data: formData,
                success: function(response) {

                    $('#input-form')[0].reset();
                    $('#submit-btn').val('Add');
                    $('#cellSelect').prop('disabled', true);
                    $('#submit-btn').prop('disabled', false);

                    $('#add-product-modal').css('display', 'none');
                    loadTable();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#submit-btn').prop('disabled', false);
                }
            });
        });


        // Archive
        $(document).on('click', '.delete-btn', function() {
            var productId = $(this).data('id');

            $('#confirmation-modal').css('display', 'block');

            $('#confirm-archive').off('click').on('click', function() {
                $('#confirmation-modal').css('display', 'block');

                $.ajax({
                    url: './main-content/archive_products.php',
                    type: 'POST',
                    data: {
                        product_id: productId
                    },
                    success: function(response) {

                        console.log("Archive response: ", response);
                        loadTable();
                        $('#confirmation-modal').css('display', 'none');

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status == 404) {
                            alert('Error: File Not Found (404). Please check the server path.');
                        } else {
                            alert('Error: ' + errorThrown);
                        }
                    }
                });

            });

        });


        // Para sa modal window ng information button
        $(document).on('click', '.info-btn', function() {
            var productId = $(this).data('id');

            $.ajax({
                url: './main-content/get_product_info.php', // PHP file to handle the request
                type: 'POST',
                data: {
                    product_id: productId
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    if (data.success) {

                        $('#info-modal .product-info').html(`
                    <p>Product ID: ${data.product_id}</p>
                    <p>Date Added: ${data.date_added}</p>
                    <p>Gender: ${data.gender}</p>
                    <p>Sold Quantity: ${data.sold_quantity}</p>
                `);
                        $('#info-modal').css('display', 'block'); // Show the modal
                    } else {
                        alert('Failed to fetch product information.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });


        // data search stuff
        $('#find-data').keyup(function() {
            var input = $(this).val();
            if (input != '') {
                $.ajax({

                    url: './main-content/search_result_Products.php',
                    method: 'POST',
                    data: {
                        input: input
                    },
                    success: function(data) {

                        $('#search-result-table').html(data);
                        $('#search-result-table').css("display", "block");

                    }
                });
            } else {

                $('#search-result-table').css("display", "none");
            }
        });
    });

    function loadTable() {
        console.log("Why are you in the console? A bit sussy I ain't gonna lie brudder.");

        $.ajax({
            url: './main-content/fetch_products.php',
            type: 'GET',
            data: {
                _: new Date().getTime()
            },
            success: function(data) {
                $('#products-table').html(data); // Update table content
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#products-table').html('<tr><td colspan="10">Error loading table data</td></tr>'); // Error message
            }


        });
    }
</script>