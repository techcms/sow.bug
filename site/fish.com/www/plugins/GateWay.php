<?php
use Sow\Bug as Y;
class GateWayPlugin extends \Yaf\Plugin_Abstract {
  public function routerStartup( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {
    Y::dump( "routerStartup" );
    Y::dump( Y::request() );
  }
  public function routerShutdown( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {
    Y::dump( "routerShutdown" );
    Y::dump( Y::request() );
  }
  public function dispatchLoopStartup( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {
    Y::dump( "dispatchLoopStartup" );
    Y::dump( Y::request() );
  }
  public function preDispatch( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {
    Y::dump( "preDispatch" );
    Y::dump( Y::request() );
    if ( !VIEW ) \Yaf\Dispatcher::getInstance()->disableView();
    //header('content-type: application/json; charset=utf-8');
  }
  public function postDispatch( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {
    Y::dump( "postDispatch" );
    Y::dump( Y::request() );
  }
  public function dispatchLoopShutdown( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {
    Y::dump( "dispatchLoopShutdown" );
    Y::dump( Y::request() );
  }
  public function preResponse( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {
    Y::dump( "preResponse" );
    Y::dump( Y::request() );
  }
}
