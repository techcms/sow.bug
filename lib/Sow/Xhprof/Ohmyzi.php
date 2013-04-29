<?php namespace Sow\Xhprof;
use Sow\Bug as Y;
class Ohmyzi {
  public static function enable() {
    $ignored  =    array(
        'ignored_functions' => array(
          'Sow\Xhprof\Ohmyzi::disable',
          'Sow\Bug::registerPlugin',
          'Sow\Sys\Bootstrap::_initGateWay',
        )
      );

    xhprof_enable( XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY ,$ignored);
  }
  public static function disable() {
    $data = xhprof_disable();
    $logpath = LOGPATH."/xhprof/".date("Ymd",time());
    if (!is_dir($logpath)){
          mkdir($logpath, 0755);
    }
    $graph = new  \Xhprof\Display($data,$logpath);
  }
}