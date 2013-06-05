<?php namespace  Sow\db;
use Sow\bug as Y;
use Sow\sys\Exception as Exception;
class Instance {

    public static $_instance = array();

    public static function db( $name ) {

        
        if (isset($_instance[$name])){
            if ($_instance[$name] instanceof Sow\db\mysqli) {
                return $_instance[$name];
            }        
        }
        $_instance[$name] = new Mysqli( $name );
        return $_instance[$name];


    }


}
