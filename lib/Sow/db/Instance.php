<?php namespace  Sow\db;
use Sow\bug as Y;
use Sow\sys\Exception as Exception;
class Instance {

    public static $_instance = array();

    public static function mysqli( $name ) {

        $mysql = new Mysqli( $name );
        Y::dump( $mysql );
        if (isset($_instance[$name])){
            

        }
        // $result = $mysql->query("select * from fish");
        // $mysql1 = new mysql('dog');
        // $result1 = $mysql1->query("select * from dog");
        // Y::dump($mysql,$mysql1);

    }


}
