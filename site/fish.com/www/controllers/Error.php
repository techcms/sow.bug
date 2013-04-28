<?php
use Sow\Bug as Y;
class ErrorController extends \Sow\Sys\Control
{
	public function init() {
		$this->setViewpath(VIEWPATH);
	}

	public function indexAction() {
		$this->_get("shit");
	}

	public function errorAction() {
		$this->_get("shit");
	}
	public function error404Action() {
		$this->_get("shit");
		Y::_404(False);
	}
}
