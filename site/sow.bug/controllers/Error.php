<?php
use Sow\bug as Y;
class Error_Controller extends \Sow\sys\Control
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
		Y::dump($this->GET("page"));
		Y::_404(False);
	}
}
