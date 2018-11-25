<html>
<body>
<?php
include_once("vendor/autoload.php");
include_once("util.php");

$no_of_records_per_page = 30;

$total_pages = "SELECT COUNT(*) FROM packets";
$result = pg_query($conn, $total_pages);
$total_rows = pg_fetch_array($result)[0];
$total_pages = ceil($total_rows / $no_of_records_per_page);
?>

<?php include("include_head.php"); ?>
<div class="container-fluid">
    <div class="row">

        <?php include "sidemenu.php"; ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="btn-toolbar mb-2 mb-md-0" style="padding-top: 20px;">

                <h1 class="h2">Dashboard
                    <small class="text-muted"><?php echo get_packet_count($conn); ?> flows identified</small>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0" style="position: absolute; right: 20px;">
                    <!-- Tool chooser -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button"
                                id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                style="margin-right: 10px;">
                            <i data-feather="server"></i> Tool (<?php echo num_tools($conn); ?>)
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a id="" class="tool dropdown-item" href="">All Tools</a>
                            <div class="dropdown-divider"></div>
                            <a id="0" class="tool dropdown-item" href="">Libprotoident</a>
                            <a id="1" class="tool dropdown-item" href="">nDPI</a>
                        </div>
                    </div>
                    <!-- END Tool chooser -->
                    <!-- Protocol chooser -->
                    <?php
                    $protocols = get_protocols($conn);
                    ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button"
                                id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                style="margin-right: 10px;">
                            <i data-feather="activity"></i> Protocols (<?php echo count($protocols); ?>)
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a id="" class="protocol dropdown-item" href="">All Protocols</a>
                            <div class="dropdown-divider"></div>
                            <?php foreach ($protocols as $protocol_name) {
                                echo "<a id=\"$protocol_name\" class=\"protocol dropdown-item\" href=\"\">" . $protocol_name . "</a>";
                            } ?>
                        </div>
                    </div>
                    <!-- END Protocol chooser -->
                    <!-- Country chooser -->
                    <?php
                    $countries = get_countries($conn);
                    ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button"
                                id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="flag"></i> Countries (<?php echo count($countries); ?>)
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a id="" class="country dropdown-item" href="">All Countries</a>
                            <div class="dropdown-divider"></div>
                            <?php foreach ($countries as $country_code) {
                                echo "<a id=\"$country_code\" class=\"country dropdown-item\" href=\"\">" . $country_code . "</a>";
                            } ?>

                        </div>
                    </div>
                    <!-- END Country chooser -->
                </div>
            </div>

            <div class="my-4 w-100" id='map' style="height: 500px;"></div>

            <script type="text/javascript">
                mapboxgl.accessToken = 'pk.eyJ1IjoiYWdvdHRhcmRvIiwiYSI6ImlQNEYtcWcifQ.2GSJXDBB7oMK61Ey9Dtzww';
                var map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/dark-v9',
                    center: [-117.4, 47.6],
                    zoom: 4,
                    bearing: 145,
                    pitch: 90
                });
                map.addControl(new mapboxgl.NavigationControl());
            </script>
            <div class="row">
                <div class="col-sm">
                    <button type="button" class="paginate-prev btn btn-outline-secondary" style="float: left;">
                        &laquo;
                        Prev
                    </button>
                </div>
                <div class="col-sm text-center">
                    <h5>Page <span id="pageno">1</span> of <?php echo $total_pages; ?></h5>
                </div>
                <div class="col-sm align-content-end">
                    <button type="button" class="paginate-next btn btn-outline-secondary" style="float: right;">Next
                        &raquo;
                    </button>
                </div>
                <div style="margin-top: 0%; margin-bottom: 5%" align="center">
                    <ul class='pagination text-center'>
                        <?php if (!empty($total_pages)):
                            for ($i = 1; $i <= $total_pages; $i++):
                                if ($i == 1):?>
                                    <li hidden class='active' id="<?php echo $i; ?>"><a
                                                href=''><?php echo $i; ?></a>
                                    </li>
                                <?php else: ?>
                                    <li hidden id="<?php echo $i; ?>"><a href=''><?php echo $i; ?></a></li>
                                <?php endif; ?>
                            <?php endfor;
                        endif; ?>
                </div>
            </div>
            <div id="table-content"></div>
            <script type="text/javascript">
                map.on("load", function () {
                    /* Image: An image is loaded and added to the map. */
                    map.loadImage("https://i.imgur.com/H2vUTYX.png", function (error, image) {
                        if (error) throw error;
                        map.addImage("custom-marker", image);
                        /* Style layer: A style layer ties together the source and image and specifies how they are displayed on the map. */
                        map.addSource("packet-info", {
                            type: "geojson",
                            data: {
                                type: "FeatureCollection",
                                features: []
                            }
                        });
                        map.addLayer({
                            id: "locations",
                            type: "symbol",
                            source: "packet-info",
                            layout: {
                                "icon-image": "{icon}",
                                "icon-allow-overlap": true
                            },
                            "filter": ["==", "$type", "Point"]
                        });
                        map.addLayer({
                            id: "route",
                            type: "line",
                            /* Source: A data source specifies the geographic coordinate where the image marker gets placed. */
                            source: "packet-info",
                            layout: {
                                "line-join": "round",
                                "line-cap": "round"
                            },
                            "paint": {
                                "line-color": "red",
                                "line-width": 1
                            },
                            "filter": ["==", "$type", "LineString"]
                        });

                        var popup = new mapboxgl.Popup({
                            closeButton: false,
                            closeOnClick: true,
                            className: 'popup'
                        });

                        // Change the cursor to a pointer when the mouse is over the places layer.
                        map.on('mouseenter', 'locations', function (e) {
                            map.getCanvas().style.cursor = 'pointer';

                            var coordinates = e.features[0].geometry.coordinates.slice();
                            var description = e.features[0].properties.description;

                            // Ensure that if the map is zoomed out such that multiple
                            // copies of the feature are visible, the popup appears
                            // over the copy being pointed to.
                            while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                                coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                            }

                            popup
                                .setLngLat(coordinates)
                                .setHTML(description)
                                .addTo(map);
                        });

                        map.on('mouseleave', 'locations', function () {
                            map.getCanvas().style.cursor = '';
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
</body>
<script>
    $(document).ready(function () {
        $("#table-content").load("pagination.php");

        let prev = $("button.paginate-prev");
        let next = $("button.paginate-next");

        next.click(function () {
            if ($('li.active').attr('id') == <?php echo $total_pages; ?> || $('#table-content tr').length < 30)
                return;
            $('li.active').removeClass('active').next().addClass('active');
            updateTable()
        });
        prev.click(function () {
            if ($('li.active').attr('id') == 1)
                return;
            $('li.active').removeClass('active').prev().addClass('active');
            updateTable()
        });

        $(".protocol").on('click', function (e) {
            changeAttr.call(this, '.protocol', e);
        });
        $(".country").on('click', function (e) {
            changeAttr.call(this, '.country', e);
        });
        $(".tool").on('click', function (e) {
            changeAttr.call(this, '.tool', e);
        });

        function changeAttr(attr, e) {
            e.preventDefault();
            $(attr).removeClass('active');
            $(this).addClass('active');
            $('ul.pagination > li.active').removeClass('active');
            $('ul.pagination > li:first').addClass('active');
            updateTable()
        }

        function updateTable() {
            let pageNum = $.trim($('ul.pagination > li.active').attr('id'));
            let protocol = $.trim($('.protocol.active').attr('id'));
            let country = $.trim($('.country.active').attr('id'));
            let tool = $.trim($('.tool.active').attr('id'));
            $("#table-content").load("pagination.php?pageno=" + pageNum + "&protocol=" + protocol + "&country=" + country + "&tool=" + tool);
        }
    });

</script>
<style>
    .mapboxgl-popup-content {
        color: #ffffff;
        background-color: #000000;
        opacity: 0.8;
    }
    .mapboxgl-popup-anchor-bottom .mapboxgl-popup-tip, .mapboxgl-popup-anchor-top .mapboxgl-popup-tip {
        border-bottom-color: #000000;
        border-top-color: #000000;
    }
</style>
</html>

