$(document).ready(function() {
    $('.Sidebar-links a').on('click', function() {

        $('.Sidebar-links a').removeClass('activePage');
        $(this).addClass('activePage');

        const target = $(this).data('target');
        $('#Main-container').load(target);
    });
});

$(document).ready(function() {
 
    // var trigger = $('#Sidebar-container ul ul li a'),
    var trigger = $('.page-nav'),
        container = $('#Main-container');

    function loadContentFromURL() {
        var params = new URLSearchParams(window.location.search);
        var page = params.get('page'); // Get the 'page' parameter from URL

        if (!page) {
            page = 'Dashboard'; 
        }

    

        container.load('./main-content/' + page + '.php');
    }
    

    // Call this function on page load to set initial content
    loadContentFromURL();

    trigger.on('click', function(e) {
        e.preventDefault(); // Prevent default link behavior

        var $this = $(this),
            target = $this.data('target');
        console.log(target);

        // Extract page name from the target URL (e.g., Products)
        var page = target.replace('./main-content/', '').replace('.php', '');

        // Load the content dynamically
        container.load(target);

        // Change the URL using query parameters without reloading the page
        history.pushState(null, null, '?page=' + page);


        return false;
    });

    // Handle the back/forward button navigation
    window.onpopstate = function() {
        loadContentFromURL(); // Reload content based on the current URL
    };
});


// LOAD TABLES FOR PRODUCTS

function loadTable() {
    console.log("Why are you in the console? A bit sussy i aint gonna lie brudder.");

    $.ajax({
        url: './main-content/fetch_products.php',
        type: 'GET',
        data: {
            _: new Date().getTime()
        },
        success: function(data) {
            $('#products-table').html(data); // Update the table content
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('#products-table').html('<tr><td colspan="10">Error loading table data</td></tr>'); // Error message
        }
    });
}

// NOTIFICATIONS


