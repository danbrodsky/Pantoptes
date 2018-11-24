<?php
include_once("vendor/autoload.php");
include_once("util.php");
include_once("graphing_utils.php");

$shouldWarn = false;

if (isset($_POST["set"])) {
    $alert_name = pg_escape_string($_POST["alert_name"]);
    $sourceIP = pg_escape_string($_POST["sourceIP"]);
    $destinationIP = pg_escape_string($_POST["destinationIP"]);
    $kind = pg_escape_string($_POST["kind"]);
    $tool = pg_escape_string($_POST["tool"]);
    $sourceCountry = pg_escape_string($_POST["source_country"]);
    $destinationCountry = pg_escape_string($_POST["destination_country"]);

    if (!isset($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $shouldWarn = true;
    } else {
        $email = pg_escape_string($_POST["email"]);
        $toolID = tool_string_to_id($tool);
        if ($sourceCountry == "Any Country") {
            $sourceCountry = "XX";
        }
        if ($destinationCountry == "Any Country") {
            $destinationCountry = "XX";
        }
        $addQuery = "INSERT INTO \"public\".\"alerts_settings\" (\"id\", \"alert_name\", \"kind\", \"source_ip\", \"destination_ip\", \"tool\", \"source_country\", \"destination_country\", \"email\") VALUES (DEFAULT, '" . $alert_name . "', '" . $kind . "', '" . $sourceIP . "', '" . $destinationIP . "', " . $toolID . ", '" . $sourceCountry . "', '" . $destinationCountry . "', '" . $email . "')";
        if (pg_query($conn, $addQuery)) {
            header("Location: /alerts.php?success=true");
        } else {
            $error = pg_last_error($conn);
            echo $error;
            die();
        }

    }
}

?>

<?php include("include_head.php"); ?>

<div class="container-fluid">
    <div class="row">

        <?php include "sidemenu.php"; ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <h1 style="margin-top: 20px;">Add alert</h1>

            <?php if ($shouldWarn) { ?>
                <div class="alert alert-warning" role="alert">
                    <h5 class="alert-heading">Unable to add alert. 😭</h5>
                    <p class="mb-0">A valid email address is required.</p>
                </div>
            <?php } ?>

            <form method="post" action="add_alert.php">
                <div class="form-group row">
                    <label for="alert_name" class="col-sm-2 col-form-label">Alert name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="alert_name" name="alert_name"
                               placeholder="My custom alert">
                    </div>
                    <small class="form-text text-muted">
                        A name for this alert. Will be used as subject of email notifications.
                    </small>
                </div>
                <hr/>
                <div class="form-group row">
                    <label for="sourceIP" class="col-sm-2 col-form-label">Source IP</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="sourceIP" name="sourceIP"
                               placeholder="192.168.0.0/24">
                    </div>
                    <small class="form-text text-muted">
                        Use CIDR notation for network ranges. Leave empty for any IP.
                    </small>
                </div>
                <div class="form-group row">
                    <label for="destinationIP" class="col-sm-2 col-form-label">Destination IP</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="destinationIP" name="destinationIP"
                               placeholder="192.168.0.0/24">
                    </div>
                    <small class="form-text text-muted">
                        Use CIDR notation for network ranges. Leave empty for any IP.
                    </small>
                </div>
                <div class="form-group row">
                    <label for="kind" class="col-sm-2 col-form-label">Traffic type</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="kind" name="kind">
                            <option>Any Protocol</option>
                            <?php foreach (get_protocols($conn) as $type) {
                                echo "<option>" . $type . "</option>";
                            } ?>
                        </select>
                    </div>
                    <small class="form-text text-muted">
                        Traffic types you wish to be notified for.
                    </small>
                </div>
                <div class="form-group row">
                    <label for="tool" class="col-sm-2 col-form-label">Tool</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="tool" name="tool">
                            <option>Any Tool</option>
                            <option>Libprotoident</option>
                            <option>nDPI</option>
                        </select>
                    </div>
                    <small class="form-text text-muted">
                        Only fire the alert if packet is detected by a specific DPI tool.
                    </small>
                </div>
                <div class="form-group row">
                    <label for="tool" class="col-sm-2 col-form-label">Source Country</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="source_country" name="source_country">
                            <option>Any Country</option>
                            <?php foreach (get_countries($conn) as $country) {
                                echo "<option>" . $country . "</option>";
                            } ?>
                        </select>
                    </div>
                    <small class="form-text text-muted">
                        Only fire the alert if packet originates from this country.
                    </small>
                </div>
                <div class="form-group row">
                    <label for="tool" class="col-sm-2 col-form-label">Destination Country</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="destination_country" name="destination_country">
                            <option>Any Country</option>
                            <?php foreach (get_countries($conn) as $country) {
                                echo "<option>" . $country . "</option>";
                            } ?>
                        </select>
                    </div>
                    <small class="form-text text-muted">
                        Only fire the alert if packet is directed at this country.
                    </small>
                </div>
                <hr/>
                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label">Send email to:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="email" name="email"
                               placeholder="andrea@gottardo.me">
                    </div>
                    <small class="form-text text-muted">
                        The email address that will receive the alert.
                    </small>
                </div>
                <hr/>
                <button type="submit" class="btn btn-primary btn-sm"><i data-feather="plus"></i> Add</button>
                <a href="/alerts.php">
                    <button type="button" class="btn btn-danger btn-sm"><i data-feather="x"></i> Cancel</button>
                </a>

                <input type="hidden" name="set" value="true"/>
            </form>

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

</body>
</html>

