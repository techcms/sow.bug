<?php

if (! function_exists('mongopress_load_mp')) {
	include_once dirname(__file__) .DIRECTORY_SEPARATOR. 'inc/mongopress.inc.php';
}

$mp = mongopress_load_mp();
$mp_options = $mp->options();

// Now initialize other parameters that may or may not be in configuration.

date_default_timezone_set($mp_options['timezone']);
