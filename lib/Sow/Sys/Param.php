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
        }else {
            $this->rule = $rule;
            if ( array_key_exists( $name, Y::Params() ) ) {
                $this->value =  Y::Param( $name );
                $this->vaild = true;
            } else {

                if ( Y::getMethod() == "GET" ) {
                    $params = $_GET;
                }
                if ( Y::getMethod() == "POST" ) {
                    $params = $_POST;
                }
                if ( array_key_exists( $name, $params ) ) {
                    $this->value =  $params[$name];
                    $this->vaild = true;
                } else {
                    $this->message = "not_in_params";
                }

            }
        }

        if ( $this->vaild ) {

            $this->validate();
        }
    }
    function validate() {
        $validate = new \Sow\Util\Validate ;
       $this->vaild = $validate->check( $this->value, $this->rule );
       if (! $this->vaild  ) {
          $this->message=$validate->error();
        }

    }

}
