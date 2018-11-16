
<?php
 $link = mysqli_connect("db_addr", "db_user", "db_password", "db_name");
 if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
    }
 $query = "SELECT Type, count(*) as number FROM table_name GROUP BY Type";
 $result = mysqli_query($link, $query);
 ?>

 <!DOCTYPE html>
 <html>
      <head>
           <title>Panoptes</title>
           <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
           <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
           <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
           <link rel="stylesheet" type="text/css" href="/static/main.css" />
           <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

           <script type="text/javascript">
           google.charts.load('current', {'packages':['corechart']});
           google.charts.setOnLoadCallback(drawChart);
           function drawChart()
           {
                var data = google.visualization.arrayToDataTable([
                          ['application', 'number'],
                          <?php
                          while($row = mysqli_fetch_array($result))
                          {
                               echo "['".$row["application"]."', ".$row["number"]."],";
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

    <br /><br />
    <div style="width:900px;">
        <h3 align="center">Make Simple Pie Chart by Google Chart API with PHP Mysql</h3>
        <br />
        <div id="piechart" style="width: 900px; height: 500px;"></div>
    </div>
</body>
</html>
