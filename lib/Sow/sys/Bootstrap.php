<?php namespace Sow\sys;
use Sow\bug as Y;
class Bootstrap extends \Yaf\Bootstrap_Abstract  {
    public function _initConfig() {
        $config = Y::config();
        Y::set( "config", $config );

        if ( isset( $_GET['ohmyzi'] ) && $config->debug ) {
            define( "OHMYZI", True );
            \Sow\xhprof\Ohmyzi::enable();
        } else {
            define( "OHMYZI", False );
        }

        if ( $config->debug ) {
            ini_set( 'display_errors' , "On" );
            error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
        } else {
            ini_set( 'display_errors' , "Off" );
            error_reporting( 0 );
        }
        ini_set('include_path',ini_get('yaf.library').':'.Y::library());
    }
    public function _initGateWay() {
        $plugins = Y::configSlice( 'application', 'plugins' );
        foreach ( $plugins as $plugin ) {
            Y::registerPlugin( $plugin );
        }

    }
    public function _initRoute( ) {
        if ( Y::get( "config" )->routes ) {
            Y::addConfig( Y::get( "config" )->routes );
        }
    }
}
