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