<?php namespace Sow\Sys;
use Sow\Bug as Y;
class Param {
    public $vaild = False;
    public $rule = NULL;
    public $message = NULL;
    public $name = NULL;
    public $value = NULL;

    function __construct( $name ) {
        $this->name = $name;
        $rule = \rule::getRule( $name );
        if ( $rule === False ) {
            $this->message = "not_in_rules";
        }else{
            $this->rule = $rule;    
        }
        
        if ( !array_key_exists( $name, Y::Params())){
            
        }

        $this->vaild = $params;
        
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
