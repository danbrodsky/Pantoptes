<?php
include_once("vendor/autoload.php");
include_once("util.php");

if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}
$no_of_records_per_page = 30;

$offset = ($pageno-1) * $no_of_records_per_page;

$query = "SELECT * FROM packets ORDER BY packets.id desc LIMIT $no_of_records_per_page OFFSET $offset  ";
$query = pg_query($conn, $query);

?>
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
        array_push($mapRows, $row); // adds the row to the map array
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . tool_id_to_string($row["tool"]) . "</td>";
        echo "<td>" . $row["packet_type"] . "</td>";
        echo "<td>" . $row["source_ip"] . "</td>";
        echo "<td>" . $row["destination_ip"] . "</td>";
        echo "<td>" . $row["source_country"] . "</td>";
        echo "<td>" . $row["destination_country"] . "</td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>