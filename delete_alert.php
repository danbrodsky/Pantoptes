<?php
/**
 * Created by PhpStorm.
 * User: agott
 * Date: 2018-11-24
 * Time: 13:58
 */
include_once("util.php");

$id = pg_escape_string($_GET["id"]);
$deletion_query = "DELETE FROM \"public\".\"alerts_settings\" WHERE \"id\" = " . $id;
if (pg_query($conn, $deletion_query)) {
    header("Location: /alerts.php");
} else {
    $err = pg_last_error($conn);
    die();
}
