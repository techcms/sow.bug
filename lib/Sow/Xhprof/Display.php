<?php namespace  Sow\Xhprof;
use Sow\Bug as Y;
class Display {
  public function __construct($data,$dir) {
    include_once "Xhprof/utils/xhprof_lib.php";
    include_once "Xhprof/utils/xhprof_runs.php";
    $xhprof_runs = new \XHProfRuns_Default($dir);
    $run_id = $xhprof_runs->save_run($data, Y::config('xhprof_id'));



    $url = Y::application_ini('baseUri')."xhprof/html/run/".$run_id."/source/".Y::config('xhprof_id');


    Y::location($url);
  }
}