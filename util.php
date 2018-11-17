<?php
/**
 * Created by PhpStorm.
 * User: agott
 * Date: 2018-11-16
 * Time: 19:02
 */

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