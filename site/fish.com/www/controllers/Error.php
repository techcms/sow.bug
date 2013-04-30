<?php
use Sow\Bug as Y;
class ErrorController extends \Sow\Sys\Control
{
	public function init() {
		$this->setViewpath(Y::view($this->getModuleName()));
	}

	public function indexAction() {
		Y::dump($this->GET("pasge"));
	}

	public function errorAction() {
		Y::dump($this->GET("pasge"));
	}
	public function error404Action() {
		// Y::dump(Y::app());
		//Y::dump($this->GET("page",1),$this->GET("page",1),$this->GET("page",1));
		Y::_404(False);
	}
}
