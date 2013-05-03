<?php
use Sow\Bug as Y;
class XhprofController extends \Sow\Sys\Control
{
	public function init() {
		$this->setViewpath( Y::view( $this->getModuleName() ) );
	}

	public function viewAction() {
		\Sow\Xhprof\Ohmyzi::graph($this->GET( 'run' ));
	}
}
