<?php namespace Sow\Sys;
use Sow\Bug as Y;
class Bootstrap extends \Yaf\Bootstrap_Abstract  {
    public function _initConfig() {
        $config = Y::config();
        Y::set( "config", $config );
        define( "DEBUG", $config->debug );
        define( "VIEW", $config->view );
        if ( DEBUG ) {
            ini_set( 'display_errors' , "On" );
            error_reporting( E_ALL );
        } else {
            ini_set( 'display_errors' , "Off" );
            error_reporting( 0 );
        }
    }
    public function _initGateWay() {
        Y::registerPlugin( 'GateWay' );
    }
    public function _initMVC() {


        $_mca = array(
            'Index' => array(
                'Index' => array(
                    "index" => 1,
                    "show" => 1,
                ),
                'Info18' => array(
                    "search" => 1,
                    "limit" => 1,
                    "recommend" => 1
                ),
                'Android' => array(
                    "check" => 1,
                    "limit" => 1,
                    "recommend" => 1
                ),
                'Apple' => array(
                    "top" => 1,
                    "show" => 1,
                    "list" => 1,
                    "ioslist" => 1
                ),
                'Login' => array(
                    "index" => 1,
                    "check" => 1,
                    "del" => 1

                ),

            ),
        );

        Y::set('mca', $_mca);
    }
    //为本地目录lib下的文件注册空间名
    public function _initPath() {
        //Y::path('V');

    }

    public function _initRoute( ) {
        if ( Y::get( "config" )->routes ) {
            Y::addConfig( Y::get( "config" )->routes );
        }
    }
}
