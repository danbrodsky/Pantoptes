<?php
include_once("vendor/autoload.php");
include_once("util.php");
include_once("graphing_utils.php");
?>

<?php include("include_head.php"); ?>

<div class="container-fluid">
    <div class="row">
        <?php include "sidemenu.php"; ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <h1 style="margin-top: 20px;">Add alert</h1>

            <form>
                <div class="form-group row">
                    <label for="sourceIP" class="col-sm-2 col-form-label">Source IP</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="sourceIP">
                    </div>
                    <small class="form-text text-muted">
                        Use CIDR notation for network ranges. Leave empty for any IP.
                    </small>
                </div>
                <div class="form-group row">
                    <label for="destinationIP" class="col-sm-2 col-form-label">Destination IP</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="destinationIP">
                    </div>
                    <small class="form-text text-muted">
                        Use CIDR notation for network ranges. Leave empty for any IP.
                    </small>
                </div>
                <div class="form-group row">
                    <label for="kind" class="col-sm-2 col-form-label">Traffic type</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="kind">
                            <option>Any Protocol</option>
                            <?php foreach (get_protocols($conn) as $type) {
                                echo "<option>".$type."</option>";
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
                        <select class="form-control" id="tool">
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
                        <select class="form-control" id="tool">
                            <option>Any Country</option>
                            <?php foreach (get_countries($conn) as $country) {
                                echo "<option>".$country."</option>";
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
                        <select class="form-control" id="tool">
                            <option>Any Country</option>
                            <?php foreach (get_countries($conn) as $country) {
                                echo "<option>".$country."</option>";
                            } ?>
                        </select>
                    </div>
                    <small class="form-text text-muted">
                        Only fire the alert if packet is directed at this country.
                    </small>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label">Send email to:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="email">
                    </div>
                    <small class="form-text text-muted">
                        The email address that will receive the alert.
                    </small>
                </div>
                <button type="button" class="btn btn-primary btn-sm"><i data-feather="plus"></i> Add</button>
                <a href="/alerts.php"><button type="button" class="btn btn-danger btn-sm"><i data-feather="x"></i> Cancel</button></a>
                <hr/>
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

<!-- Graphs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>

</body>
</html>

