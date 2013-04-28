<?php namespace Sow\Sys;
use Sow\Bug as Y;
class Control extends \Yaf\Controller_Abstract {

  public function assign( $name, $value =null ) {
    if ( $value ) {
      return $this->_view->assign( $name, $value );
    } else {
      return $this->_view->assign( $name );
    }
  }

  public function GET( $name ) {
    return new Param($name);
  }
  public function POST( $name ) {
    return $this->GET( $name );
  }

}
