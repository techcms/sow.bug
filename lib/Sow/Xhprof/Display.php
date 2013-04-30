<?php namespace  Sow\Xhprof;
use Sow\Bug as Y;
class Display {
  public function __construct($data,$dir) {
    include_once "Xhprof/utils/xhprof_lib.php";
    include_once "Xhprof/utils/xhprof_runs.php";
    $xhprof_runs = new \XHProfRuns_Default($dir);
    $run_id = $xhprof_runs->save_run($data, Y::config('xhprof_id'));
    Y::dump($run_id);
    echo "http://localhost/xhprof/xhprof_html/index.php?run={$run_id}&source=xhprof_testing\n";
  }
}