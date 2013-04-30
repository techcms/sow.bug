<?php namespace Sow\Xhprof;
use Sow\Bug as Y;

define( 'XHPROF_STRING_PARAM', 1 );
define( 'XHPROF_UINT_PARAM',   2 );
define( 'XHPROF_FLOAT_PARAM',  3 );
define( 'XHPROF_BOOL_PARAM',   4 );
class Graph {
	private $data = NULL;
	private $source = 'xhprof';
	private $func = '';
	private $type = 'png';
	private $threshold = 0.01;
	private $critical = True;

	public function __construct( $data ) {
		$this->data = $data;
		ini_set( 'max_execution_time', 100 );

	}

	function display( ) {
		error_reporting(E_ALL ^ E_NOTICE);
		$script = $this->script( $this->data, $this->threshold, $this->source, 'bitch', $this->func, $this->critical );
		$dot = $this->dot( $script, $this->type );
		$this->mime( $this->type, strlen( $script ) );
		echo $dot;
	}

	function dot( $dot_script, $type ) {
		$descriptorspec = array(
			// stdin is a pipe that the child will read from
			0 => array( "pipe", "r" ),
			// stdout is a pipe that the child will write to
			1 => array( "pipe", "w" ),
			// stderr is a pipe that the child will write to
			2 => array( "pipe", "w" )
		);

		$cmd = " dot -T".$type;

		$process = proc_open( $cmd, $descriptorspec, $pipes, "/tmp", array() );
		if ( is_resource( $process ) ) {
			fwrite( $pipes[0], $dot_script );
			fclose( $pipes[0] );

			$output = stream_get_contents( $pipes[1] );

			$err = stream_get_contents( $pipes[2] );
			if ( !empty( $err ) ) {
				print "failed to execute cmd: \"$cmd\". stderr: `$err'\n";
				exit;
			}

			fclose( $pipes[2] );
			fclose( $pipes[1] );
			proc_close( $process );
			return $output;
		}
		print "failed to execute cmd \"$cmd\"";
		exit();
	}




	function script( $raw_data, $threshold, $source, $page,
		$func, $critical_path, $right=null,
		$left=null ) {

		$max_width = 5;
		$max_height = 3.5;
		$max_fontsize = 35;
		$max_sizing_ratio = 20;

		$totals;

		if ( $left === null ) {
			// init_metrics($raw_data, null, null);
		}
		$sym_table = $this->flatinfo( $raw_data, $totals );

		if ( $critical_path ) {
			$children_table = $this->xhprof_get_children_table( $raw_data );
			$node = "main()";
			$path = array();
			$path_edges = array();
			$visited = array();
			while ( $node ) {
				$visited[$node] = true;
				if ( isset( $children_table[$node] ) ) {
					$max_child = null;
					foreach ( $children_table[$node] as $child ) {

						if ( isset( $visited[$child] ) ) {
							continue;
						}
						if ( $max_child === null ||
							abs( $raw_data[$this->xhprof_build_parent_child_key( $node,
									$child )]["wt"] ) >
							abs( $raw_data[$this->xhprof_build_parent_child_key( $node,
									$max_child )]["wt"] ) ) {
							$max_child = $child;
						}
					}
					if ( $max_child !== null ) {
						$path[$max_child] = true;
						$path_edges[$this->xhprof_build_parent_child_key( $node, $max_child )] = true;
					}
					$node = $max_child;
				} else {
					$node = null;
				}
			}
		}

		// if it is a benchmark callgraph, we make the benchmarked function the root.
		if ( $source == "bm" && array_key_exists( "main()", $sym_table ) ) {
			$total_times = $sym_table["main()"]["ct"];
			$remove_funcs = array( "main()",
				"hotprofiler_disable",
				"call_user_func_array",
				"xhprof_disable" );

			foreach ( $remove_funcs as $cur_del_func ) {
				if ( array_key_exists( $cur_del_func, $sym_table ) &&
					$sym_table[$cur_del_func]["ct"] == $total_times ) {
					unset( $sym_table[$cur_del_func] );
				}
			}
		}

		// use the function to filter out irrelevant functions.
		if ( !empty( $func ) ) {
			$interested_funcs = array();
			foreach ( $raw_data as $parent_child => $info ) {
				list( $parent, $child ) = xhprof_parse_parent_child( $parent_child );
				if ( $parent == $func || $child == $func ) {
					$interested_funcs[$parent] = 1;
					$interested_funcs[$child] = 1;
				}
			}
			foreach ( $sym_table as $symbol => $info ) {
				if ( !array_key_exists( $symbol, $interested_funcs ) ) {
					unset( $sym_table[$symbol] );
				}
			}
		}

		$result = "digraph call_graph {\n";

		// Filter out functions whose exclusive time ratio is below threshold, and
		// also assign a unique integer id for each function to be generated. In the
		// meantime, find the function with the most exclusive time (potentially the
		// performance bottleneck).
		$cur_id = 0; $max_wt = 0;
		foreach ( $sym_table as $symbol => $info ) {
			if ( empty( $func ) && abs( $info["wt"] / $totals["wt"] ) < $threshold ) {
				unset( $sym_table[$symbol] );
				continue;
			}
			if ( $max_wt == 0 || $max_wt < abs( $info["excl_wt"] ) ) {
				$max_wt = abs( $info["excl_wt"] );
			}
			$sym_table[$symbol]["id"] = $cur_id;
			$cur_id ++;
		}

		// Generate all nodes' information.
		foreach ( $sym_table as $symbol => $info ) {
			if ( $info["excl_wt"] == 0 ) {
				$sizing_factor = $max_sizing_ratio;
			} else {
				$sizing_factor = $max_wt / abs( $info["excl_wt"] ) ;
				if ( $sizing_factor > $max_sizing_ratio ) {
					$sizing_factor = $max_sizing_ratio;
				}
			}
			$fillcolor = ( ( $sizing_factor < 1.5 ) ?
				", style=filled, fillcolor=red" : "" );

			if ( $critical_path ) {
				// highlight nodes along critical path.
				if ( !$fillcolor && array_key_exists( $symbol, $path ) ) {
					$fillcolor = ", style=filled, fillcolor=yellow";
				}
			}

			$fontsize = ", fontsize="
				.(int)( $max_fontsize / ( ( $sizing_factor - 1 ) / 10 + 1 ) );

			$width = ", width=".sprintf( "%.1f", $max_width / $sizing_factor );
			$height = ", height=".sprintf( "%.1f", $max_height / $sizing_factor );

			if ( $symbol == "main()" ) {
				$shape = "octagon";
				$name = "Total: ".( $totals["wt"] / 1000.0 )." ms\\n";
				$name .= addslashes( isset( $page ) ? $page : $symbol );
			} else {
				$shape = "box";
				$name = addslashes( $symbol )."\\nInc: ". sprintf( "%.3f", $info["wt"] / 1000 ) .
					" ms (" . sprintf( "%.1f%%", 100 * $info["wt"] / $totals["wt"] ).")";
			}
			if ( $left === null ) {
				$label = ", label=\"".$name."\\nExcl: "
					.( sprintf( "%.3f", $info["excl_wt"] / 1000.0 ) )." ms ("
					.sprintf( "%.1f%%", 100 * $info["excl_wt"] / $totals["wt"] )
					. ")\\n".$info["ct"]." total calls\"";
			} else {
				if ( isset( $left[$symbol] ) && isset( $right[$symbol] ) ) {
					$label = ", label=\"".addslashes( $symbol ).
						"\\nInc: ".( sprintf( "%.3f", $left[$symbol]["wt"] / 1000.0 ) )
						." ms - "
						.( sprintf( "%.3f", $right[$symbol]["wt"] / 1000.0 ) )." ms = "
						.( sprintf( "%.3f", $info["wt"] / 1000.0 ) )." ms".
						"\\nExcl: "
						.( sprintf( "%.3f", $left[$symbol]["excl_wt"] / 1000.0 ) )
						." ms - ".( sprintf( "%.3f", $right[$symbol]["excl_wt"] / 1000.0 ) )
						." ms = ".( sprintf( "%.3f", $info["excl_wt"] / 1000.0 ) )." ms".
						"\\nCalls: ".( sprintf( "%.3f", $left[$symbol]["ct"] ) )." - "
						.( sprintf( "%.3f", $right[$symbol]["ct"] ) )." = "
						.( sprintf( "%.3f", $info["ct"] ) )."\"";
				} else if ( isset( $left[$symbol] ) ) {
						$label = ", label=\"".addslashes( $symbol ).
							"\\nInc: ".( sprintf( "%.3f", $left[$symbol]["wt"] / 1000.0 ) )
							." ms - 0 ms = ".( sprintf( "%.3f", $info["wt"] / 1000.0 ) )
							." ms"."\\nExcl: "
							.( sprintf( "%.3f", $left[$symbol]["excl_wt"] / 1000.0 ) )
							." ms - 0 ms = "
							.( sprintf( "%.3f", $info["excl_wt"] / 1000.0 ) )." ms".
							"\\nCalls: ".( sprintf( "%.3f", $left[$symbol]["ct"] ) )." - 0 = "
							.( sprintf( "%.3f", $info["ct"] ) )."\"";
					} else {
					$label = ", label=\"".addslashes( $symbol ).
						"\\nInc: 0 ms - "
						.( sprintf( "%.3f", $right[$symbol]["wt"] / 1000.0 ) )
						." ms = ".( sprintf( "%.3f", $info["wt"] / 1000.0 ) )." ms".
						"\\nExcl: 0 ms - "
						.( sprintf( "%.3f", $right[$symbol]["excl_wt"] / 1000.0 ) )
						." ms = ".( sprintf( "%.3f", $info["excl_wt"] / 1000.0 ) )." ms".
						"\\nCalls: 0 - ".( sprintf( "%.3f", $right[$symbol]["ct"] ) )
						." = ".( sprintf( "%.3f", $info["ct"] ) )."\"";
				}
			}
			$result .= "N" . $sym_table[$symbol]["id"];
			$result .= "[shape=$shape ".$label.$width
				.$height.$fontsize.$fillcolor."];\n";
		}

		// Generate all the edges' information.
		foreach ( $raw_data as $parent_child => $info ) {
			list( $parent, $child ) = $this->xhprof_parse_parent_child( $parent_child );

			if ( isset( $sym_table[$parent] ) && isset( $sym_table[$child] ) &&
				( empty( $func ) ||
					( !empty( $func ) && ( $parent == $func || $child == $func ) ) ) ) {

				$label = $info["ct"] == 1 ? $info["ct"]." call" : $info["ct"]." calls";

				$headlabel = $sym_table[$child]["wt"] > 0 ?
					sprintf( "%.1f%%", 100 * $info["wt"]
					/ $sym_table[$child]["wt"] )
					: "0.0%";

				$taillabel = ( $sym_table[$parent]["wt"] > 0 ) ?
					sprintf( "%.1f%%",
					100 * $info["wt"] /
					( $sym_table[$parent]["wt"] - $sym_table["$parent"]["excl_wt"] ) )
					: "0.0%";

				$linewidth = 1;
				$arrow_size = 1;

				if ( $critical_path &&
					isset( $path_edges[$this->xhprof_build_parent_child_key( $parent, $child )] ) ) {
					$linewidth = 10; $arrow_size = 2;
				}

				$result .= "N" . $sym_table[$parent]["id"] . " -> N"
					. $sym_table[$child]["id"];
				$result .= "[arrowsize=$arrow_size, style=\"setlinewidth($linewidth)\","
					." label=\""
					.$label."\", headlabel=\"".$headlabel
					."\", taillabel=\"".$taillabel."\" ]";
				$result .= ";\n";

			}
		}
		$result = $result . "\n}";

		return $result;
	}



	function mime( $type, $length ) {
		switch ( $type ) {
		case 'jpg':
			$mime = 'image/jpeg';
			break;
		case 'gif':
			$mime = 'image/gif';
			break;
		case 'png':
			$mime = 'image/png';
			break;
		case 'svg':
			$mime = 'image/svg+xml'; // content type for scalable vector graphic
			break;
		case 'ps':
			$mime = 'application/postscript';
		default:
			$mime = false;
		}

		if ( $mime ) {
			header( 'Content-type: '.$mime, true );
			header( 'Content-length: '.(string)$length, true );
		}
	}


	function flatinfo( $raw_data, &$overall_totals ) {

		global $display_calls;

		$metrics = $this->metrics( $raw_data );

		$overall_totals = array( "ct" => 0,
			"wt" => 0,
			"ut" => 0,
			"st" => 0,
			"cpu" => 0,
			"mu" => 0,
			"pmu" => 0,
			"samples" => 0
		);

		// compute inclusive times for each function
		$symbol_tab = $this->inclusive_times( $raw_data );

		/* total metric value is the metric value for "main()" */
		foreach ( $metrics as $metric ) {
			$overall_totals[$metric] = $symbol_tab["main()"][$metric];
		}

		/*
   * initialize exclusive (self) metric value to inclusive metric value
   * to start with.
   * In the same pass, also add up the total number of function calls.
   */
		foreach ( $symbol_tab as $symbol => $info ) {
			foreach ( $metrics as $metric ) {
				$symbol_tab[$symbol]["excl_" . $metric] = $symbol_tab[$symbol][$metric];
			}
			if ( $display_calls ) {
				/* keep track of total number of calls */
				$overall_totals["ct"] += $info["ct"];
			}
		}

		/* adjust exclusive times by deducting inclusive time of children */
		foreach ( $raw_data as $parent_child => $info ) {
			list( $parent, $child ) = $this->xhprof_parse_parent_child( $parent_child );

			if ( $parent ) {
				foreach ( $metrics as $metric ) {
					// make sure the parent exists hasn't been pruned.
					if ( isset( $symbol_tab[$parent] ) ) {
						$symbol_tab[$parent]["excl_" . $metric] -= $info[$metric];
					}
				}
			}
		}

		return $symbol_tab;
	}

	function metrics( $xhprof_data ) {

		// get list of valid metrics
		$possible_metrics = $this->possible_metrics();

		// return those that are present in the raw data.
		// We'll just look at the root of the subtree for this.
		$metrics = array();
		foreach ( $possible_metrics as $metric => $desc ) {
			if ( isset( $xhprof_data["main()"][$metric] ) ) {
				$metrics[] = $metric;
			}
		}

		return $metrics;
	}
	function possible_metrics() {
		static $possible_metrics =
			array( "wt" => array( "Wall", "microsecs", "walltime" ),
			"ut" => array( "User", "microsecs", "user cpu time" ),
			"st" => array( "Sys", "microsecs", "system cpu time" ),
			"cpu" => array( "Cpu", "microsecs", "cpu time" ),
			"mu" => array( "MUse", "bytes", "memory usage" ),
			"pmu" => array( "PMUse", "bytes", "peak memory usage" ),
			"samples" => array( "Samples", "samples", "cpu time" ) );
		return $possible_metrics;
	}
	function inclusive_times( $raw_data ) {
		global $display_calls;

		$metrics = $this->metrics( $raw_data );

		$symbol_tab = array();

		/*
   * First compute inclusive time for each function and total
   * call count for each function across all parents the
   * function is called from.
   */
		foreach ( $raw_data as $parent_child => $info ) {

			list( $parent, $child ) = $this->xhprof_parse_parent_child( $parent_child );

			if ( $parent == $child ) {
				/*
       * XHProf PHP extension should never trigger this situation any more.
       * Recursion is handled in the XHProf PHP extension by giving nested
       * calls a unique recursion-depth appended name (for example, foo@1).
       */
				xhprof_error( "Error in Raw Data: parent & child are both: $parent" );
				return;
			}

			if ( !isset( $symbol_tab[$child] ) ) {

				if ( $display_calls ) {
					$symbol_tab[$child] = array( "ct" => $info["ct"] );
				} else {
					$symbol_tab[$child] = array();
				}
				foreach ( $metrics as $metric ) {
					$symbol_tab[$child][$metric] = $info[$metric];
				}
			} else {
				if ( $display_calls ) {
					/* increment call count for this child */
					$symbol_tab[$child]["ct"] += $info["ct"];
				}

				/* update inclusive times/metric for this child  */
				foreach ( $metrics as $metric ) {
					$symbol_tab[$child][$metric] += $info[$metric];
				}
			}
		}

		return $symbol_tab;
	}

function xhprof_parse_parent_child($parent_child) {
  $ret = explode("==>", $parent_child);

  // Return if both parent and child are set
  if (isset($ret[1])) {
    return $ret;
  }

  return array(null, $ret[0]);
}
function xhprof_get_children_table($raw_data) {
  $children_table = array();
  foreach ($raw_data as $parent_child => $info) {
    list($parent, $child) = $this->xhprof_parse_parent_child($parent_child);
    if (!isset($children_table[$parent])) {
      $children_table[$parent] = array($child);
    } else {
      $children_table[$parent][] = $child;
    }
  }
  return $children_table;
}
function xhprof_build_parent_child_key($parent, $child) {
  if ($parent) {
    return $parent . "==>" . $child;
  } else {
    return $child;
  }
}
}
