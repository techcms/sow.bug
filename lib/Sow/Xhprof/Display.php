<?php namespace  Sow\Xhprof;
use Sow\Bug as Y;
class Display {
	public function __construct( $data, $dir ) {



		$url = Y::application_ini( 'baseUri' )."xhprof/view/run/".$run_id."/source/".Y::config( 'xhprof_id' );


		Y::location( $url );
	}
}
