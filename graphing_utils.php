<?php
/**
 * Created by PhpStorm.
 * User: agott
 * Date: 2018-11-20
 * Time: 13:15
 */

function num_packets_types($conn)
{
    $acc = array();
    $query = "SELECT packet_type, count(packet_type) as cnt FROM packets GROUP BY packet_type ORDER BY packet_type asc";
    $query = pg_query($conn, $query);
    while ($row = pg_fetch_assoc($query)) {
        array_push($acc, $row);
    }
    return $acc;
}

function num_countries($conn)
{
    $acc = array();
    $query = "SELECT source_country, count(source_country) as cnt FROM packets GROUP BY source_country ORDER BY source_country asc";
    $query = pg_query($conn, $query);
    while ($row = pg_fetch_assoc($query)) {
        array_push($acc, $row);
    }
    return $acc;
}

function packet_times($conn)
{
    $acc = array();
    $query = "SELECT timestamp, count(timestamp) as cnt FROM packets GROUP BY timestamp ORDER BY timestamp asc";
    $query = pg_query($conn, $query);
    while ($row = pg_fetch_assoc($query)) {
        array_push($acc, $row);
        echo "<script>console.log( 'Debug Objects: " . $row["cnt"] . "' );</script>";
    }
    return $acc;
}