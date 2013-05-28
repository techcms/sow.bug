<?php

Global $_MP;
$_MP['COOKIE'] = array();
$_MP['INCLUDES'] = dirname(__FILE__);
$_MP['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));

if (DIRECTORY_SEPARATOR !== '/') {
	$_MP['INCLUDES'] = str_replace('\\','/',$_MP['INCLUDES']);
	$_MP['DOCUMENT_ROOT'] = str_replace('\\','/',$_MP['DOCUMENT_ROOT']);
}
$_MP['INC'] = $_MP['INCLUDES'].'/inc';
$_MP['SETTINGS'] = $_MP['DOCUMENT_ROOT'] . '/mp-settings';
$_MP['CACHE'] = $_MP['DOCUMENT_ROOT'] . '/mp-cache'; // allow override later;
$_MP['THEME_ROOT'] = $_MP['DOCUMENT_ROOT'] . '/mp-content/themes';
$_MP['HOME'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',$_MP['DOCUMENT_ROOT']);
if (empty($_MP['HOME'])) $_MP['HOME'] = '/';
else $_MP['HOME'] .= '/';

$_MP['SLUG'] = $_SERVER['REQUEST_URI'];

$qs_pos = strpos($_MP['SLUG'],'?');
if ($qs_pos !== false) $_MP['SLUG'] = substr($_MP['SLUG'],0,$qs_pos);

$_MP['SLUG'] = substr($_MP['SLUG'],strlen($_MP['HOME']));


if (!empty($_GET['obj'])){
    $object_id = $_GET['obj'];
    if(is_string($object_id)){
        $object_id = strip_tags(str_replace(array('$', chr(0)), '', $object_id));
    }
} else {
    $object_id = '';
}
?>
