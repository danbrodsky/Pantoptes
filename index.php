<?php
include_once("vendor/autoload.php");
include_once("util.php");
// Uncomment the following line to debug from your machine (tells your local PHP instance where to find the DB).
// Comment it out before pushing to master!
// putenv("DATABASE_URL=postgres://eqdvefruwrhirc:57bbdd00b6b88481eebeeea8c11b52776d0ec96f9e3dd9a21d12f6d9376b9a62@ec2-54-83-27-162.compute-1.amazonaws.com:5432/dqt8lhkkbe5h7");
$conn = pg_connect(getenv("DATABASE_URL"));
$query = "SELECT tool, count(*) as number FROM packets GROUP BY tool";
$query = pg_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panoptes</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['application', 'number'],
                <?php
                while ($row = pg_fetch_assoc($query)) {
                    echo "['" . tool_id_to_string($row["tool"]) . "', " . $row["number"] . "],";
                }
                ?>
            ]);
            var options = {
                title: 'Percentage of Packet by Application',
                //is3D:true,
                pieHole: 0.4
            };
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
<div class="jumbotron text-center">
    <div class="container">
        <h1>Welcome to Panoptes.</h1>
        <p>This is the Data visualization portal for Panoptes.</p>
    </div>
</div>

<br/><br/>
<div style="width:900px;">
    <h3 align="center">Make Simple Pie Chart by Google Chart API with PHP Mysql</h3>
    <br/>
    <div id="piechart" style="width: 900px; height: 500px;"></div>
</div>
</body>
</html>
