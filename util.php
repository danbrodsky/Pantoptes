<?php
/**
 * Created by PhpStorm.
 * User: agott
 * Date: 2018-11-16
 * Time: 19:02
 */
// Uncomment the following line to debug from your machine (tells your local PHP instance where to find the DB).
// Comment it out before pushing to master!
putenv("DATABASE_URL=postgres://eqdvefruwrhirc:57bbdd00b6b88481eebeeea8c11b52776d0ec96f9e3dd9a21d12f6d9376b9a62@ec2-54-83-27-162.compute-1.amazonaws.com:5432/dqt8lhkkbe5h7");
$conn = pg_connect(getenv("DATABASE_URL"));

/**
 * Returns an human-readable representation of the tool name used to capture a packet.
 * @param $tool_id 0 (libprotoident) or 1 (nDPI)
 * @return string
 */
function tool_id_to_string($tool_id)
{
    if ($tool_id == "0") {
        return "Libprotoident";
    } else {
        return "nDPI";
    }
}

/**
 * Returns a list of protocols used in the database.
 * @param $conn resource DB connection
 * @return array list of protocols used in the database
 */
function get_protocols($conn)
{
    $acc = array();
    $query = "SELECT DISTINCT packet_type FROM packets LIMIT 500";
    $query = pg_query($conn, $query);
    while ($row = pg_fetch_assoc($query)) {
        array_push($acc, $row["packet_type"]);
    }
    $acc = array_unique($acc);
    sort($acc);
    return $acc;
}

/**
 * Returns a list of countries used in the database.
 * @param $conn resource DB connection
 * @return array list of countries in the database
 */
function get_countries($conn)
{
    $acc = array();
    $query = "SELECT DISTINCT source_country FROM packets LIMIT 500";
    $query = pg_query($conn, $query);
    while ($row = pg_fetch_assoc($query)) {
        array_push($acc, $row["source_country"]);
    }
    $query = "SELECT DISTINCT destination_country FROM packets LIMIT 500";
    $query = pg_query($conn, $query);
    while ($row = pg_fetch_assoc($query)) {
        array_push($acc, $row["destination_country"]);
    }
    $acc = array_unique($acc);
    sort($acc);
    return $acc;
}

/**
 * Print a row in the table if cond is true.
 * @param $row is an associative array of values to print to inline HTML.
 *        $mapRows the global map array.
 * @return void
 */
function print_row($arr, $row)
{
    array_push($arr, $row); // adds the row to the map array
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

function get_packet_count($conn)
{
    $query = "SELECT count(id) FROM packets";
    $query = pg_query($conn, $query);
    return pg_fetch_result($query, 0, 0);
}

function debug_to_console($data)
{
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

function num_tools($conn)
{
    $query = "SELECT count(DISTINCT tool) FROM packets";
    $query = pg_query($conn, $query);
    return pg_fetch_result($query, 0, 0);
}

function relativeTime($ts): string
{
    $currentTime = DateTime::createFromFormat('U', $ts);
    $currentTime->setTimezone(new DateTimeZone("PST"));
    $formatted = $currentTime->format('Y-m-d H:i:s');
    return $formatted;
}

function num_nodes($conn): string
{
    $query = "SELECT count(DISTINCT node_id) FROM packets";
    $query = pg_query($conn, $query);
    return pg_fetch_result($query, 0, 0);
}