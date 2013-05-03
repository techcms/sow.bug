<?php
use Sow\Bug as Y;
class rule {
  protected static $rules=array(
    "page"=>array(
      'required' => true,
      'type'     => "int",
      'msg'      => "page"
    ),
    "run"=>array(
      'required' => true,
      'type'     => "string",
      'msg'      => "page"
    ),

  );

  public static function getRule( $name ) {

    if ( array_key_exists( $name , self::$rules ) ) {
      return self::$rules[$name];
    }
    return NULL;
  }

}
