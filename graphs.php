<html>
<body>
<?php
include_once("vendor/autoload.php");
include_once("util.php");
include_once("graphing_utils.php");
?>

<?php include("include_head.php"); ?>

<div class="container-fluid">
    <div class="row">
        <?php include "sidemenu.php"; ?>

        <main style="display:inline-block;" role="main" class="col-md-9 ml-sm-auto col-lg-10 col-lg-10 px-4">
            <canvas class="my-4 w-100 col-md-6" id="packet_chart" style="display:inline-block; float: left;"></canvas>
            <canvas class="my-4 w-100 col-md-6" id="srccountry_chart" style="display:inline-block;float: right;"></canvas>
            <span style="margin-left: 40%;" data-toggle="tooltip" data-placement="top" data-html="true" title="<b>Flow Protocol Chart</b><br>Click on protocol type above graph to filter it out">
                <i style= "width: 30px; height: 30px; color: grey;" data-feather="info" ></i>
            </span>
            <span style="float: right; margin-right: 10%;" data-toggle="tooltip" data-placement="top" data-html="true" title="<b>Packet Source Country Chart</b><br>Click on country abbreviation above graph to filter it out">
                <i style= "width: 30px; height: 30px; color: grey;" data-feather="info" ></i>
            </span>
        </main>
    </div>
    <div>
        <?php include "sidemenu.php"; ?>
    </div>
    <main style="width: 83%; height: 38%; float: right;" role="main">
        <div style="float: left;" data-toggle="tooltip" data-placement="right" data-html="true" title="<b>Flow Frequency Chart</b><br> Displays the number of unique flows over a given time interval">
            <i style= "width: 30px; height: 30px; color: grey;" data-feather="info" ></i>
        </div>
        <div class="dropdown" style="float: right; margin-right: 5%;">
            <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button"
                    id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="margin-right: 10px;">
                <i data-feather="clock"></i> Time range
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a id="" class="time dropdown-item active" href="" >All time</a>
                <a id="h" class="time dropdown-item" href="" >Last hour</a>
                <a id="d" class="time dropdown-item" href="" >Last day</a>
                <a id="w" class="time dropdown-item" href="" >Last week</a>
                <a id="M" class="time dropdown-item" href="" >Last month</a>
            </div>
        </div>
        <br>
        <br>
        <canvas style="display: inline-block;" id="time_chart"></canvas>
    </main>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
<?php
$traffic_types = num_packets_types($conn);
$types = json_encode(array_column($traffic_types, "packet_type"), JSON_PRETTY_PRINT);
$counts = json_encode(array_column($traffic_types, "cnt"), JSON_PRETTY_PRINT);
$colors = array();
for ($i = 0; $i < count($traffic_types); $i++) {
    $r = rand(0,200);
    $g = rand(0,200);
    $b = rand(0,200);
    array_push($colors, "rgba(".$r.", ".$g.", ".$b.", 1)");
}
$colors = json_encode($colors, JSON_PRETTY_PRINT);
?>

<script>
    var ctx = document.getElementById("packet_chart");
    // And for a doughnut chart
    var data = {
        datasets: [{
            data: <?php echo $counts; ?>,
            backgroundColor: <?php echo $colors; ?>
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: <?php echo $types; ?>
    };
    var myDoughnutChart = new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: []
    });
</script>

<?php
$country_types = num_countries($conn);
$types = json_encode(array_column($country_types, "source_country"), JSON_PRETTY_PRINT);
$country_counts = json_encode(array_column($country_types, "cnt"), JSON_PRETTY_PRINT);
$country_colors = array();
for ($i = 0; $i < count($country_types); $i++) {
    $r = rand(0,200);
    $g = rand(0,200);
    $b = rand(0,200);
    array_push($country_colors, "rgba(".$r.", ".$g.", ".$b.", 1)");
}
$country_colors = json_encode($country_colors, JSON_PRETTY_PRINT);
?>

<script>
    var ctx = document.getElementById("srccountry_chart");
    // And for a doughnut chart
    var data = {
        datasets: [{
            data: <?php echo $country_counts; ?>,
            backgroundColor: <?php echo $country_colors; ?>
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: <?php echo $types; ?>
    };
    var myDoughnutChart = new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: []
    });
</script>

<?php
$times = packet_times($conn);
$types = json_encode(array_column($times, "timestamp"), JSON_PRETTY_PRINT);
$packet_counts = json_encode(array_column($times, "cnt"), JSON_PRETTY_PRINT);
?>
<script>
    var ctx = document.getElementById("time_chart").getContext('2d');


    var timeFormat = 'MM/DD HH:mm';

    function newDateString(epoch) {
        return moment(epoch*1000).format(timeFormat);
    }

    <?php echo "var times = ". $types . ";\n"; ?>

    times.forEach((item, index) => {
        times[index] = newDateString(times[index]);
    });


    var timeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: times,
            datasets: [{
                backgroundColor: 'rgb(255, 99, 132,0.5)',
                borderColor: 'rgb(255, 99, 132)',
                fill: true,
                data: <?php echo $packet_counts; ?>,
            }]
        },
        options: {
            title: {
                text: 'Flow Frequency'
            },
            maintainAspectRatio: false,
            legend: false,
            scales: {
                xAxes: [{
                    type: 'time',
                    time: {
                        min: '',
                        parser: timeFormat,
                        tooltipFormat: 'll HH:mm'
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Time'
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: '# Flows Detected'
                    }
                }]
            }
        }
    });
    $(document).ready(function() {
        $(".time").on('click', function (e) {
            e.preventDefault();
            if ($(this).attr('id') === ''){
                timeChart.config.options.scales.xAxes[0].time.min = undefined;
                timeChart.config.options.scales.xAxes[0].time.max = undefined;
            }
            else {
                timeChart.config.options.scales.xAxes[0].time.min = moment().add(-1, $(this).attr('id'));
            }
            timeChart.update();
        });
    });
</script>
</body>
<script>
    $('[data-toggle="tooltip"]').tooltip({
        trigger : 'click'
    });
</script>
</html>

