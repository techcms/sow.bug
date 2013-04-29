<?php namespace Xhprof;
use Sow\Bug as Y;
class Display {
  public function __construct($data,$dir) {
    include_once "utils/xhprof_lib.php";
    include_once "utils/xhprof_runs.php";
    $xhprof_runs = new \XHProfRuns_Default($dir);
    $run_id = $xhprof_runs->save_run($data, "xhprof_testing");
    echo "http://localhost/xhprof/xhprof_html/index.php?run={$run_id}&source=xhprof_testing\n";
  }
}