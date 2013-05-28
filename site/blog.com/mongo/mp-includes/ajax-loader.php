<?php
$_MP['COOKIE'] = array();
$_MP['INCLUDES'] = str_replace('\\','/',dirname(__FILE__));
$_MP['DOCUMENT_ROOT'] = str_replace('\\','/',dirname(dirname(__FILE__)));
$_MP['SETTINGS'] = $_MP['DOCUMENT_ROOT'] . '/mp-settings';
$_MP['CACHE'] = $_MP['DOCUMENT_ROOT'] . '/mp-cache'; // allow override later;
$_MP['THEME_ROOT'] = $_MP['DOCUMENT_ROOT'] . '/mp-content/themes/';

// This could fail - if a numpty removes their config.
if(@file_exists($_MP['SETTINGS'].'/config.php')){
	require_once $_MP['SETTINGS'].'/config.php';
} if(@file_exists($_MP['SETTINGS'].'/security.php')){
	require_once $_MP['SETTINGS'].'/security.php';
}
require_once $_MP['INCLUDES'].'/includes.php';