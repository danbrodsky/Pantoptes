<?php
include_once("vendor/autoload.php");
include_once("util.php");
// Uncomment the following line to debug from your machine (tells your local PHP instance where to find the DB).
// Comment it out before pushing to master!
putenv("DATABASE_URL=postgres://eqdvefruwrhirc:57bbdd00b6b88481eebeeea8c11b52776d0ec96f9e3dd9a21d12f6d9376b9a62@ec2-54-83-27-162.compute-1.amazonaws.com:5432/dqt8lhkkbe5h7");
$conn = pg_connect(getenv("DATABASE_URL"));
$query = "SELECT * FROM packets LIMIT 500";
$query = pg_query($conn, $query);
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

</head>

<body>
<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Panoptes</a>
    <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
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
                            Dashboard <span class="sr-only">(current)</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="btn-toolbar mb-2 mb-md-0">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0" style="position: absolute; right: 0;">
                    <div class="btn-group mr-2">
                        <button class="btn btn-sm btn-outline-secondary">Share</button>
                        <button class="btn btn-sm btn-outline-secondary">Export</button>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
                        <span data-feather="calendar"></span>
                        This week
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>

                        <th>Packet ID</th>
                        <th>Tool</th>
                        <th>Packet Type</th>
                        <th>Source IP</th>
                        <th>Destination IP</th>
                        <th>Source Country</th>
                        <th>Destination Country</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = pg_fetch_assoc($query)) {
                        echo "<tr>";
                        echo "<td>".$row["id"]."</td>";
                        echo "<td>".tool_id_to_string($row["tool"])."</td>";
                        echo "<td>".$row["packet_type"]."</td>";
                        echo "<td>".$row["source_ip"]."</td>";
                        echo "<td>".$row["destination_ip"]."</td>";
                        echo "<td>".$row["source_country"]."</td>";
                        echo "<td>".$row["destination_country"]."</td>";
                        echo "</tr>";
                    }

                    ?>
                    </tbody>
                </table>
            </div>
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
</html>

