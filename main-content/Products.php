<?php
require "../dbconnection.php";
include '../session_check.php';
checkAccess(ADMIN); // Ensure only admins can access this page

// ADD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve posted data
    $product_name = $_POST['name'];
    $product_sizes = $_POST['size'];
    $product_gender = $_POST['gender'];
    // $product_cell 
    $product_quantity = $_POST['quantity'];
    $product_price = $_POST['price'];

    if (!empty($product_sizes)) {
        foreach ($product_sizes as $size) {
            // Insert a row for each selected size and set cell_num to NULL
            $insert_new_product = "INSERT INTO products (category_id, size, gender, product_quantity, price, cell_num) 
                                   VALUES ('$product_name', '$size', '$product_gender', '$product_quantity', '$product_price', NULL)";

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
<h2 style="text-align: center;">Add Products</h2>
<!-- Add Products Modal -->
<div id="add-product-modal" class="modal">
    <div class="modal-content">

        <span class="close-modal"><i id="close-icon" class="fa-solid fa-xmark"></i></span>


        <!-- Add Products Part -->
        <h3 class="title-form">Add Product</h3>
        <form action="" method="POST" enctype="multipart/form-data" id="input-form">
            <input type="hidden" name="product_id" id="product_id" value="">
            <h3 style="color: #0454ac;">Uniform Type</h3>
            <select class="select-uniform-name" name="name" required>
                <option value="" disabled selected>...</option>
                <option value="1">School Uniform</option>
                <option value="2">PE Uniform</option>
                <option value="3">Washday Shirt</option>
            </select>

            <!-- <select class="select-uniform-size" name="size" required>
                <option value="" disabled selected>Uniform Size</option>
                <option value="1">Small</option>
                <option value="2">Medium</option>
                <option value="3">Large</option>
                <option value="4">Extra Large</option>
            </select> -->
            <h3 style="color: #0454ac;">Sizes</h3>
            <div class="sizes-container">
                <input type="checkbox" value="1" name="size[]">Small
                <input type="checkbox" value="2" name="size[]">Medium
                <input type="checkbox" value="3" name="size[]">Large
                <input type="checkbox" value="4" name="size[]">Extra Large
            </div>
            <h3 style="color: #0454ac;">Gender</h3>
            <select class="select-gender" name="gender" required>
                <option value="" disabled selected>Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="unisex">Unisex</option>
            </select>

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
<div id=confirmation-modal class="modal">
    <div class="modal-confirm-content">
        <h3 class="title-form">Confirm Action</h3>
        <p>Are you sure you want to archive this product?</p>
        <button type="button" id="confirm-archive" class="confirm-btn">Yes</button>
        <button type="button" id="cancel-archive" class="cancel-btn">No</button>
    </div>
</div>


<!-- Button for add product modal window -->
<div class="open-product-modal">
    <button type="button" id="add-product-btn"><i id="icon-plus" class="fa-solid fa-plus"></i>Add Product</button>

</div>




<!-- Start Table Display -->

<div class=" products-table-container">




    <!-- Search bar -->
    <input id="find-data" class="search-table" type="search" placeholder="Search...">

    <!-- Table para mapakita yung data na sinearch ng user -->
    <table class="search-result-table" id="search-result-table"></table>


    <!-- Table para ma display yung changes sa table. -->
    <table class="products-table" id="products-table">



    </table>
    <!-- Previous page || Next page -->
</div>
<!-- Table Display End -->

<!-- 
    AJAX SCRIPTS

    EVENT DELEGATION WOW! COOL CONCEPT NOICE
     https://www.youtube.com/watch?v=aZ3JWv0ofuA

             gr8 video
             
                  
-->
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
                        $('select[name="name"]').val(product.product_name);
                        $('select[name="size"]').val(product.size);
                        $('select[name="gender"]').val(product.gender);
                        $('input[name="quantity"]').val(product.product_quantity);
                        $('input[name="price"]').val(product.price);

                        $('#submit-btn').val('Update'); // Change button label to 'Update'
                        $('#add-product-modal').css('display', 'block');

                    } else {
                        alert('Error: ' + product.error);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error fetching product details: ' + errorThrown);
                }
            });
        });

        // ADD and EDIT
        $('#input-form').off('submit').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();
            // Disable the submit button to prevent multiple submissions
            $('#submit-btn').prop('disabled', true);

            $.ajax({
                url: $('#submit-btn').val() === 'Update' ? './main-content/update_products.php' : './main-content/Products.php',
                type: 'POST',
                data: formData,
                success: function(response) {

                    $('#input-form')[0].reset();
                    $('#submit-btn').val('Add');
                    $('#cellSelect').prop('disabled', true);
                    loadTable();

                    $('#submit-btn').prop('disabled', false);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Re-enable the submit button
                    $('#submit-btn').prop('disabled', false);
                }
            });
        });
        // Cell number add
        $(document).on('click', '.cell-btn', function() {
            $('#add-cell-num').css('display', 'block');
            loadCellNumbers()

            $('#submit-cell').off('click').on('click', function() {
                var selectedCell = $('#cellSelect').val();
                if (selectedCell) {
                    $.ajax({
                        url: './main-content/update-cell-num.php',
                        method: 'POST',
                        data: {
                            product_id: productId,
                            cell_num: selectedCell
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Cell number updated successfully');
                                $('#add-cell-num').css('display', 'none');
                                loadTable(); // Refresh the product table
                            } else {
                                alert('Error updating cell number: ' + response.error);
                            }
                        },
                        error: function() {
                            alert('Error');
                        }
                    })
                }
            });
        })

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
        
        // Function to load the product table
        // function loadTable() {
        //     console.log("Why are you in the console? A bit sussy i aint gonna lie brudder.");

        //     $.ajax({
        //         url: './main-content/fetch_products.php',
        //         type: 'GET',
        //         data: {
        //             _: new Date().getTime()
        //         },
        //         success: function(data) {
        //             $('#products-table').html(data); // Update the table content
        //         },
        //         error: function(jqXHR, textStatus, errorThrown) {
        //             $('#products-table').html('<tr><td colspan="10">Error loading table data</td></tr>'); // Error message
        //         }
        //     });
        // }
    });
</script>