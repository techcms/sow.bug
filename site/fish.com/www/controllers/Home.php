<?php
use Sow\Bug as Y;
class HomeController extends \Sow\Sys\Control
{
	public function init() {
		$this->setViewpath(Y::view($this->getModuleName()));
	}

	public function indexAction() {
    Y::dump($this->GET('page',true));

	}
	public function demoAction() {
		$this->assign( "b", array(1,2) ); 
	}
}
