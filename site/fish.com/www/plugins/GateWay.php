<?php
use Sow\Bug as Y;
class GateWayPlugin extends \Yaf\Plugin_Abstract {
  public function routerStartup( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {

  }
  public function routerShutdown( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {

  }
  public function dispatchLoopStartup( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {

  }
  public function preDispatch( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {
    if ( !VIEW ) \Yaf\Dispatcher::getInstance()->disableView();
    
    if ( FILTER ) Y::filter();
    if ( JSON ) header( 'content-type: application/json; $charset=utf-8' );

  }
  public function postDispatch( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {

  }
  public function dispatchLoopShutdown( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {

  }
  public function preResponse( \Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response ) {
  }
}
