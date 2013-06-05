<?php namespace Sow\log;
use Sow\bug as Y;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class Monolog {
  private static $instance = array();
  public static function stream( $name ) {
    $config = Y::config( 'log' )->$name->toArray();
    $logpath = $config['path']."/".date( "Ymd", time() );
    $logfile = $logpath."/fish_".date( "YmdH", time() ).".log";
    if ( !is_dir( $logpath ) ) {
      mkdir( $logpath, 0755, true );
    }

    if (isset(self::$instance[$name])){
      if (file_exists($logfile)){
        return self::$instance[$name];
      }
    }

    self::$instance[$name] = new Logger( $name );
    self::$instance[$name]->pushHandler( new StreamHandler( $logfile ) );

    return self::$instance[$name];

  }
}
