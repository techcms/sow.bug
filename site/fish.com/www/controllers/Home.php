<?php
use Sow\bug as Y;
use Sow\util\FB as fb;
use Sow\DB as DB;

use Sow\log\Monolog as log;
use Guzzle\Http\Client;



class Home_Controller extends \Sow\sys\Control
{
	public function init() {
		$this->setViewpath( Y::view( $this->getModuleName() ) );
	}

	public function indexAction() {

		$url = \Purl\Url::parse( 'http://jwage.com' )
		->set( 'scheme', 'https' )
		->set( 'port', '443' )
		->set( 'user', 'jwage' )
		->set( 'pass', 'password' )
		->set( 'path', 'about/me' )
		->set( 'query', 'param1=value1&param2=value2' )
		->set( 'fragment', 'about/me?param1=value1&param2=value2' );

		echo $url->getUrl();
	}
	public function demoAction() {
		//Y::dump($this->GET('page'));

		// fb::log( Y::config() );
		// fb::log( Y::config()->toArray() );
		// fb::info( 'Info Message' );
		// fb::warn( 'Warn Message' );
		// fb::error( 'Error Message' );
		// $client = new Client('http://www.techweb.com.cn');
		// $request = $client->get('/');
		// $response = $request->send();
		// Y::dump($response);

		/*
		const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
		*/
		// $log = log::stream('fish');
		// $log->addDebug('123213213');
		// $log->addWarning('123213213');
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
