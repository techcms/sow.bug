<?php
use Sow\Bug as Y;

date_default_timezone_set('Asia/Shanghai');

$yaf = new \Yaf\Application( dirname( __DIR__ ).'/config/fish.ini', 'dev' );

$response = Y::http(True);


// //-------------------------------------------
// if ( ( $config->xhprof )&& YDEBUG ) {
// 	$xhprof_data = xhprof_disable();
// 	$xhprof_runs = new XHProf();
// 	$run_id = $xhprof_runs->save_run( $xhprof_data, "www" );
// 	$xhprof_data = array( 'graphivz'=>"http://dev.home.com/xhprof_html/callgraph.php?source=www&run=".$run_id, 'xhprof' )+$xhprof_data;
// 	FB::info( $xhprof_data, 'xhprof' );
// }
// //-------------------------------------------

