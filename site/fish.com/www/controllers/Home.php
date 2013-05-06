<?php
use Sow\Bug as Y;
use Sow\Util\FB as fb;
class HomeController extends \Sow\Sys\Control
{
	public function init() {
		$this->setViewpath( Y::view( $this->getModuleName() ) );
	}

	public function indexAction() {

	}
	public function demoAction() {
		// fb::log( Y::config() );
		// fb::log( Y::config()->toArray() );
		// fb::info( 'Info Message' );
		// fb::warn( 'Warn Message' );
		// fb::error( 'Error Message' );
	}
}
