<?php
//http://pear.php.net/manual/en/standards.including.php
global_include(dirname(__FILE__).'/env.php');

if (file_exists(dirname(dirname(__FILE__)).'/mp-settings/config.php')) {
global_include(dirname(dirname(__FILE__)).'/mp-settings/config.php');
}

if (file_exists(dirname(dirname(__FILE__)).'/mp-settings/security.php')) {
global_include(dirname(dirname(__FILE__)).'/mp-settings/security.php');
}
// above is not good - we should actually - test for existence and redirect to the install

global_include(dirname(__FILE__).'/cache.php');
global_include(dirname(__FILE__).'/inc/utils.inc.php');
global_include(dirname(__FILE__).'/inc/mongopress.inc.php');
global_include(dirname(__FILE__).'/inc/mongopress.class.php');
global_include(dirname(__FILE__).'/inc/installException.class.php');
global_include(dirname(__FILE__).'/inc/media.inc.php');
global_include(dirname(__FILE__).'/plugins.php');
global_include(dirname(__FILE__).'/languages.php');
global_include(dirname(__FILE__).'/inc/shortcodes.inc.php');
global_include(dirname(__FILE__).'/formatting.php');
global_include(dirname(__FILE__).'/enques.php');
global_include(dirname(__FILE__).'/feeds.php');
// TODO - include only if in private section
global_include(dirname(dirname(__FILE__)).'/mp-admin/admin.php');

global_include(dirname(__FILE__).'/rewrites.php');
global_include(dirname(__FILE__).'/inc/themes.inc.php');
global_include(dirname(__FILE__).'/inc/nonce.inc.php');

if (isset($do_not_run) && $do_not_run == true) {
    // do nothing!
} else {
	global_include(dirname(__FILE__).'/init.php');
	global_include(dirname(__FILE__).'/custom.php');
}

function global_include($script_path) {
    if (isset($script_path) && is_file($script_path)) {
        extract($GLOBALS, EXTR_REFS);
        ob_start();
        require_once $script_path;
        return ob_get_clean();
    } else {
        trigger_error('The script to parse in the global scope was not found:' . $script_path); 
    }
}
