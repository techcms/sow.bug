<?php

/* INCLUDE REQUIRED FILES */
require_once dirname(dirname(__FILE__)).'/ajax-loader.php';
require_once $_MP['INCLUDES'].'/inc/install.inc.php';

$versions = mongopress_get_versions();
$warning = __('ERROR: Unable to fetch contributors!');

$contributors = trim(file_get_contents('http://www.mongopress.org/mp-contributors.php'));
if((!empty($contributors))&&(!strstr($contributors, 'reset-css'))){ echo $contributors; }else{ echo $warning; }