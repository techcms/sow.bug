<?php
/* This loads up all of MongoPress in the correct order, skipping checks if various flags exist. */

require_once dirname(__FILE__) . '/env.php';

// 2 - check basic installation

function mongopress_install_error($object_id,$msg='') {
	if (! file_exists($GLOBALS['_MP']['SETTINGS'].'/config.php'))
		header('Location: http://'.$_SERVER['HTTP_HOST'].$GLOBALS['_MP']['HOME'].'mp-admin/install.php');

	// we prefer to redirect - but if not - we can include the file.
	require_once($GLOBALS['_MP']['DOCUMENT_ROOT'].'/mp-admin/install.php');
	die();
}

// skip the installation check if mp-admin/check-install.php successfully passed
if (! file_exists($_MP['CACHE'].'/flags/installed.flag'))  mongopress_install_error($object_id,'installed flag missing');

// 3 - load up all the files required to run mongopress.
// NOTE - MODULES CAN BE DONE HERE

if (file_exists($_MP['SETTINGS'].'/config.php')) require_once($_MP['SETTINGS'].'/config.php');
else mongopress_install_error($object_id,'missing config');
if (file_exists($_MP['SETTINGS'].'/security.php')) require_once($_MP['SETTINGS'].'/security.php');
else mongopress_install_error($object_id,'missing config');


// GO DO STUFF!

require_once($_MP['INCLUDES'].'/includes.php');
require_once($_MP['INCLUDES'].'/mp-init.php');


$GLOBALS['_MP']['COOKIE'] = $mp->get_cookies();

mongopress_load_core($object_id);