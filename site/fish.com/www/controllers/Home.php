<?php
use Sow\bug as Y;
use Sow\util\FB as fb;
use Sow\db\Instance as mysql;

use Sow\log\Monolog as log;
use Guzzle\Http\Client;
use Zend\Barcode\Barcode;



class Home_Controller extends \Sow\sys\Control
{
	public function init() {
		$this->setViewpath( Y::view( $this->getModuleName() ) );
	}

	public function indexAction() {

			$mysql = mysql::db('fish');
			$result = $mysql->query("select * from word_posts");
			Y::dump($result);



		
	}
	public function demoAction() {

		$table = new Zend\Text\Table\Table( array( 'columnWidths' => array( 10, 20 ) ) );

		// Either simple
		$table->appendRow( array( 'Zend', 'Framework' ) );

		// Or verbose
		$row = new Zend\Text\Table\Row();

		$row->appendColumn( new Zend\Text\Table\Column( 'Zend' ) );
		$row->appendColumn( new Zend\Text\Table\Column( 'Framework' ) );

		$table->appendRow( $row );
		echo $table;
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
