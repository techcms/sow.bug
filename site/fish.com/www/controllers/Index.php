<?php
use Sow\Bug as Y;
class IndexController extends \Sow\Sys\Control
{
	public function init() {
		$this->setViewpath(VIEWPATH);
	}

	public function indexAction() {
		Y::dump($this->GET("pasge"));
		//Y::dump( VIEWPATH,$this->getView(), $this->getViewpath() );
	}
}
