<?php namespace Sow\sys;
use Sow\bug as Y;
class Param {
    public $vaild = False;
    public $rule = NULL;
    public $message = 'not_fit_rules';
    public $name = NULL;
    public $value = NULL;
    function __construct( $name ) {
        $this->name = $name;
        $this->rule = new \rule($name);
        if ( method_exists( "rule", $name ) ) {
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

        }else {
            $this->message = "not_in_rules";
        }

        if ( $this->vaild ) {
            $this->validate();
        }
    }
    function validate() {
        $name = $this->name;
        $validator = $this->rule->$name();
        $this->vaild = $validator->validate( $this->value );
        $this->message = NULL;

    }

}
