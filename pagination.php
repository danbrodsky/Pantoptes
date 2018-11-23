<?php
include_once("vendor/autoload.php");
include_once("util.php");

if (isset($_GET['pageno']) and $_GET["pageno"] != '') {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}

$no_of_records_per_page = 30;

$offset = ($pageno - 1) * $no_of_records_per_page;

$query = "SELECT * FROM packets";
$filter = "WHERE ";
$paginate = "ORDER BY packets.id desc LIMIT $no_of_records_per_page OFFSET $offset  ";
?>

<script type="text/javascript">
    document.getElementById("pageno").innerHTML = "<?php echo $pageno; ?>";
</script>

<table class="table table-striped table-sm table-hover">
    <thead class="thead-light">
    <tr>
        <th>Packet ID</th>
        <th>Time</th>
        <th>Tool</th>
        <th>Packet Type</th>
        <th>Source IP</th>
        <th>Destination IP</th>
    </tr>
    </thead>
    <tbody>
    <?php

    // TODO: SANITATION
    $protocol = "";
    $tool = "";
    $country = "";
    if (isset($_GET["protocol"])) {
        $protocol = preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET["protocol"]);
    }
    if (isset($_GET["country"])) {
        $country = preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET["country"]);
    }
    if (isset($_GET["tool"])) {
        $tool = preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET["tool"]);
    }
    if ($protocol != '') {
        $filter .= "packet_type = '" . $_GET["protocol"] . "'";
    } else {
        $filter .= "1=1";
    };
    if ($country != '') {
        $filter .= " AND source_country = '" . $_GET["country"] . "'";
    };
    if ($tool != '') {
        $filter .= " AND tool = " . $_GET["tool"];
    };

    $query = $query . ' ' . $filter . ' ' . $paginate;
    $query = pg_query($conn, $query);
    while ($row = pg_fetch_assoc($query)) {
        $srcCountry = $row["source_country"] !== null ? trim($row["source_country"]) : "";
        $dstCountry = $row["destination_country"] !== null ? trim($row["destination_country"]) : "";
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . relativeTime($row["timestamp"]) . " <small>PST</small></td>";
        echo "<td>" . tool_id_to_string($row["tool"]) . "</td>";
        echo "<td>" . $row["packet_type"] . " <small><strong>" . $row["source_port"] . "→" . $row["destination_port"] . "</strong></small></td>";
        if ($srcCountry != "") {
            echo "<td data-toggle=\"tooltip\" data-placement=\"top\" title='".gethostbyaddr($row["source_ip"])."'><img src='/img/flags/" . $srcCountry . ".png' height='13' /> " . $row["source_ip"] . " <small><strong><a class='text-dark' href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=".$row["source_ip"]."'>[w]</a></strong></small></td>";
        } else {
            echo "<td data-toggle=\"tooltip\" data-placement=\"top\" title='".gethostbyaddr($row["source_ip"])."'>" . $row["source_ip"] . " <small><strong><a class='text-dark' href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=".$row["source_ip"]."'>[w]</a></strong></small></td>";
        }
        if ($dstCountry != "") {
            echo "<td data-toggle=\"tooltip\" data-placement=\"top\" title='".gethostbyaddr($row["destination_ip"])."'><img src='/img/flags/" . $dstCountry . ".png' height='15' /> " . $row["destination_ip"] . "  <small><strong><a class='text-dark' href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=".$row["destination_ip"]."'>[w]</a></strong></small></td>";
        } else {
            echo "<td data-toggle=\"tooltip\" data-placement=\"top\" title='".gethostbyaddr($row["destination_ip"])."'>" . $row["destination_ip"] . " <small><strong><a class='text-dark' href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=".$row["destination_ip"]."'>[w]</a></strong></small></td>";
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>