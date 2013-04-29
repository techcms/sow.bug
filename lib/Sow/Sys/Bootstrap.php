<?php namespace Sow\Sys;
use Sow\Bug as Y;
class Bootstrap extends \Yaf\Bootstrap_Abstract  {
    public function _initConfig() {
        $config = Y::config();
        Y::set( "config", $config );
        define( "DEBUG", $config->debug );
        define( "VIEW", $config->view );
        define( "JSON", $config->json );
        define( "FILTER", $config->filter );
        define( "VIEWPATH", $config->viewpath );
        define( "LOGPATH", $config->logpath );
        if (isset($_GET['ohmyzi']) && DEBUG ){
            define( "OHMYZI", True );
            \Sow\Xhprof\Ohmyzi::enable();
        } else {
            define( "OHMYZI", False );
        }


        if ( DEBUG ) {
            ini_set( 'display_errors' , "On" );
            error_reporting( E_ALL );
        } else {
            ini_set( 'display_errors' , "Off" );
            error_reporting( 0 );
        }
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
