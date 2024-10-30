<h3 class="title-form">Dashboard</h3>
<hr>

<!-- Chart -->
<div id="products-chart"></div>




<!-- <hr> -->
<div class="dash-container">
    <!-- Dito lalabas yung mga update dynamically -->
</div>



<!-- Para sa charts -->
<script src="./jquery/jquery.canvasjs.min.js"></script>
<script>
    // ito na yung ajax polling 
    // every 5 seconds nag re-request ang server.
    //  Literal na pseudo real time ang nangyayari. 
    // I guess recommendation sa future ay gumamit ng websockets kaysa dito.
    // Late ko na din kasi nalaman may ganto pala. Big bruh moment.
    // Dinagdagan ko ng chart

    $(document).ready(function() {
        productChart();
        dashPoll();


    });

    function dashPoll() {

        $.ajax({

            url: './server/dashboard_poll.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('.dash-container').empty();

                //How many cell to display
                for (let cell = 1; cell <= 12; cell++) {
                    let cellData = response[cell] || {
                        product_name: 'No Data',
                        size_name: 'No Data',
                    };

                    let dashContent = `
                        <div class="unit-containers">
                            <div class="unit-number">
                                <h2>UNIT ${cell}</h2>
                            </div>
                            <p>${cellData.product_name}</p>
                            <p>Size: ${cellData.size_name}</p>
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
    // Stocks chart
    function productChart() {
        if ($('#products-chart').length) {
            $.ajax({
                url: './server/products_chart.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var dataPoints = [];

                    response.forEach(function(product) {
                        dataPoints.push({
                            label: "UNIT " + product.unit_num,

                            // Coverts the data to number. 
                            //JSON kasi yung data and na interprate na string yung data kahit int ang data type niya sa db
                            y: Number(product.product_quantity)
                        });
                    });
                    var chart = new CanvasJS.Chart("products-chart", {
                        animationEnabled: true,
                        title: {
                            text: "Product Stocks",
                            fontFamily: "Arial",
                        },
                        axisY: {
                            title: "Stock Quantity",
                        },
                        axisX: {
                            title: "Unit Numbers",

                        },
                        data: [{
                            type: "column",

                            dataPoints: dataPoints
                        }]
                    });

                    // Render the chart
                    chart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching product data: ' + error);
                }
            });
        }
    }
    setInterval(dashPoll, 10000); //10 seconds
    setInterval(productChart, 600000); //10 minutes
</script>