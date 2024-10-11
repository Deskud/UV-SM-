<h3 class="title-form">Dashboard</h3>
<hr>
<div class="dash-container">
    <!-- Dito lalabas yung mga update dynbamically -->
</div>

<script>
    // ito na yung ajax polling 
    // every 5 seconds nag re-request ang server.
    //  Literal na pseudo real time ang nangyayari. 
    // I guess recommendation sa future ay gumamit ng websockets kaysa dito.
    // Late ko na din kasi nalaman may ganto pala. Big bruh moment.

    function dashPoll() {
        $.ajax({

            url: './server/dashboard_poll.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('.dash-container').empty();

                for (let cell = 1; cell <= 24; cell++) {
                    let cellData = response[cell] || {
                        product_name: 'No Data',
                        size_name: 'No Data',
                        quantity: 0
                    };

                    let dashContent = `
                        <div class="unit-containers">
                            <div class="unit-number">
                                <h2>UNIT ${cell}</h2>
                            </div>
                            <svg class="cell-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                <path d="M211.8 0c7.8 0 14.3 5.7 16.7 13.2C240.8 51.9 277.1 80 320 80s79.2-28.1 91.5-66.8C413.9 5.7 420.4 0 428.2 0l12.6 0c22.5 0 44.2 7.9 61.5 22.3L628.5 127.4c6.6 5.5 10.7 13.5 11.4 22.1s-2.1 17.1-7.8 23.6l-56 64c-11.4 13.1-31.2 14.6-44.6 3.5L480 197.7 480 448c0 35.3-28.7 64-64 64l-192 0c-35.3 0-64-28.7-64-64l0-250.3-51.5 42.9c-13.3 11.1-33.1 9.6-44.6-3.5l-56-64c-5.7-6.5-8.5-15-7.8-23.6s4.8-16.6 11.4-22.1L137.7 22.3C155 7.9 176.7 0 199.2 0l12.6 0z"></path>
                            </svg>
                            <p>${cellData.product_name}</p>
                            <p>Size: ${cellData.size_name}</p>
                            <p>Quantity: ${cellData.quantity}</p>
                        </div>
                           `;
                    $('.dash-container').append(dashContent);
                }
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    }
    setInterval(dashPoll, 3000);

    dashPoll();
</script>