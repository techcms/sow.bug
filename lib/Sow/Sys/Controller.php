<?php namespace Sow\Sys;
class Controller extends \Yaf\Controller_Abstract {
  public $_tpl_dir;
  public $error;
  public function assign( $name, $value =null ) {
    if ( $value ) {
      return $this->_view->assign( $name, $value );
    } else {
      return $this->_view->assign( $name );
    }
  }
  public function verify( $key, $verify ) {
    if ( !array_key_exists( $key, $verify ) ) { //判断key是否存在验证规则
      return "key does not exist validation rules";
    }else {
      $preg=$verify;
    }
    $Validate=new Validate();
    if ( array_key_exists( $key, $data=Y::request()->getParams() ) ) {
      $value=trim( $data[$key] );
      if ( $d=$Validate->check( $value, $preg[$key] ) ) {
        $get=trim( $value );
      }else {
        $this->error[$key]=$Validate->error();
        $get = false;
      }
      return $get;
    }elseif ( array_key_exists( $key, $data=Y::request()->getPost() ) ) {
      $value=trim( $data[$key] );
      if ( $d=$Validate->check( $value, $preg[$key] ) ) {
        $get=trim( $value );
      }else {
        $this->error[$key]=$Validate->error();
        $get = false;
      }
      return $get;
    }else {
      return false;
    }
  }
}
