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

  public function GET( $name,$debug = False) {
    $param = new Param($name);

    if ($debug) {
      return $param;
    }
    return $param->value;
    
  }
  public function POST( $name ,$debug = False) {
    return $this->GET( $name ,$debug = False);
  }

}
