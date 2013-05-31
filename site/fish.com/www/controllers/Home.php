<?php
use Sow\bug as Y;
use Sow\util\FB as fb;
use Sow\DB as DB;
class Home_Controller extends \Sow\sys\Control
{
	public function init() {
		$this->setViewpath( Y::view( $this->getModuleName() ) );
	}

	public function indexAction() {

	}
	public function demoAction() {
		//Y::dump($this->GET('page'));

		// fb::log( Y::config() );
		// fb::log( Y::config()->toArray() );
		// fb::info( 'Info Message' );
		// fb::warn( 'Warn Message' );
		// fb::error( 'Error Message' );


		// $imagine = new Imagine\Gd\Imagine();
		// $size = new Imagine\Image\Box( 200, 200 );

		// $mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
		// $imagine->open( '/web/photo/t1.jpg' )
		// ->thumbnail( $size, $mode )
		// ->save( '/web/photo/t1_small.jpg' );


		// $redis = Y::redis('localhost');

		// $redis->set('1',11111);

		// Y::dump($redis->get('1'));

	}
}
