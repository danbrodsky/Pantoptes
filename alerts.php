<?php
include_once("vendor/autoload.php");
include_once("util.php");
include_once("graphing_utils.php");

$query_alerts = "SELECT * FROM alerts_settings";
$result = pg_query($conn, $query_alerts);

?>

<?php include("include_head.php"); ?>

<div class="container-fluid">
    <div class="row">
        <?php include "sidemenu.php"; ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <h1 style="margin-top: 20px;">Alerts</h1>
            <?php if ($_GET["success"] == "true") { ?>
                <div class="alert alert-success" role="alert">
                    Alert created successfully.
                </div>
            <?php } ?>
            <a href="/add_alert.php">
                <button type="button" class="btn btn-primary btn-sm"><i data-feather="plus"></i> Add Alert</button>
            </a>
            <div class="card" style="margin-top: 20px;">
                <ul class="list-group list-group-flush">
                    <?php
                    while ($row = pg_fetch_assoc($result)) { ?>
                        <li class="list-group-item">
                            <?php
                            if ($row["source_country"] != "XX") {
                                $from = " from country code <code>".$row["source_country"]."</code>";
                            }
                            if ($row["source_ip"] != "") {
                                $from = " from IP address <code>".$row["source_ip"]."</code>";
                            }
                            if ($row["destination_country"] != "XX") {
                                $to = " to country code <code>".$row["destination_country"]."</code>";
                            }
                            if ($row["destination_ip"] != "") {
                                $to = " to IP address <code>".$row["destination_ip"]."</code>";
                            }
                            ?>
                            <h5>Alert #<?php echo $row["id"]; ?>: <?php echo $row["alert_name"]; ?></h5><a href="/delete_alert.php?id=<?php echo $row["id"]; ?>">
                                <button type="button" class="btn btn-danger btn-sm float-right"><i
                                            data-feather="trash-2"></i> Delete
                                </button>
                            </a>
                            <p>An email will be sent to <code><?php echo $row["email"]; ?></code> if
                                <strong><?php echo $row["kind"]; ?></strong> traffic is detected
                            <?php if (isset($from)) echo $from; ?> <?php if (isset($to)) echo $to; ?></p>
                        </li>
                    <?php } ?>
                </ul>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>

</body>
</html>

