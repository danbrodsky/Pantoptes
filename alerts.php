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

            <h1 style="margin-top: 20px;">Alerts</h1>
            <button type="button" class="btn btn-secondary btn-sm"><i data-feather="plus-square"></i> New Alert</button>
            <div class="card" style="margin-top: 20px;">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <h5>Alert #1: Telegram messages to China</h5>
                        <p>An email will be sent to <code>andrea@gottardo.me</code> if <i>outgoing</i> <code>Telegram</code> traffic is detected towards country code <code>CN</code>.</p>
                    </li>
                    <li class="list-group-item">
                        <h5>Alert #2: SSH incoming connections</h5>
                        <p>An email will be sent to <code>andrea@gottardo.me</code> if <i>incoming</i> <code>SSH</code> traffic is detected.</p>
                    </li>
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

