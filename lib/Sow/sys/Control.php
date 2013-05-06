<?php namespace Sow\sys;
use Sow\bug as Y;
class Control extends \Yaf\Controller_Abstract {

  public function assign( $name, $value =null ) {
    if ( $value ) {
      return $this->_view->assign( $name, $value );
    } else {
      return $this->_view->assign( $name );
    }
  }

  public function GET( $name,$object = False) {
    $param = new Param($name);


    if ($object) {
      return $param;
    }
    if ($param->vaild){
      return $param->value;
    } else
      return false;
      
    
  }
  public function POST( $name ,$object = False) {
    return $this->GET( $name ,$object);
  }

}
