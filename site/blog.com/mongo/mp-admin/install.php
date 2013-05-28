<?php
// consider security here - make sure we don't run twice!
// - TODO - upgrades
// - TODO - recover numpty crashed installations

if (isset($_GET['action'])) $action = $_GET['action']; else $action = false;

$GLOBALS['do_not_run'] = true; // don't connect to mongo, don't do stuff - just check things
$message = '';

require_once(dirname(dirname(__FILE__)).'/mp-includes/includes.php');
require_once(dirname(dirname(__FILE__)).'/mp-includes/inc/install.inc.php');

// TODO - Here run dependency checks on Drivers, PHP, etc - if not met - display a message and die.

$passed_check = install_check_versions_passed(true);
$details = '';
    
if ($passed_check !== true) {
    $versions = mongopress_get_versions();
    foreach ($versions['current'] as $key=>$val) {
        $details .= "\n" . sprintf(__("%s : Current: %s Required: %s"),$versions['labels'][$key], $val, $versions['mp_mins'][$key]);
    }
    mongopress_simple_page(__('Install Error'), sprintf(__('MongoPress requirements not met: %s'),$passed_check ."<pre>$details</pre>"));
    die();
}

$server = install_get_server();
if ($server == 'nginx') {
    $message = '<br><br>' . __('You are running on Nginx');
    $message .= __(' There are additional manual steps you need to make.');
    $message .= __(' You need to add the following lines to your nginx config.');
    
    $settings = file_get_contents($GLOBALS['_MP']['INCLUDES'] . '/install/nginx');
    
    $message .= "<pre>$settings</pre>";

    $message .= __('You need to restart your web server.');
}

$user_config = array(
	'site_name' 	=> array('label'=>__('Site Name <span class="required">*</span>'),'extras'=>'','default'=>'MongoPress'),
	'mongodb_name'	=> array(
							'label' => __('MongoDB Name <span class="required">*</span>'),
							'extras' => '',
							'placeholder'=>'mongopress',
							'default'=>'mongopress',
						),
	'email'			=> array('label'=>__('Your Email'),'extras'=>'','default'=>'','type'=>'email'),
	'mongodb_server'=> array(
							'label'=>__('MongoDB Host'),
							'extras'=>__('ip address or dns name'),
							'default'=>'localhost',
							'type'=>'advanced'
						),
	'mongodb_port'=> array('label'=>__('MongoDB Port'),'extras'=>'','default'=>'27017','type'=>'advanced'),
	'mongodb_user'=> array('label'=>__('MongoDB User'),'extras'=>__('database username'),'default'=>'','type'=>'advanced'),
	'mongodb_password'=> array('label'=>__('MongoDB Password'),'extras'=>__('database password'),'default'=>'','type'=>'advanced'),
	'collection_prefix'=> array('label'=>__('MongoDB Collection Prefix'),'extras'=>__('collection prefix eg. mp_'),'default'=>'','type'=>'advanced'),
	'display_name'		=> array('label'=>__('Display Name'),'default'=>'MongoPress User'),
	'user_name'		=> array('label'=>__('User Name <span class="required">*</span>'),'extras'=>'','default'=>''),
	'password'		=> array('label'=>__('Password <span class="required">*</span>'),'extras'=>'','default'=>'','type'=>'password'),
	'_action' 		=> 'create_config',
);


if (! isset($_POST['create_config']) || !is_array($_POST['create_config'])) $_POST['create_config'] = array();


// each overidden by what ever is set... if we have all the data then we can create the configs.
$data = array_merge($user_config,$_POST['create_config']); // pulls back in

if (! (install_check('config.php','mp-settings') && install_check('security.php','mp-settings'))) {
		echo mongopress_simple_form($data,__('MongoPress Installation Step 1 of 1: Create Config '),__('( we really do have the simplest set-up imaginable - fill this one form out and you will be good to go )'),'create-config','mp-form');
		die();
}

// HERE WE CAN DO ADDITIONAL CHECKS.

// CREATE USER AND PASS

try {

    $mp = mongopress_load_mp();
	$options = $mp->options();

} catch (MongoConnectionException $e) {
    $error = __('Error connecting to MongoDB Server');
    mongopress_pretty_page($error,__('MongoDB Error'),true);
} catch (MongoException $e) {
    $error = sprintf(__('Error: %s'),$e->getMessage());
    mongopress_pretty_page($error,__('MongoDB Error'),true);
}

$mp_settings_dir = dirname(dirname(__FILE__)).'/mp-settings';
if (utils_get_os() == 'linux' && is_writable($mp_settings_dir)) {
    $message = '<br><br>' . __('Your installation is successful');
    $message .= __(' However your settings directory is not secure - we suggest');
    $message .= '<pre>chmod 755 '.dirname(dirname(__FILE__)).'/mp-settings </pre>';
};

mongopress_simple_page(__('Successfully Installed'),sprintf(__('This installation of MongoPress is successfully installed %s'), $message),$_MP['HOME']);