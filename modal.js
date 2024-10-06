//MODAL FOR ADDING PRODUCTS


$(document).ready(function () {
    var modalProduct = $('#add-product-modal'); // Modal window para sa add product
    var modalProdInfo = $('#info-modal'); //Para sa info button sa product
    var modalConfirm = $('#confirmation-modal');//Modal confirmation para sa archive/orders
    var modalOrders = $('#proceed-order-modal');//Modal winodw para sa orders
    var modalUpdateorder = $('#update-orders');
    var modalReturn = $('#return');
    var closeModal = $('.close-modal'); // Get the <span> element that closes the modal
   
    //Bubukas modal window pag pinendot yung button (ADD PRODUCT)
    $('#add-product-btn').on('click', function () {
        console.log("click click");
        modalProduct.css('display', 'block');
    });

    //Pag pinido tyung font awesome icon dapat mag c-close yung modal window (ADD PROD)
    closeModal.on('click', function () {
        modalProduct.css('display', 'none');
        modalOrders.css('display', 'none');
        modalProdInfo.css('display', 'none');
    });




    //Pag pinidot yung background mag c-close yung window. (PRODUCT at ARCHIVE)
    $(window).on('click', function (event) {

        if ($(event.target).is(modalProduct)) {
            resetModal();
            modalProduct.css('display', 'none');
        }

        // if ($(event.target).is(modalConfirm)) {
        //     modalConfirm.css('display', 'none');
        // }

        if ($(event.target).is(modalOrders)) {
            modalOrders.css('display', 'none');
        }
        


    });
    

    // FOR closing ng update at finish order modal pero di mag 
    // c-close yung main modal window (proceed-order-modal) 

    modalReturn.on('click', function () {
        console.log('click');
        modalUpdateorder.css('display', 'none');
    });



    // FOR ARCHIVING MODAL WINDOW

    $('#cancel-archive').on('click', function () {
        modalConfirm.css('display', 'none');

    });
    // Finish code printer cancel. Tapos mag c-close modal pag pinress "No".
    $('#cancel-print').on('click', function (){
        modalConfirm.css('display', 'none');
    });

});
// Pag cinlick ko yung edit tapos di ako naglagay babalik sa add product function.
function resetModal() {
    $('#input-form')[0].reset(); // Reset the form
    $('#submit-btn').val('Add'); // Set button to 'Add'
    $('#cellSelect').prop('disabled', false); // Enable the cell number select
    $('#add-product-modal').css('display', 'block'); // Open the modal


}

//Para ito sa cell num same siya sa resetModal except dito para naman sa add produc tbutton
$('#add-product-btn').on('click', function () {
    $('.title-form-product').text('Add Products');
    $('#input-form')[0].reset();
    $('input[name="size[]"]').prop('disabled', false);  // Enable checkboxes
    $('.gender-container').css('display', 'block');
    $('.sizes-container').css('display', 'block'); // Show checkboxes
    $('#submit-btn').val('Add');
    $('#add-product-modal').css('display', 'block');
});



