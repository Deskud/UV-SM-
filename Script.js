
// Pag pinindot yung side bar magkakaroon ng highlight. If ni reload yung page mag s-stay parin doon sa pinendot na link yung highlight.
$(document).ready(function() {

    function setActivePage(target) {
        $('.Sidebar-links a').removeClass('activePage');

        $('.Sidebar-links a[data-target="' + target + '"]').addClass('activePage');

        $('#Main-container').load(target);

        localStorage.setItem('activePage', target);
    }

 
    $('.Sidebar-links a').on('click', function() {
        const target = $(this).data('target');

        // Set the active page and load the content
        setActivePage(target);
    });

    const savedPage = localStorage.getItem('activePage');

    if (savedPage) {
        setActivePage(savedPage);
    } else {
        setActivePage('./main-content/Dashboard.php');
    }
});


// Dito yung part para sa walang reloading pag nag lilipat ng pages. (Single Page Application)
$(document).ready(function() {
 
    var trigger = $('.page-nav'),
        container = $('#Main-container');

    function loadContentFromURL() {
        var params = new URLSearchParams(window.location.search);
        var page = params.get('page');

        if (!page) {
            page = 'Dashboard'; 
        }

    

        container.load('./main-content/' + page + '.php');
    }
    

    // Mag lo-load content nung page
    loadContentFromURL();

    trigger.on('click', function(e) {
        e.preventDefault(); // Prevent default link behavior

        var $this = $(this),
            target = $this.data('target');
        console.log(target);

    
        var page = target.replace('./main-content/', '').replace('.php', '');

        container.load(target);

        history.pushState(null, null, '?page=' + page);


        return false;
    });

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


