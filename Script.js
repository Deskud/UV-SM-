
// Pag pinindot yung side bar magkakaroon ng highlight. If ni reload yung page mag s-stay parin doon sa pinendot na link yung highlight.
$(document).ready(function() {

    function setActivePage(target, shouldUpdateURL = true) {
        // Remove 'activePage' from all links, then add it to the current one
        $('.Sidebar-links a').removeClass('activePage');
        $('.Sidebar-links a[data-target="' + target + '"]').addClass('activePage');

        // Load the content into the container
        $('#Main-container').load(target);

        // Saves the recently clicked page to the local storage
        localStorage.setItem('activePage', target);

        if (shouldUpdateURL) {
            var page = target.replace('./main-content/', '').replace('.php', '');
            history.pushState(null, null, '?page=' + page);
        }
    }

    $('.Sidebar-links a').on('click', function(e) {
        e.preventDefault();  // Prevent default link behavior

        const target = $(this).data('target');
        setActivePage(target);  // Set the active page and load content
    });

    // Load content from URL on page reload
    function loadContentFromURL() {
        const params = new URLSearchParams(window.location.search);
        let page = params.get('page') || 'Dashboard'; // Default to 'Dashboard'

        let target = './main-content/' + page + '.php';
        setActivePage(target, false);  // 
    }

   
    window.onpopstate = function() {
        loadContentFromURL();  // Reload content based on current URL
    };

    const savedPage = localStorage.getItem('activePage');
    if (savedPage) {
        setActivePage(savedPage, false); // Load the saved page without updating the URL
    } else {
        loadContentFromURL(); // Load based on URL parameter (or default Dashboard)
    }

});



