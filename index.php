
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
           <br /><br />
           <div style="width:900px;">
                <h3 align="center">Make Simple Pie Chart by Google Chart API with PHP Mysql</h3>
                <br />
                <div id="piechart" style="width: 900px; height: 500px;"></div>
           </div>
      </body>
 </html>
