<?php
include_once("vendor/autoload.php");
include_once("util.php");

$no_of_records_per_page = 30;

$total_pages = "SELECT COUNT(*) FROM packets";
$result= pg_query($conn, $total_pages);
$total_rows = pg_fetch_array($result)[0];
$total_pages = ceil($total_rows / $no_of_records_per_page);

$query = "SELECT * FROM packets ORDER BY packets.id desc";
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Panoptes</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/dashboard.css" rel="stylesheet">

    <script src='https://api.mapbox.com/mapbox-gl-js/v0.51.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v0.51.0/mapbox-gl.css' rel='stylesheet'/>

</head>

<body>
<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Panoptes</a>
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="#">Sign out</a>
        </li>
    </ul>

</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <span data-feather="home"></span>
                            Dashboard

                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="btn-toolbar mb-2 mb-md-0" style="padding-top: 20px;">

                <h1 class="h2">Dashboard
                    <small class="text-muted"><?php echo get_packet_count($conn); ?> packets collected</small>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0" style="position: absolute; right: 20px;">
                    <!-- Tool chooser -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button"
                                id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                style="margin-right: 10px;">
                            <i data-feather="server"></i> Tool
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Libprotoident</a>
                            <a class="dropdown-item" href="#">nDPI</a>
                        </div>
                    </div>
                    <!-- END Tool chooser -->
                    <!-- Protocol chooser -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button"
                                id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                style="margin-right: 10px;">
                            <i data-feather="activity"></i> Protocols
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php foreach (get_protocols($conn) as $protocol_name) {
                                echo "<a class=\"dropdown-item\" href=\"#\">" . $protocol_name . "</a>";
                            } ?>
                        </div>
                    </div>
                    <!-- END Protocol chooser -->
                    <!-- Country chooser -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button"
                                id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="flag"></i> Countries
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php foreach (get_countries($conn) as $country_code) {
                                echo "<a class=\"dropdown-item\" href=\"#\">" . $country_code . "</a>";
                            } ?>
                        </div>
                    </div>
                    <!-- END Country chooser -->
                </div>
            </div>

            <div class="my-4 w-100" id='map' style="height: 300px;"></div>

            <script type="text/javascript">
                mapboxgl.accessToken = 'pk.eyJ1IjoiYWdvdHRhcmRvIiwiYSI6ImlQNEYtcWcifQ.2GSJXDBB7oMK61Ey9Dtzww';
                var map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/dark-v9',
                    center: [-123.1, 49.25],
                    zoom: 5
                });
                map.addControl(new mapboxgl.NavigationControl());
            </script>
            <div>
                    <button type="button" class="paginate-prev btn btn-danger" style="float: left; background: black;">&laquo; Prev</button>
                    <button type="button"class="paginate-next btn btn-danger" style="float: right; background: black;">Next &raquo;</button>
                <div style="margin-bottom: 5%" align="center">
                    <ul class='pagination text-center'>
                        <?php if(!empty($total_pages)):
                            for($i=1; $i<=$total_pages; $i++):
                                if($i == 1):?>
                                    <li hidden class='active' id="<?php echo $i;?>"><a href='pagination.php?pageno=<?php echo $i;?>'><?php echo $i;?></a></li>
                                <?php else:?>
                                    <li hidden id="<?php echo $i;?>"><a href='pagination.php?pageno=<?php echo $i;?>'><?php echo $i;?></a></li>
                                <?php endif;?>
                            <?php endfor;
                        endif;?>
                </div>
                <div id="table-content" ></div>
            </div>
            <script type="text/javascript">
                map.on("load", function () {
                    /* Image: An image is loaded and added to the map. */
                    map.loadImage("https://i.imgur.com/MK4NUzI.png", function (error, image) {
                        if (error) throw error;
                        map.addImage("custom-marker", image);
                        /* Style layer: A style layer ties together the source and image and specifies how they are displayed on the map. */
                        map.addLayer({
                            id: "route",
                            type: "line",
                            /* Source: A data source specifies the geographic coordinate where the image marker gets placed. */
                            source: {
                                type: "geojson",
                                data: {
                                    type: "FeatureCollection",
                                    features: [<?php
                                        $mapRows = array();
                                        $query = pg_query($conn, $query);
                                        while ($row = pg_fetch_assoc($query)) {
                                            array_push($mapRows, $row); // adds the row to the map array
                                        }
                                        foreach ($mapRows as $row) {
                                            if ($row["source_longitude"] != 0.0 && $row["source_latitude"] != 0.0 &&
                                                $row["destination_longitude"] != 0.0 && $row["destination_latitude"])
                                                echo "{\"type\":\"Feature\",\"geometry\":{\"type\":\"LineString\",\"coordinates\":[[" . $row["source_longitude"] . "," . $row["source_latitude"] . "], [" . $row["destination_longitude"] . "," . $row["destination_latitude"] . "]]}},";
                                        }
                                        ?>
                                    ]
                                }
                            },
                            layout: {
                                "line-join": "round",
                                "line-cap": "round"
                            },
                            "paint": {
                                "line-color": "red",
                                "line-width": 1
                            }
                        });
                    });
                });
            </script>
        </main>
    </div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="/js/popper.min.js"></script>
<script src="/js/bootstrap.min.js"></script>

<!-- Icons -->
<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace()
</script>

<!-- Graphs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
<script>
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
            datasets: [{
                data: [15339, 21345, 18483, 24003, 23489, 24092, 12034],
                lineTension: 0,
                backgroundColor: 'transparent',
                borderColor: '#007bff',
                borderWidth: 4,
                pointBackgroundColor: '#007bff'
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false
                    }
                }]
            },
            legend: {
                display: false,
            }
        }
    });
</script>
</body>
<script>
    jQuery(document).ready(function() {
        jQuery("#table-content").load("pagination.php?pageno=1");

        let prev = $("button.paginate-prev");
        let next = $("button.paginate-next");

        next.click(function() {
            if ($('li.active').attr('id') == <?php echo $total_pages; ?>)
                return;
            $('li.active').removeClass('active').next().addClass('active');
            let pageNum = $('li.active').attr('id');
            jQuery("#table-content").load("pagination.php?pageno=" + pageNum);
        });
        prev.click(function() {
            if ($('li.active').attr('id') == 1)
                return;
            $('li.active').removeClass('active').prev().addClass('active');
            let pageNum = $('li.active').attr('id');
            jQuery("#table-content").load("pagination.php?pageno=" + pageNum);
        });
    });
</script>
</html>

