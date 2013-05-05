<?php namespace  Sow\Xhprof;
use Sow\Bug as Y;
class Debug {
  public function __construct($data,$dir) {
    include "Xhprof/utils/xhprof_lib.php";
    include "Xhprof/utils/xhprof_runs.php";
    $xhprof_runs = new \XHProfRuns_Default($dir);
    $run_id = $xhprof_runs->save_run($data, Y::config('xhprof_id'));
    Ohmyzi::graph($this->GET( 'run' ));
  }
}