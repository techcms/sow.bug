#!/usr/local/bin/php
<?php
use Sow\bug as Y;
$yaf = new \Yaf\Application( 'config/bug.ini', 'dev' );
Y::shell( $argc, $argv );
./bdie();

$commands = array(
	'help', 'add', 'remove', 'tree'
);

$objects = array(
	'site', 'module', 'control', 'model', 'plugin'
);

$dirs = array(
	"/www/views/index", "/www/moudle/demo", "/www/plugins", "/www/model", "/config", "/lib", "/shell"
);



$files = array(
	"site_index" => array (
		"content" => $file_site_index,
		"file" => "/www/index.php",
	)
);


$function = "sow_".$command."_".$object;
if ( function_exists( $function ) ) {
	call_user_func( $function, $name );
	die();
}

sow_help();

function sow_add_site( $site ) {


	if ( is_dir( "./$site" ) ) {
		echo "Error : site $site 已存在 \n";
	} else {
		global $dirs, $files;

		foreach ( $dirs as $dir ) {
			mkdir( "./$site/$dir", 0755, True );
		}


	}
}


function sow_help() {
	global $objects, $commands;
	echo
	"Sowbug version 0.1 \n",
	"./sow [command] [object] [name]\n",
	"------------------------------------------\n",
	"@command   ".implode( ",", $commands )."\n",
	"@object    ".implode( ",", $objects )."\n",
	"@name      example:fish.com\n",
	"------------------------------------------\n",
	"./sow help\n",
	"./sow add site fish.com\n",
	"./sow add module fish.com\n",

	"\n"
	;
}
