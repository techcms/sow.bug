<?php
use Sow\Bug as Y;
class HomeController extends \Sow\Sys\Control
{
	public function init() {
		$this->setViewpath(Y::view($this->getModuleName()));
	}

	public function indexAction() {
    $s = eval(\rule::$page);
    Y::dump(\rule::$page,$s,eval(\rule::$page),$result);

	}
	public function demoAction() {
		$this->assign( "b", array(1,2) ); 
	}
}
