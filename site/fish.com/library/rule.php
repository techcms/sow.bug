<?php
use Sow\Bug as Y;
class rule {
  protected static $rules=array(
    "quality"=>array(
      'required' => true,
      'regex'     => "/^((high)|(plain))$/",
      'msg'      => "quality_false"
    ),
    "page"=>array(
      'required' => true,
      'type'     => "int",
      'msg'      => "page"
    )
  );

  public static function getRule( $name ) {
    if ( array_key_exists( $name , self::$rules ) ) {
      return self::$rules[$name];
    }
    return NULL;
  }

}
