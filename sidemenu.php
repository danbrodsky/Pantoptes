<?php
/**
 * Created by PhpStorm.
 * User: agott
 * Date: 2018-11-20
 * Time: 13:48
 */
?>

<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="/">
                    <span data-feather="home"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/graphs.php">
                    <span data-feather="pie-chart"></span>
                    Statistics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/alerts.php">
                    <span data-feather="bell"></span>
                    Alerts <span class="badge badge-primary"> <?php echo num_alerts($conn); ?></span>
                </a>
            </li>
            <div style="margin-top: 10px; margin-left: 15px; margin-right: 15px;">
                <small>Panoptes is currently receiving packets from <?php echo num_nodes($conn); ?> listening node(s).<br/>PHP v<?php echo phpversion();?>.</small>
            </div>
        </ul>
    </div>
</nav>
