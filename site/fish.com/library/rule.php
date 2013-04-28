<?php
use Sow\Bug as Y;
class rule {

  private  $rules = array(
    '' => '',
  );

  public static function _get( $name ) {

    var_dump(Y::param("shit"));

  }
  public static function _post( $name ) {



  }

  // public function verify( $key, $verify ) {
  //   if ( !array_key_exists( $key, $verify ) ) { //判断key是否存在验证规则
  //     return "key does not exist validation rules";
  //   }else {
  //     $preg=$verify;
  //   }
  //   $Validate=new Validate();
  //   if ( array_key_exists( $key, $data=Y::request()->getParams() ) ) {
  //     $value=trim( $data[$key] );
  //     if ( $d=$Validate->check( $value, $preg[$key] ) ) {
  //       $get=trim( $value );
  //     }else {
  //       $this->error[$key]=$Validate->error();
  //       $get = false;
  //     }
  //     return $get;
  //   }elseif ( array_key_exists( $key, $data=Y::request()->getPost() ) ) {
  //     $value=trim( $data[$key] );
  //     if ( $d=$Validate->check( $value, $preg[$key] ) ) {
  //       $get=trim( $value );
  //     }else {
  //       $this->error[$key]=$Validate->error();
  //       $get = false;
  //     }
  //     return $get;
  //   }else {
  //     return false;
  //   }
  // }
}
