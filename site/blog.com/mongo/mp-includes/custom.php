<?php
/* CHECK FOR THEME FUNCTIONS OR PLUGIN FIRST - NOT SURE */
/* TODO: ANSWER THIS QUESTION */
$mp = mongopress_load_mp();
$mp_options = $mp->options();
$mp_perma = mongopress_load_perma();
$perma = $mp_perma->current();

$admin_slug = $mp_options['admin_slug'];
$admin_len = strlen($admin_slug);

if(substr($perma,0,$admin_len) == $admin_slug || substr($perma,0,8)=='mp-admin') { // Catches all admin section
	/* ADMIN SPECIFIC FUNCTIONALITY */
}else{
	/* THEME SPECIFIC FUNCTIONALITY */
	$theme_functions = $mp_options['theme'].'/functions.php';
	if(@file_exists(dirname(dirname(__FILE__))."/mp-content/themes/$theme_functions")){
		require_once(dirname(dirname(__FILE__))."/mp-content/themes/$theme_functions");
	}
}

/* CHECK FOR MU-PLUGINS */
global $mp_plugins;
$mu_plugins_folder = dirname(dirname(__FILE__)).'/mp-content/mu-plugins/';
if($folder = opendir($mu_plugins_folder)){
    $mu_plugins = array();
    while (false !== ($mu_plugin = readdir($folder))) {
        if(strstr($mu_plugin, '.php')){
            $mp_plugins['mu'][$mu_plugin] = true;
            require_once(dirname(dirname(__FILE__)).'/mp-content/mu-plugins/'.$mu_plugin);
        }elseif(!strstr($mu_plugin, '.')){
            if(file_exists(dirname(dirname(__FILE__)).'/mp-content/mu-plugins/'.$mu_plugin.'/'.$mu_plugin.'.php')){
                $mp_plugins['mu'][$mu_plugin.'.php'] = true;
                require_once(dirname(dirname(__FILE__)).'/mp-content/mu-plugins/'.$mu_plugin.'/'.$mu_plugin.'.php');
            }
        }
    }
    closedir($folder);
    do_action('mu_plugins_init');
}