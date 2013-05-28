<?php
// if starts with 10 or 192.168 or if mongopress.org run. - security.
$ip = $_SERVER['SERVER_ADDR']; 
if (substr($ip,0,3) == '10.' || substr($ip,0,7) == '192.168' || substr($ip,0,6) == '127.0.' || substr($_SERVER['HTTP_HOST'],-14) == 'mongopress.org') {
    $mongopress_version = 'unknown';
    require_once 'mp-includes/install/requirements.php';
    echo $mongopress_version;
} else die();