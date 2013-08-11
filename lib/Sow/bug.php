<?php namespace Sow;
class bug {

//tools
//--------------------------------------------------------
  public static function dump() {
    $argc = func_num_args();
    $argv = func_get_args();
    echo '<pre><br>';
    foreach ( $argv as $arg ) {
      var_dump( $arg );
      echo '<hr>';
    }
    echo '</pre>';
  }
  public static function array_md5( array $array ) {
    array_multisort( $array );
    return md5( json_encode( $array ) );
  }

  public static function out() {
    echo "\n";
    $argc = func_num_args();
    $argv = func_get_args();
    foreach ( $argv as $arg ) {
      var_dump( $arg );
      echo "---------------------------\n";
    }
  }

//yaf 
//--------------------------------------------------------
  public static function app( ) {
    return \Yaf\Application::app();
  }
  public static function set( $name , $value ) {
    return \Yaf\Registry::set( $name , $value );
  }
  public static function get( $name ) {
    return \Yaf\Registry::get( $name );
  }
  public static function has( $name ) {
    return \Yaf\Registry::has( $name );
  }
  public static function del( $name ) {
    return \Yaf\Registry::del( $name );
  }

  public static function application_ini( $name ) {
    $application = self::config( 'application' );

    return $application[$name];
  }

  public static function config( $item = NULL ) {
    if ( is_null( $item ) ) {
      return self::app()->getConfig() ;
    }
    return self::app()->getConfig()->$item ;
  }
  public static function configSlice(  ) {
    $argc = func_num_args();
    if ( $argc == 0 ) return False;

    $argv = func_get_args();
    $return = self::get( "config" )->toArray();
    for ( $i = 0;$i<$argc;$i++ ) {
      $return = $return[$argv[$i]];
    }
    if ( is_array( $return ) ) return $return;
    return explode( ",", $return );

  }
  public static function loader() {
    return \Yaf\Loader::getInstance();
  }
  public static function library() {
    return self::loader()->getLibraryPath();
  }
  public static function path( ) {
    return self::loader()->registerLocalNameSpace( func_get_args() );
  }
  public static function view( $module = 'Index' ) {
    return self::config( 'viewpath' )."/".$module;
  }

  public static function returnResponse( $switch = True ) {
    return self::dispatch()->returnResponse( $switch );
  }


  public static function filter() {
    $filter = self::configSlice( 'application', 'filter' );
    $p =self::pathinfo();
    if ( isset( $p['extension'] ) ) {
      $extension = strtolower( $p['extension'] );
      if  ( in_array( $extension, $filter ) ) {
        self::_404();
      }
    }

    $modules = self::getModules();
    $mca = array();
    foreach ( $modules as $module ) {
      $controls = self::configSlice( "module_".$module );
      foreach ( $controls as $control => $actions ) {
        $actions = self::configSlice( "module_".$module, $control );
        foreach ( $actions as $action ) {
          $mca[$module][$control][$action] = 1;
        }
      }
    }
    if ( !@$mca[$p['m']][$p['c']][$p['a']] ) {
      self::reRoute( 'error', 'error404' );
    }
  }

  public static function http( $return = True ) {
    self::app()->bootstrap();
    self::dispatch()->returnResponse( $return );

    $response = self::app()->run();
    if ( OHMYZI ) {
      \Sow\xhprof\Ohmyzi::disable();
    } else {
      return  $response;
    }

  }

  public static function shell( $argc, $argv ) {

    self::app()->bootstrap()
    ->getDispatcher( new \Yaf\Request\Simple() );

    $route = array(
      0=>"Index",
      1=>"Index"
    );

    if ( isset( $argv[1] ) ) {
      $ca = explode( '/', $argv[1] );
      $route[0] = $ca[0];
      if ( isset( $ca[1] ) ) $route[1] = $ca[1];
    }
    //  $route =   @ + $route;

    $params = @explode( '/', $argv[2] );



    self::out( $route, $params );

    die();
    $c = 'Help';
    $a = 'Index';
    if ( isset( $route[0] ) ) $c =  $route[0];
    if ( isset( $route[1] ) ) $a =  $route[1];

    var_dump( $params );

    for ( $i=0; $i < count( $params ); $i=$i+2 ) {
      self::request()->setParam( $params[$i], $params[$i+1] );
    }
    self::reRoute( $c, $a );
    return self::app()->run();
  }
  public static function registerPlugin( $plugin ) {
    $plugin = $plugin.'_Plugin';
    return self::dispatch()->registerPlugin( new $plugin() );
  }

  public static function dispatch() {
    return \Yaf\Dispatcher::getInstance();
  }
  public static function request() {
    return self::dispatch()->getRequest();
  }
  public static function params() {
    return self::request()->getParams();
  }
  public static function param( $name ) {
    return self::request()->getParam( $name );
  }
  public static function getMethod() {
    return self::request()->getMethod();
  }
  public static function isCli() {
    return self::request()->isCli();
  }
  public static function disableView( ) {
    return self::dispatch()->disableView();
  }
  public static function enableView( ) {
    return self::dispatch()->enableView();
  }
  public static function reRoute( $c ='Index', $a ='index', $m ='Index'  ) {
    self::request()->module = $m;
    self::request()->controller = \Sow\util\Str::ucfirst( $c );
    self::request()->action = $a;

  }
  public static function pathinfo(  ) {
    $path['m'] = self::request()->module;
    $path['c'] = self::request()->controller;
    $path['a'] = self::request()->action;
    $path['method'] = self::request()->method;
    return $path +pathinfo(  self::request()->getRequestUri() );
  }

  public static function _404( $exit = true ) {
    header( "HTTP/1.1 404 Not Found" );
    header( "Status: 404 Not Found" );
    if ( $exit ) exit;
  }

  public static function location( $url ) {
    header( "location:".$url );
  }
  public static function getModules( ) {
    return self::app()->getModules();
  }
  public static function environ( ) {
    return self::app()->environ();
  }
  public static function session( ) {
    return \Yaf\Session::getInstance() ;
  }

  public static function addConfig( $routes_config ) {

    return self::dispatch()->getRouter()->addConfig( $routes_config );
  }

  public static function isAjax(){
      if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
          return true;         
      else
          return false;        
  }
}
