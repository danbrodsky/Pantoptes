<?php
include_once("vendor/autoload.php");
include_once("util.php");

if (isset($_GET['pageno']) and pg_escape_string($_GET["pageno"]) != '') {
    $pageno = pg_escape_string($_GET['pageno']);
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
        $filter .= "packet_type = '" . pg_escape_string($_GET["protocol"]) . "'";
    } else {
        $filter .= "1=1";
    };
    if ($country != '') {
        $filter .= " AND source_country = '" . pg_escape_string($_GET["country"]) . "'";
    };
    if ($tool != '') {
        $filter .= " AND tool = " . pg_escape_string($_GET["tool"]);
    };


    $query = $query . ' ' . $filter . ' ' . $paginate;
    $map_query = substr($query, 0, strpos($query, "LIMIT"));
    $query = pg_query($conn, $query);

    while ($row = pg_fetch_assoc($query)) {
        if ($row["source_ip"] == "10.0.0.5"){
            $row["source_country"] = "us";
            $row["source_longitude"] = "-122.121513";
            $row["source_latitude"] = "47.673988";
        }
        if ($row["destination_ip"] == "10.0.0.5"){
            $row["destination_country"] = "us";
            $row["destination_longitude"] = "-122.121513";
            $row["destination_latitude"] = "47.673988";
        }
        $srcCountry = $row["source_country"] !== null ? trim($row["source_country"]) : "";
        $dstCountry = $row["destination_country"] !== null ? trim($row["destination_country"]) : "";
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . relativeTime($row["timestamp"]) . " <small>PST</small></td>";
        echo "<td>" . tool_id_to_string($row["tool"]) . "</td>";
        echo "<td>" . $row["packet_type"] . " <small><strong>" . $row["source_port"] . "→" . $row["destination_port"] . "</strong></small></td>";
        if ($srcCountry != "") {
            echo "<td class=\"flowRow\" data-toggle=\"tooltip\" data-placement=\"top\" title='" . gethostbyaddr($row['source_ip']) . "'><img src='/img/flags/" . strtolower($srcCountry) . ".png' height='13' /> " . $row["source_ip"] . " <small><strong><a class='text-dark' href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=".$row["source_ip"]."'>[w]</a></strong></small></td>";
        } else {
            echo "<td class=\"flowRow\" data-toggle=\"tooltip\" data-placement=\"top\" title='" . gethostbyaddr($row['source_ip']) . "'>" . $row["source_ip"] . " <small><strong><a class='text-dark' href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=".$row["source_ip"]."'>[w]</a></strong></small></td>";
        }
        if ($dstCountry != "") {
            echo "<td class=\"flowRow\" data-toggle=\"tooltip\" data-placement=\"top\" title='" . gethostbyaddr($row['destination_ip']) . "'><img src='/img/flags/" . strtolower($dstCountry) . ".png' height='15' /> " . $row["destination_ip"] . "  <small><strong><a class='text-dark' href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=".$row["destination_ip"]."'>[w]</a></strong></small></td>";
        } else {
            echo "<td class=\"flowRow\" data-toggle=\"tooltip\" data-placement=\"top\" title='" . gethostbyaddr($row['destination_ip']) . "'>" . $row["destination_ip"] . " <small><strong><a class='text-dark' href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=".$row["destination_ip"]."'>[w]</a></strong></small></td>";
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    map.getSource('packet-info').setData({
        type: "FeatureCollection",
        features: [<?php
            $map_query = pg_query($conn, $map_query);
            $mapRows = array();
            while ($row = pg_fetch_assoc($map_query)) {
                array_push($mapRows, $row);
            }
            foreach ($mapRows as $row) {
                if ($row["source_ip"] == "10.0.0.5"){
                    $row["source_country"] = "us";
                    $row["source_longitude"] = "-122.121513";
                    $row["source_latitude"] = "47.673988";
                }
                if ($row["destination_ip"] == "10.0.0.5"){
                    $row["destination_country"] = "us";
                    $row["destination_longitude"] = "-122.121513";
                    $row["destination_latitude"] = "47.673988";
                }
                $srcCountry = $row["source_country"] !== null ? trim($row["source_country"]) : "";
                $dstCountry = $row["destination_country"] !== null ? trim($row["destination_country"]) : "";
                if ($row["source_country"] == "CN" && $row["destination_longitude"] != 0.0) {
                    echo "{\"type\":\"Feature\",\"geometry\":{\"type\":\"LineString\",\"coordinates\":[[29.406838, 106.920059], [" . $row["destination_longitude"] . "," . $row["destination_latitude"] . "]]}},";
                } else if ($row["destination_country"] == "CN" && $row["source_longitude"] != 0.0) {
                    echo "{\"type\":\"Feature\",\"geometry\":{\"type\":\"LineString\",\"coordinates\":[" . $row["source_longitude"] . "," . $row["source_latitude"] . "], [29.406838, 106.920059]]}},";
                } else if ($row["source_longitude"] != 0.0 && $row["source_latitude"] != 0.0 &&
                    $row["destination_longitude"] != 0.0 && $row["destination_latitude"] != 0.0) {
                    echo "{\"type\":\"Feature\",\"geometry\":{\"type\":\"LineString\",\"coordinates\":[[" . $row["source_longitude"] . "," . $row["source_latitude"] . "], [" . $row["destination_longitude"] . "," . $row["destination_latitude"] . "]]}},";
                    echo "{\"type\": \"Feature\",\"properties\":{\"description\":\"<img src='/img/flags/" . strtolower($srcCountry) . ".png' height='13' /> " . $row["source_ip"] . " <small><strong><a href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=" . $row["source_ip"] . "'>[w]</a></strong></small>\", \"icon\": \"custom-marker\"},\"geometry\": {\"type\": \"Point\",\"coordinates\": [" . $row["source_longitude"] . "," . $row["source_latitude"] . "]}},";
                    echo "{\"type\": \"Feature\",\"properties\":{\"description\":\"<img src='/img/flags/" . strtolower($dstCountry) . ".png' height='15' /> " . $row["destination_ip"] . "  <small><strong><a href='https://apps.db.ripe.net/db-web-ui/#/query?searchtext=" . $row["destination_ip"] . "'>[w]</a></strong></small>\", \"icon\": \"custom-marker\"},\"geometry\": {\"type\": \"Point\",\"coordinates\": [" . $row["destination_longitude"] . "," . $row["destination_latitude"] . "]}},";
                }
            }
            ?>
        ]
    });
</script>