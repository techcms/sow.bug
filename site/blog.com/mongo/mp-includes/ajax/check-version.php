<?php

/* INCLUDE REQUIRED FILES */
require_once dirname(dirname(__FILE__)).'/ajax-loader.php';
require_once $_MP['INCLUDES'].'/inc/install.inc.php';

$versions = mongopress_get_versions();

$our_version = $versions['mongopress'];
$latest_version = trim(file_get_contents('http://www.mongopress.org/mp-version.php'));

$data = array(
    'success' => true,
    'current_version' => $our_version,
    'latest_version' => $latest_version,
    'update_needed' => utils_version_compare($our_version , $latest_version),
);

/* RETURN OBJECT */
mp_json_send($data);