<?php namespace Sow\Sys;
class Bootstrap extends \Yaf\Bootstrap_Abstract  {
	public function _initHook() {
		Bug::registerPlugin( 'GateWay' );
	}
    public function _initMVC()
    {
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

       // Y::set('mca', $_mca);
    }
    //为本地目录lib下的文件注册空间名
    public function _initPath()
    {
        //Y::path('V');

    }

	public function _initRoute( Yaf_Dispatcher $dispatcher ) {
		if ( Y::get( "config" )->routes ) {
			Y::addConfig( Y::get( "config" )->routes );
		}
	}
}
