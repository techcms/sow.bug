<?php namespace Sow;
class Bug {
  public static function dump(  ) {
    $numargs = func_num_args();
    $arg_list = func_get_args();
    echo '<pre>';
    foreach ( $arg_list as $arg ) {
      var_dump( $arg );
    }
    echo '</pre>';
  }
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
  public static function config( ) {
    return self::app()->getConfig() ;
  }
  public static function Loader() {
    return \Yaf\Loader::getInstance();
  }
  public static function path( ) {
    return self::loader()->registerLocalNameSpace( func_get_args() );
  }
  public static function returnResponse( $switch = True ) {
    return self::dispatch()->returnResponse( $switch );
  }
  public static function http( $bootstrap = True, $return = True ) {

    $config = self::config();
    self::set( "config", $config );
    define( "YDEBUG", $config->debug );

    if ( YDEBUG ) {
      ini_set( 'display_errors' , "On" );
      error_reporting( E_ALL );
    } else {
      ini_set( 'display_errors' , "Off" );
      error_reporting( 0 );
    }

    if ( $bootstrap ) {
      self::app()->bootstrap();
    }

    if ( $return ) {
      self::app()->returnResponse();
    }

    return self::app()->run();
  }

  public static function shell( $argc, $argv ) {


    $config = self::config();
    self::set( "config", $config );
    define( "YDEBUG", $config->debug );

    if ( YDEBUG ) {
      ini_set( 'display_errors' , "On" );
      error_reporting( E_ALL );
    } else {
      ini_set( 'display_errors' , "Off" );
      error_reporting( 0 );
    }

    self::app()->getDispatcher( new \Yaf\Request\Simple() )->disableView();


    $route = @explode( '/', $argv[1] );
    $params = @explode( '/', $argv[2] );

    $c = 'Help';
    $a = 'Index';
    if ( isset( $route[0] ) ) $c =  $route[0];
    if ( isset( $route[1] ) ) $a =  $route[1];


    for ( $i=0; $i < count( $params ); $i=$i+2 ) {
      @self::request()->setParam( $params[$i], $params[$i+1] );
    }
    self::reRoute( $c, $a );
    return self::app()->run();
  }
  public static function registerPlugin( $plugin ) {
    $plugin = $plugin.'Plugin';
    return self::dispatch()->registerPlugin( new $plugin() );
  }

  public static function dispatch() {
    return \Yaf\Dispatcher::getInstance();
  }
  public static function request() {
    return self::dispatch()->getRequest();
  }
  public static function isCli() {
    return self::request()->isCli();
  }
  public static function disableView( ) {
    return self::dispatch()->disableView();
  }
  public static function reRoute( $c ='Index', $a ='index', $m ='Index'  ) {
    self::request()->module = $m;
    self::request()->controller = \Sow\Util\Str::ucfirst( $c );
    self::request()->action = $a;

  }
  /*
  //----------------------------------------------------
  // browser
  //----------------------------------------------------

  public static function browser(  ) {
    $userAgent = strtolower( $_SERVER['HTTP_USER_AGENT'] );

    // Identify the browser. Check Opera and Safari first in case of spoof. Let Google Chrome be identified as Safari.
    if ( preg_match( '/opera/', $userAgent ) ) {
      $name = 'opera';
    }
    elseif ( preg_match( '/msie/', $userAgent ) ) {
      $name = 'msie';
    }
    elseif ( preg_match( '/chrome/', $userAgent ) ) {
      $name = 'chrome';
    }
    elseif ( preg_match( '/webkit/', $userAgent ) ) {
      $name = 'safari';
    }
    elseif ( preg_match( '/mozilla/', $userAgent ) && !preg_match( '/compatible/', $userAgent ) ) {
      $name = 'mozilla';
    }
    else {
      $name = 'unrecognized';
    }

    // What version?
    if ( preg_match( '/.+(?:me|ox|ion|rv|it|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches ) ) {
      $version = $matches[1];
    }
    else {
      $version = 'unknown';
    }

    // Running on what platform?
    if ( preg_match( '/linux/', $userAgent ) ) {
      $platform = 'linux';
    }
    elseif ( preg_match( '/macintosh|mac os x/', $userAgent ) ) {
      $platform = 'mac';
    }
    elseif ( preg_match( '/windows|win32/', $userAgent ) ) {
      $platform = 'windows';
    }
    else {
      $platform = 'unrecognized';
    }

    return array(
      'name'      => $name,
      'version'   => $version,
      'platform'  => $platform,
      'userAgent' => $userAgent
    );
  }
  //----------------------------------------------------
  // new object
  //----------------------------------------------------

  public static function model( $modelName, $params = NULL ) {
    $modelName = $modelName.'Model';
    return new $modelName( $params );
  }

  public static function lib( $libName, $params = NULL ) {
    return new $libName( $params );
  }
  //----------------------------------------------------
  // rquest fliter
  //----------------------------------------------------
  public static function pathinfo(  ) {
    $path['m'] = Y::request()->module;
    $path['c'] = Y::request()->controller;
    $path['a'] = Y::request()->action;
    $path['method'] = Y::request()->method;
    return $path +pathinfo(  Y::request()->getRequestUri() );
  }
  public static function _404( $return = false  ) {
    if ( $return ) {
      // @todo
    }else {
      header( "HTTP/1.1 404 Not Found" );
      header( "Status: 404 Not Found" );
      exit;
    }
  }

  public static function fliter() {
    $filter = array( 'js', 'jpg', 'css', 'png', 'gif', 'html', 'ico' );
    $p =Y::pathinfo();
    if ( isset( $p['extension'] ) ) {
      $extension = strtolower( $p['extension'] );
      if  ( in_array( $extension, $filter ) ) {
        Y::_404();
      }
    }
    $mca = Y::get( 'mca' );
    if ( !@$mca[$p['m']][$p['c']][$p['a']] ) {
      Y::reRoute( 'error', 'error404' );
    }
  }

  public static function run( ) {
    return \Yaf\Application::run();
  }




  public static function session( ) {
    return \Yaf\Session::getInstance() ;
  }




  public static function setDefaultController( $default_controller_name ) {
    return self::dispatch()->setDefaultController( $default_controller_name );
  }
  public static function setDefaultModule( $default_module_name ) {
    return self::dispatch()->setDefaultModule( $default_module_name );
  }
  public static function setDefaultAction( $default_action_name ) {
    return self::dispatch()->setDefaultAction( $default_action_name );
  }
  public static function throwException( $switch = True ) {
    return self::dispatch()->throwException( $switch );
  }
  public static function catchException( $switch = True ) {
    return self::dispatch()->catchException( $switch );
  }



  public static function setAppDirectory( $directory ) {
    $plugin = $plugin.'Plugin';
    return self::dispatch()->setAppDirectory( $directory );
  }
  public static function setRequest( $request ) {
    return self::dispatch()->setRequest( $request );
  }
  public static function setView( $request ) {
    return self::dispatch()->setView( $request );
  }
  public static function initView( $tpl_dir ) {
    return self::dispatch()->initView( $tpl_dir );
  }
  public static function environ( ) {
    return self::app()->environ();
  }

  public static function getModules( ) {
    return self::app()->getModules();
  }
  public static function import( $file ) {
    return \Yaf\Loader::import( $file );
  }

  public static function enableView( ) {
    return self::dispatch()->enableView();
  }
  public static function autoRender( $switch = True ) {
    return self::dispatch()->autoRender( $switch );
  }


  public static function flushInstantly( $switch = True ) {
    return self::dispatch()->flushInstantly( $switch );
  }

  public static function setErrorHandler( $callback, $error_code = "E_ALL | E_STRICT" ) {
    return self::dispatch()->setErrorHandler( $callback, $error_code );
  }



  //----------------------------------------------------
  // router
  //----------------------------------------------------
  public static function router() {
    return self::dispatch()->getRouter();
  }
  public static function addRoute( $name, $route ) {
    return self::router()->addRoute( $name, $route );
  }
  public static function addConfig( $routes_config ) {
    return self::router()->addConfig( $routes_config );
  }
  public static function getRoutes( ) {
    return self::router()->getRoutes( );
  }
  public static function getRoute( $name ) {
    return self::router()->getRoute( $name );
  }
  public static function getCurrentRoute() {
    return self::router()->getCurrentRoute();
  }
  public static function isModuleName( $name ) {
    return self::router()->isModuleName( $name );
  }
  public static function isAjax(){
      if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
          return true;        //是ajax请求
      else
          return false;       //不是ajax请求
  }

*/

}
