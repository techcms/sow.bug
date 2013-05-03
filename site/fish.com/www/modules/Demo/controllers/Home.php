<?php
use Sow\Bug as Y;
class HomeController extends \Sow\Sys\Control
{
	public function init() {
		$this->setViewpath(Y::view($this->getModuleName()));
	}

	public function indexAction() {
		Y::dump($this->GET("page"));
		//Y::dump( VIEWPATH,$this->getView(), $this->getViewpath() );
	}
	public function demoAction() {
		Y::dump($this->GET("page"));
		//Y::dump( VIEWPATH,$this->getView(), $this->getViewpath() );
	}	
}
