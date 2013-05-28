<?php

/**
 * Build URL query based on an associative and, or indexed array.
 *
 * This is a convenient function for easily building url queries. It sets the
 * separator to '&' and uses _http_build_query() function.
 *
 * @see _http_build_query() Used to build the query
 * @link http://us2.php.net/manual/en/function.http-build-query.php more on what
 *		http_build_query() does.
 *
 * @since 2.3.0
 *
 * @param array $data URL-encode key/value pairs.
 * @return string URL encoded string
 */
function build_query( $data ) {
	return _http_build_query( $data, null, '&', '', false );
}

// from php.net (modified by Mark Jaquith to behave like the native PHP5 function)
function _http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true) {
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		if ( $urlencode)
			$k = urlencode($k);
		if ( is_int($k) && $prefix != null )
			$k = $prefix.$k;
		if ( !empty($key) )
			$k = $key . '%5B' . $k . '%5D';
		if ( $v === NULL )
			continue;
		elseif ( $v === FALSE )
			$v = '0';

		if ( is_array($v) || is_object($v) )
			array_push($ret,_http_build_query($v, '', $sep, $k, $urlencode));
		elseif ( $urlencode )
			array_push($ret, $k.'='.urlencode($v));
		else
			array_push($ret, $k.'='.$v);
	}

	if ( NULL === $sep )
		$sep = ini_get('arg_separator.output');

	return implode($sep, $ret);
}

/**
 * Retrieve a modified URL query string.
 *
 * You can rebuild the URL and append a new query variable to the URL query by
 * using this function. You can also retrieve the full URL with query data.
 *
 * Adding a single key & value or an associative array. Setting a key value to
 * emptystring removes the key. Omitting oldquery_or_uri uses the $_SERVER
 * value.
 *
 * @since 1.5.0
 *
 * @param mixed $param1 Either newkey or an associative_array
 * @param mixed $param2 Either newvalue or oldquery or uri
 * @param mixed $param3 Optional. Old query or uri
 * @return string New URL query string.
 */
function add_query_arg() {
	$ret = '';
	if ( is_array( func_get_arg(0) ) ) {
		if ( @func_num_args() < 2 || false === @func_get_arg( 1 ) )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = @func_get_arg( 1 );
	} else {
		if ( @func_num_args() < 3 || false === @func_get_arg( 2 ) )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = @func_get_arg( 2 );
	}

	if ( $frag = strstr( $uri, '#' ) )
		$uri = substr( $uri, 0, -strlen( $frag ) );
	else
		$frag = '';

	if ( preg_match( '|^https?://|i', $uri, $matches ) ) {
		$protocol = $matches[0];
		$uri = substr( $uri, strlen( $protocol ) );
	} else {
		$protocol = '';
	}

	if ( strpos( $uri, '?' ) !== false ) {
		$parts = explode( '?', $uri, 2 );
		if ( 1 == count( $parts ) ) {
			$base = '?';
			$query = $parts[0];
		} else {
			$base = $parts[0] . '?';
			$query = $parts[1];
		}
	} elseif ( !empty( $protocol ) || strpos( $uri, '=' ) === false ) {
		$base = $uri . '?';
		$query = '';
	} else {
		$base = '';
		$query = $uri;
	}

	mp_parse_str( $query, $qs );
	$qs = urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
	if ( is_array( func_get_arg( 0 ) ) ) {
		$kayvees = func_get_arg( 0 );
		$qs = array_merge( $qs, $kayvees );
	} else {
		$qs[func_get_arg( 0 )] = func_get_arg( 1 );
	}

	foreach ( (array) $qs as $k => $v ) {
		if ( $v === false )
			unset( $qs[$k] );
	}

	$ret = build_query( $qs );
	$ret = trim( $ret, '?' );
	$ret = preg_replace( '#=(&|$)#', '$1', $ret );
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim( $ret, '?' );
	return $ret;
}

/**
 * Removes an item or list from the query string.
 *
 * @since 1.5.0
 *
 * @param string|array $key Query key or keys to remove.
 * @param bool $query When false uses the $_SERVER value.
 * @return string New URL query string.
 */
function remove_query_arg( $key, $query=false ) {
	if ( is_array( $key ) ) { // removing multiple keys
		foreach ( $key as $k )
			$query = add_query_arg( $k, false, $query );
		return $query;
	}
	return add_query_arg( $key, false, $query );
}

/**
 * BackPress Scripts enqueue.
 *
 * These classes were refactored from the WordPress WP_Scripts and WordPress
 * script enqueue API.
 *
 * @package BackPress
 * @since r74
 */

/**
 * BackPress enqueued dependiences class.
 *
 * @package BackPress
 * @uses _WP_Dependency
 * @since r74
 */
class WP_Dependencies {
	var $registered = array();
	var $queue = array();
	var $to_do = array();
	var $done = array();
	var $args = array();
	var $groups = array();
	var $group = 0;

	function WP_Dependencies() {
		$args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
	}

	function __construct() {}

	/**
	 * Do the dependencies
	 *
	 * Process the items passed to it or the queue.  Processes all dependencies.
	 *
	 * @param mixed handles (optional) items to be processed.  (void) processes queue, (string) process that item, (array of strings) process those items
	 * @return array Items that have been processed
	 */
	function do_items( $handles = false, $group = false ) {
		// Print the queue if nothing is passed.  If a string is passed, print that script.  If an array is passed, print those scripts.
		$handles = false === $handles ? $this->queue : (array) $handles;
		$this->all_deps( $handles );

		foreach( $this->to_do as $key => $handle ) {
			if ( !in_array($handle, $this->done) && isset($this->registered[$handle]) ) {

				if ( ! $this->registered[$handle]->src ) { // Defines a group.
					$this->done[] = $handle;
					continue;
				}

				if ( $this->do_item( $handle, $group ) )
					$this->done[] = $handle;

				unset( $this->to_do[$key] );
			}
		}

		return $this->done;
	}

	function do_item( $handle ) {
		return isset($this->registered[$handle]);
	}

	/**
	 * Determines dependencies
	 *
	 * Recursively builds array of items to process taking dependencies into account.  Does NOT catch infinite loops.
	 *

	 * @param mixed handles Accepts (string) dep name or (array of strings) dep names
	 * @param bool recursion Used internally when function calls itself
	 */
	function all_deps( $handles, $recursion = false, $group = false ) {
		if ( !$handles = (array) $handles )
			return false;

		foreach ( $handles as $handle ) {
			$handle_parts = explode('?', $handle);
			$handle = $handle_parts[0];
			$queued = in_array($handle, $this->to_do, true);

			if ( in_array($handle, $this->done, true) ) // Already done
				continue;

			$moved = $this->set_group( $handle, $recursion, $group );

			if ( $queued && !$moved ) // already queued and in the right group
				continue;

			$keep_going = true;
			if ( !isset($this->registered[$handle]) )
				$keep_going = false; // Script doesn't exist
			elseif ( $this->registered[$handle]->deps && array_diff($this->registered[$handle]->deps, array_keys($this->registered)) )
				$keep_going = false; // Script requires deps which don't exist (not a necessary check.  efficiency?)
			elseif ( $this->registered[$handle]->deps && !$this->all_deps( $this->registered[$handle]->deps, true, $group ) )
				$keep_going = false; // Script requires deps which don't exist

			if ( !$keep_going ) { // Either script or its deps don't exist.
				if ( $recursion )
					return false; // Abort this branch.
				else
					continue; // We're at the top level.  Move on to the next one.
			}

			if ( $queued ) // Already grobbed it and its deps
				continue;

			if ( isset($handle_parts[1]) )
				$this->args[$handle] = $handle_parts[1];

			$this->to_do[] = $handle;
		}

		return true;
	}

	/**
	 * Adds item
	 *
	 * Adds the item only if no item of that name already exists
	 *
	 * @param string handle Script name
	 * @param string src Script url
	 * @param array deps (optional) Array of script names on which this script depends
	 * @param string ver (optional) Script version (used for cache busting)
	 * @return array Hierarchical array of dependencies
	 */
	function add( $handle, $src, $deps = array(), $ver = false, $args = null ) {
		if ( isset($this->registered[$handle]) )
			return false;
		$this->registered[$handle] = new _WP_Dependency( $handle, $src, $deps, $ver, $args );
		return true;
	}

	/**
	 * Adds extra data
	 *
	 * Adds data only if script has already been added
	 *
	 * @param string handle Script name
	 * @param string data_name Name of object in which to store extra data
	 * @param array data Array of extra data
	 * @return bool success
	 */
	function add_data( $handle, $data_name, $data ) {
		if ( !isset($this->registered[$handle]) )
			return false;
		return $this->registered[$handle]->add_data( $data_name, $data );
	}

	function remove( $handles ) {
		foreach ( (array) $handles as $handle )
			unset($this->registered[$handle]);
	}

	function enqueue( $handles ) {
		foreach ( (array) $handles as $handle ) {
			$handle = explode('?', $handle);
			if ( !in_array($handle[0], $this->queue) && isset($this->registered[$handle[0]]) ) {
				$this->queue[] = $handle[0];
				if ( isset($handle[1]) )
					$this->args[$handle[0]] = $handle[1];
			}
		}
	}

	function dequeue( $handles ) {
		foreach ( (array) $handles as $handle ) {
			$handle = explode('?', $handle);
			$key = array_search($handle[0], $this->queue);
			if ( false !== $key ) {
				unset($this->queue[$key]);
				unset($this->args[$handle[0]]);
			}
		}
	}

	function query( $handle, $list = 'registered' ) { // registered, queue, done, to_do
		switch ( $list ) :
		case 'registered':
		case 'scripts': // back compat
			if ( isset($this->registered[$handle]) )
				return $this->registered[$handle];
			break;
		case 'to_print': // back compat
		case 'printed': // back compat
			if ( 'to_print' == $list )
				$list = 'to_do';
			else
				$list = 'printed';
		default:
			if ( in_array($handle, $this->$list) )
				return true;
			break;
		endswitch;
		return false;
	}

	function set_group( $handle, $recursion, $group ) {
		$group = (int) $group;

		if ( $recursion )
			$group = min($this->group, $group);
		else
			$this->group = $group;

		if ( isset($this->groups[$handle]) && $this->groups[$handle] <= $group )
			return false;

		$this->groups[$handle] = $group;
		return true;
	}

}

class _WP_Dependency {
	var $handle;
	var $src;
	var $deps = array();
	var $ver = false;
	var $args = null;

	var $extra = array();

	function _WP_Dependency() {
		@list($this->handle, $this->src, $this->deps, $this->ver, $this->args) = func_get_args();
		if ( !is_array($this->deps) )
			$this->deps = array();
	}

	function add_data( $name, $data ) {
		if ( !is_scalar($name) )
			return false;
		$this->extra[$name] = $data;
		return true;
	}
}

/**
 * BackPress Scripts enqueue.
 *
 * These classes were refactored from the WordPress WP_Scripts and WordPress
 * script enqueue API.
 *
 * @package BackPress
 * @since r16
 */

/**
 * BackPress Scripts enqueue class.
 *
 * @package BackPress
 * @uses WP_Dependencies
 * @since r16
 */
class WP_Scripts extends WP_Dependencies {
	var $base_url; // Full URL with trailing slash
	var $content_url;
	var $default_version;
	var $in_footer = array();
	var $concat = '';
	var $concat_version = '';
	var $do_concat = false;
	var $print_html = '';
	var $print_code = '';
	var $ext_handles = '';
	var $ext_version = '';
	var $default_dirs;

	function __construct() {
		do_action_ref_array( 'mp_default_scripts', array(&$this) );
	}

	/**
	 * Prints scripts
	 *
	 * Prints the scripts passed to it or the print queue.  Also prints all necessary dependencies.
	 *
	 * @param mixed handles (optional) Scripts to be printed.  (void) prints queue, (string) prints that script, (array of strings) prints those scripts.
	 * @param int group (optional) If scripts were queued in groups prints this group number.
	 * @return array Scripts that have been printed
	 */
	function print_scripts( $handles = false, $group = false ) {
		return $this->do_items( $handles, $group );
	}

	function print_scripts_l10n( $handle, $echo = true ) {
		if ( empty($this->registered[$handle]->extra['l10n']) || empty($this->registered[$handle]->extra['l10n'][0]) || !is_array($this->registered[$handle]->extra['l10n'][1]) )
			return false;

		$object_name = $this->registered[$handle]->extra['l10n'][0];

		$data = "var $object_name = {\n";
		$eol = '';
		foreach ( $this->registered[$handle]->extra['l10n'][1] as $var => $val ) {
			if ( 'l10n_print_after' == $var ) {
				$after = $val;
				continue;
			}
			$data .= "$eol\t$var: \"" . esc_js( $val ) . '"';
			$eol = ",\n";
		}
		$data .= "\n};\n";
		$data .= isset($after) ? "$after\n" : '';

		if ( $echo ) {
			echo "<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n";
			echo $data;
			echo "/* ]]> */\n";
			echo "</script>\n";
			return true;
		} else {
			return $data;
		}
	}

	function do_item( $handle, $group = false ) {
		if ( !parent::do_item($handle) )
			return false;

		if ( 0 === $group && $this->groups[$handle] > 0 ) {
			$this->in_footer[] = $handle;
			return false;
		}

		if ( false === $group && in_array($handle, $this->in_footer, true) )
			$this->in_footer = array_diff( $this->in_footer, (array) $handle );

		$ver = $this->registered[$handle]->ver ? $this->registered[$handle]->ver : $this->default_version;
		if ( isset($this->args[$handle]) )
			$ver .= '&amp;' . $this->args[$handle];

		$src = $this->registered[$handle]->src;

		if ( $this->do_concat ) {
			$srce = apply_filters( 'script_loader_src', $src, $handle );
			if ( $this->in_default_dir($srce) ) {
				$this->print_code .= $this->print_scripts_l10n( $handle, false );
				$this->concat .= "$handle,";
				$this->concat_version .= "$handle$ver";
				return true;
			} else {
				$this->ext_handles .= "$handle,";
				$this->ext_version .= "$handle$ver";
			}
		}

		$this->print_scripts_l10n( $handle );
		if ( !preg_match('|^https?://|', $src) && ! ( $this->content_url && 0 === strpos($src, $this->content_url) ) ) {
			$src = $this->base_url . $src;
		}

		$src = add_query_arg('ver', $ver, $src);
		$src = esc_url(apply_filters( 'script_loader_src', $src, $handle ));

		if ( $this->do_concat )
			$this->print_html .= "<script type='text/javascript' src='$src'></script>\n";
		else
			echo "<script type='text/javascript' src='$src'></script>\n";

		return true;
	}

	/**
	 * Localizes a script
	 *
	 * Localizes only if script has already been added
	 *
	 * @param string handle Script name
	 * @param string object_name Name of JS object to hold l10n info
	 * @param array l10n Array of JS var name => localized string
	 * @return bool Successful localization
	 */
	function localize( $handle, $object_name, $l10n ) {
		if ( !$object_name || !$l10n )
			return false;
		return $this->add_data( $handle, 'l10n', array( $object_name, $l10n ) );
	}

	function set_group( $handle, $recursion, $group = false ) {
		$grp = isset($this->registered[$handle]->extra['group']) ? (int) $this->registered[$handle]->extra['group'] : 0;
		if ( false !== $group && $grp > $group )
			$grp = $group;

		return parent::set_group( $handle, $recursion, $grp );
	}

	function all_deps( $handles, $recursion = false, $group = false ) {
		$r = parent::all_deps( $handles, $recursion );
		if ( !$recursion )
			$this->to_do = apply_filters( 'print_scripts_array', $this->to_do );
		return $r;
	}

	function do_head_items() {
		$this->do_items(false, 0);
		return $this->done;
	}

	function do_footer_items() {
		if ( !empty($this->in_footer) ) {
			foreach( $this->in_footer as $key => $handle ) {
				if ( !in_array($handle, $this->done, true) && isset($this->registered[$handle]) ) {
					$this->do_item($handle);
					$this->done[] = $handle;
					unset( $this->in_footer[$key] );
				}
			}
		}
		return $this->done;
	}

	function in_default_dir($src) {
		if ( ! $this->default_dirs )
			return true;

		foreach ( (array) $this->default_dirs as $test ) {
			if ( 0 === strpos($src, $test) )
				return true;
		}
		return false;
	}

	function reset() {
		$this->do_concat = false;
		$this->print_code = '';
		$this->concat = '';
		$this->concat_version = '';
		$this->print_html = '';
		$this->ext_version = '';
		$this->ext_handles = '';
	}
}


/**
 * BackPress script procedural API.
 *
 * @package BackPress
 * @since r16
 */

/**
 * Prints script tags in document head.
 *
 * Called by admin-header.php and by mp_head hook. Since it is called by mp_head
 * on every page load, the function does not instantiate the WP_Scripts object
 * unless script names are explicitly passed. Does make use of already
 * instantiated $mp_scripts if present. Use provided mp_print_scripts hook to
 * register/enqueue new scripts.
 *
 * @since r16
 * @see WP_Dependencies::print_scripts()
 */
function mp_print_scripts( $handles = false ) {
	do_action( 'mp_print_scripts' );
	if ( '' === $handles ) // for mp_head
		$handles = false;

	global $mp_scripts;
        if(!($mp_scripts instanceof WP_Scripts)){
		if ( !$handles )
			return array(); // No need to instantiate if nothing's there.
		else
			$mp_scripts = new WP_Scripts();
	}

	return $mp_scripts->do_items( $handles );
}

/**
 * Register new JavaScript file.
 *
 * @since r16
 * @param string $handle Script name
 * @param string $src Script url
 * @param array $deps (optional) Array of script names on which this script depends
 * @param string|bool $ver (optional) Script version (used for cache busting), set to NULL to disable
 * @param bool $in_footer (optional) Whether to enqueue the script before </head> or before </body>
 * @return null
 */
function mp_register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
	global $mp_scripts;
	if(!($mp_scripts instanceof WP_Scripts))
		$mp_scripts = new WP_Scripts();

	$mp_scripts->add( $handle, $src, $deps, $ver );
	if ( $in_footer )
		$mp_scripts->add_data( $handle, 'group', 1 );
}

/**
 * Localizes a script.
 *
 * Localizes only if script has already been added.
 *
 * @since r16
 * @see WP_Scripts::localize()
 */
function mp_localize_script( $handle, $object_name, $l10n ) {
	global $mp_scripts;
	if ( !is_a($mp_scripts, 'WP_Scripts') )
		return false;

	return $mp_scripts->localize( $handle, $object_name, $l10n );
}

/**
 * Remove a registered script.
 *
 * @since r16
 * @see WP_Scripts::remove() For parameter information.
 */
function mp_deregister_script( $handle ) {
	global $mp_scripts;
	if ( !is_a($mp_scripts, 'WP_Scripts') )
		$mp_scripts = new WP_Scripts();

	$mp_scripts->remove( $handle );
}

/**
 * Enqueues script.
 *
 * Registers the script if src provided (does NOT overwrite) and enqueues.
 *
 * @since r16
 * @see mp_register_script() For parameter information.
 */
function mp_enqueue_script( $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {
	global $mp_scripts;
	if ( !is_a($mp_scripts, 'WP_Scripts') )
		$mp_scripts = new WP_Scripts();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$mp_scripts->add( $_handle[0], $src, $deps, $ver );
		if ( $in_footer )
			$mp_scripts->add_data( $_handle[0], 'group', 1 );
	}
	$mp_scripts->enqueue( $handle );
}

/**
 * Remove an enqueued script.
 *
 * @since WP 3.1
 * @see WP_Scripts::dequeue() For parameter information.
 */
function mp_dequeue_script( $handle ) {
	global $mp_scripts;
	if ( !is_a($mp_scripts, 'WP_Scripts') )
		$mp_scripts = new WP_Scripts();

	$mp_scripts->dequeue( $handle );
}

/**
 * Check whether script has been added to WordPress Scripts.
 *
 * The values for list defaults to 'queue', which is the same as enqueue for
 * scripts.
 *
 * @since WP unknown; BP unknown
 *
 * @param string $handle Handle used to add script.
 * @param string $list Optional, defaults to 'queue'. Others values are 'registered', 'queue', 'done', 'to_do'
 * @return bool
 */
function mp_script_is( $handle, $list = 'queue' ) {
	global $mp_scripts;
	if ( !is_a($mp_scripts, 'WP_Scripts') )
		$mp_scripts = new WP_Scripts();

	$query = $mp_scripts->query( $handle, $list );

	if ( is_object( $query ) )
		return true;

	return $query;
}

/**
 * BackPress Styles enqueue.
 *
 * These classes were refactored from the WordPress WP_Scripts and WordPress
 * script enqueue API.
 *
 * @package BackPress
 * @since r74
 */

/**
 * BackPress Styles enqueue class.
 *
 * @package BackPress
 * @uses WP_Dependencies
 * @since r74
 */
class WP_Styles extends WP_Dependencies {
	var $base_url;
	var $content_url;
	var $default_version;
	var $text_direction = 'ltr';
	var $concat = '';
	var $concat_version = '';
	var $do_concat = false;
	var $print_html = '';
	var $default_dirs;

	function __construct() {
		do_action_ref_array( 'wp_default_styles', array(&$this) );
	}

	function do_item( $handle ) {
		if ( !parent::do_item($handle) )
			return false;

		if ( null === $this->registered[$handle]->ver )
			$ver = '';
		else
			$ver = $this->registered[$handle]->ver ? $this->registered[$handle]->ver : $this->default_version;

		if ( isset($this->args[$handle]) )
			$ver = $ver ? $ver . '&amp;' . $this->args[$handle] : $this->args[$handle];

		if ( $this->do_concat ) {
			if ( $this->in_default_dir($this->registered[$handle]->src) && !isset($this->registered[$handle]->extra['conditional']) && !isset($this->registered[$handle]->extra['alt']) ) {
				$this->concat .= "$handle,";
				$this->concat_version .= "$handle$ver";
				return true;
			}
		}

		if ( isset($this->registered[$handle]->args) )
			$media = esc_attr( $this->registered[$handle]->args );
		else
			$media = 'all';

		$href = $this->_css_href( $this->registered[$handle]->src, $ver, $handle );
		$rel = isset($this->registered[$handle]->extra['alt']) && $this->registered[$handle]->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
		$title = isset($this->registered[$handle]->extra['title']) ? "title='" . esc_attr( $this->registered[$handle]->extra['title'] ) . "'" : '';

		$end_cond = $tag = '';
		if ( isset($this->registered[$handle]->extra['conditional']) && $this->registered[$handle]->extra['conditional'] ) {
			$tag .= "<!--[if {$this->registered[$handle]->extra['conditional']}]>\n";
			$end_cond = "<![endif]-->\n";
		}

		$tag .= apply_filters( 'style_loader_tag', "<link rel='$rel' id='$handle-css' $title href='$href' type='text/css' media='$media' />\n", $handle );
		if ( 'rtl' === $this->text_direction && isset($this->registered[$handle]->extra['rtl']) && $this->registered[$handle]->extra['rtl'] ) {
			if ( is_bool( $this->registered[$handle]->extra['rtl'] ) ) {
				$suffix = isset( $this->registered[$handle]->extra['suffix'] ) ? $this->registered[$handle]->extra['suffix'] : '';
				$rtl_href = str_replace( "{$suffix}.css", "-rtl{$suffix}.css", $this->_css_href( $this->registered[$handle]->src , $ver, "$handle-rtl" ));
			} else {
				$rtl_href = $this->_css_href( $this->registered[$handle]->extra['rtl'], $ver, "$handle-rtl" );
			}

			$tag .= apply_filters( 'style_loader_tag', "<link rel='$rel' id='$handle-rtl-css' $title href='$rtl_href' type='text/css' media='$media' />\n", $handle );
		}

		$tag .= $end_cond;

		if ( $this->do_concat )
			$this->print_html .= $tag;
		else
			echo $tag;

		// Could do something with $this->registered[$handle]->extra here to print out extra CSS rules
//		echo "<style type='text/css'>\n";
//		echo "/* <![CDATA[ */\n";
//		echo "/* ]]> */\n";
//		echo "</style>\n";

		return true;
	}

	function all_deps( $handles, $recursion = false, $group = false ) {
		$r = parent::all_deps( $handles, $recursion );
		if ( !$recursion )
			$this->to_do = apply_filters( 'print_styles_array', $this->to_do );
		return $r;
	}

	function _css_href( $src, $ver, $handle ) {
		if ( !is_bool($src) && !preg_match('|^https?://|', $src) && ! ( $this->content_url && 0 === strpos($src, $this->content_url) ) ) {
			$src = $this->base_url . $src;
		}

		if ( !empty($ver) )
			$src = add_query_arg('ver', $ver, $src);
		$src = apply_filters( 'style_loader_src', $src, $handle );
		return esc_url( $src );
	}

	function in_default_dir($src) {
		if ( ! $this->default_dirs )
			return true;

		foreach ( (array) $this->default_dirs as $test ) {
			if ( 0 === strpos($src, $test) )
				return true;
		}
		return false;
	}

}

/**
 * BackPress styles procedural API.
 *
 * @package BackPress
 * @since r79
 */

/**
 * Display styles that are in the queue or part of $handles.
 *
 * @since r79
 * @uses do_action() Calls 'mp_print_styles' hook.
 * @global object $mp_styles The WP_Styles object for printing styles.
 *
 * @param array|bool $handles Styles to be printed. An empty array prints the queue,
 *  an array with one string prints that style, and an array of strings prints those styles.
 * @return bool True on success, false on failure.
 */
function mp_print_styles( $handles = false ) {
	do_action( 'mp_print_styles' );
	if ( '' === $handles ) // for mp_head
		$handles = false;
	global $mp_styles;
	if ( !($mp_styles instanceof WP_Styles) ) {
		if ( !$handles )
			return array(); // No need to instantiate if nothing's there.
		else
			$mp_styles = new WP_Styles();
	}
	return $mp_styles->do_items( $handles );
}

/**
 * Register CSS style file.
 *
 * @since r79
 * @see WP_Styles::add() For additional information.
 * @global object $mp_styles The WP_Styles object for printing styles.
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @param string $handle Name of the stylesheet.
 * @param string|bool $src Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
 * @param array $deps Array of handles of any stylesheet that this stylesheet depends on.
 *  (Stylesheets that must be loaded before this stylesheet.) Pass an empty array if there are no dependencies.
 * @param string|bool $ver String specifying the stylesheet version number. Set to NULL to disable.
 *  Used to ensure that the correct version is sent to the client regardless of caching.
 * @param string $media The media for which this stylesheet has been defined.
 */
function mp_register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
	global $mp_styles;
	if(!($mp_styles instanceof WP_Styles))
		$mp_styles = new WP_Styles();

	$mp_styles->add( $handle, $src, $deps, $ver, $media );
}

/**
 * Remove a registered CSS file.
 *
 * @since r79
 * @see WP_Styles::remove() For additional information.
 * @global object $mp_styles The WP_Styles object for printing styles.
 *
 * @param string $handle Name of the stylesheet.
 */
function mp_deregister_style( $handle ) {
	global $mp_styles;
	if ( !is_a($mp_styles, 'WP_Styles') )
		$mp_styles = new WP_Styles();

	$mp_styles->remove( $handle );
}

/**
 * Enqueue a CSS style file.
 *
 * Registers the style if src provided (does NOT overwrite) and enqueues.
 *
 * @since r79
 * @see WP_Styles::add(), WP_Styles::enqueue()
 * @global object $mp_styles The WP_Styles object for printing styles.
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @param string $handle Name of the stylesheet.
 * @param string|bool $src Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
 * @param array $deps Array of handles (names) of any stylesheet that this stylesheet depends on.
 *  (Stylesheets that must be loaded before this stylesheet.) Pass an empty array if there are no dependencies.
 * @param string|bool $ver String specifying the stylesheet version number, if it has one. This parameter
 *  is used to ensure that the correct version is sent to the client regardless of caching, and so should be included
 *  if a version number is available and makes sense for the stylesheet.
 * @param string $media The media for which this stylesheet has been defined.
 */
function mp_enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {
	global $mp_styles;
	if ( !is_a($mp_styles, 'WP_Styles') )
		$mp_styles = new WP_Styles();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$mp_styles->add( $_handle[0], $src, $deps, $ver, $media );
	}
	$mp_styles->enqueue( $handle );
}

/**
 * Remove an enqueued style.
 *
 * @since WP 3.1
 * @see WP_Styles::dequeue() For parameter information.
 */
function mp_dequeue_style( $handle ) {
	global $mp_styles;
	if ( !is_a($mp_styles, 'WP_Styles') )
		$mp_styles = new WP_Styles();

	$mp_styles->dequeue( $handle );
}

/**
 * Check whether style has been added to WordPress Styles.
 *
 * The values for list defaults to 'queue', which is the same as mp_enqueue_style().
 *
 * @since WP unknown; BP unknown
 * @global object $mp_styles The WP_Styles object for printing styles.
 *
 * @param string $handle Name of the stylesheet.
 * @param string $list Values are 'registered', 'done', 'queue' and 'to_do'.
 * @return bool True on success, false on failure.
 */
function mp_style_is( $handle, $list = 'queue' ) {
	global $mp_styles;
	if ( !is_a($mp_styles, 'WP_Styles') )
		$mp_styles = new WP_Styles();

	$query = $mp_styles->query( $handle, $list );

	if ( is_object( $query ) )
		return true;

	return $query;
}

/* MONGOPRESS SPECIFIC FUNCTIONS */
function mp_enqueue_script_admin($handle, $src, $dep = false, $version = false){
    global $mp_scripts_admin;
    if($handle){
        //echo 'root = '.$_SERVER['DOCUMENT_ROOT'].' and $src = '.$src.'<br />';
        if(@file_exists($_SERVER['DOCUMENT_ROOT'].$src)) {
            //echo 'time to register script';
            mp_register_script($handle, $src, $dep, $version);
            $mp_scripts_admin[]=$handle;
        }
    }
    //print_r($mp_scripts_admin);
}

function mp_enqueue_script_theme($handle, $src, $dep = false, $version = false){
    if($handle){
        if(@file_exists($_SERVER['DOCUMENT_ROOT'].$src)) {
            global $mp_scripts_theme;
            mp_register_script($handle, $src, $dep, $version);
            $mp_scripts_theme[]=$handle;
        }
    }
}

function mp_enqueue_style_admin($handle, $src, $dep = false, $version = false){
    if($handle){
        if(@file_exists($_SERVER['DOCUMENT_ROOT'].$src)) {
            global $mp_styles_admin;
            mp_register_style($handle, $src, $dep, $version);
            $mp_styles_admin[]=$handle;
        }
    }
}

function mp_enqueue_style_theme($handle, $src, $dep = false, $version = false){
    if($handle){
        if(@file_exists($_SERVER['DOCUMENT_ROOT'].$src)) {
            global $mp_styles_theme;
            mp_register_style($handle, $src, $dep, $version);
            $mp_styles_theme[]=$handle;
        }
    }
}