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
  public function set( $name, $value =null ) {
    $this->assign($name,$value);
  }

  public function _get( $name ) {
    return \rule::_get($name);
  }
  public function _post( $name ) {
   return \rule::_post($name); 
  }
}
