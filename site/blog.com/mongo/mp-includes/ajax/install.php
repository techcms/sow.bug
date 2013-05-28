<?php

/* INCLUDE REQUIRED FILES */

$do_not_run = true;
$_debug = false;
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');
require_once($_MP['INC'].'/install.inc.php');

function mongopress_salt_generation($length=25){
    $string = "";
    $possible = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for($i=0;$i < $length;$i++) {
    $char = $possible[mt_rand(0, strlen($possible)-1)];
        $string .= $char;
    }
    return $string;
}

/* GET VARS */
$config = array();

// All the vars that appear in the config files must appear in this list.
$valid_vars = array('nonce','site_name','site_description','user_name','password','mongodb_server','mongodb_port','mongodb_user','mongodb_name','mongodb_password','email','collection_prefix','mongodb_port','mp_debug','objs_pp','mongodb_replicas','public_ttl','private_ttl','site_salt','cookie_salt','query_perma','search_perma','mongopress_theme','skip_ht','display_name','admin_slug','media_slug','cookie_ttl','base_url_dir');

$defaults = array(
	'mongodb_server' => 'localhost',
	'mongodb_name' => 'mongopress',
	'mongodb_username' => '',
	'mongodb_password' => '',
	'mongodb_replicas' => 'false',
	'collection_prefix' => '',
    'base_url_dir' => substr($_MP['HOME'],1,-1),
	'site_name' => __('MongoPress'),
	'site_description' => __('The High-Performance, PHP MongoDB CMS'),
	'mp_debug'=>'false',
	'skip_ht'=>'false',
	'objs_pp'=>'25',
    'cookie_ttl'=>'session',
	'mongopress_theme'=>'default',
	'query_perma'=>'',
	'search_perma'=>'',
	'admin_slug'=>'admin',
	'media_slug'=>'media',
);

// next loop puts valid vars in global scope and sanitizes them.
foreach ($valid_vars as $var) {
	if (isset($_POST[$var])) $config[$var] = sanitize_text_field($_POST[$var]);
	elseif (isset($defaults[$var])) $config[$var] = $defaults[$var];
	else $config[$var] = 'false';
}

// First verify mongodb connection passed from ajax.

if (isset($config['mongodb_user']) && !empty($config['mongodb_user'])) $unpw = "{$config['mongodb_user']}:{$config['mongodb_password']}@"; else $unpw = '';
$connect_str = "mongodb://$unpw{$config['mongodb_server']}:{$config['mongodb_port']}"; 
	// here we can do multiple connections! mongodb://[username:password@]host1[:port1][,host2[:port2:],...]/db

try {
	$m = new Mongo($connect_str);

} catch (Exception $e) {
    $progress['message'] = sprintf(__('MongoDB Connection Failed - please check your advanced settings and if MongoDB is running on %s'),$config['mongodb_server']);
    $progress['message'] .= sprintf("\n\n" . __('Verbose Error: %s'), get_class($e) . ' '.  $e->getMessage() . __(' connection string:') . $connect_str);
    $progress['success'] = false;
	echo json_encode($progress);
	die();
}

// Now we need to create mp_db and the mp_username - mp_password combo

try {
	$db_list = $m->listDBs();
} catch (Exception $e) {
	$error = sprintf(__('Error: %s'), get_class($e) . ' '.$e->getMessage());
	$progress['message']=__('Unexpected Error: ') . $error;
	$progress['success']=false;
	echo json_encode($progress);
	die();
} 

$mp_db_created = false;
if (isset($db_list['ok']) && $db_list['ok'] == 0) {
    $progress['message'] =__('MongoDB Error: ') . __('Please check your MongoDB username and password');
    $progress['message'] .= "\n\n". __('Raw Error: ') . $db_list['errmsg'] .' '. $db_list['assertion'];
    $progress['message'] .= "\n\n". __('Connect String: ') . $connect_str;
	$progress['success']=false;
	echo json_encode($progress);
	die();
}

$dbs = $db_list['databases'];
foreach ($dbs as $db) {
	if ($db['name'] == $config['mongodb_name']) {
		// we have it. // what happens if a numpty sets to admin? -- another example of a backend reserved word.
		$mp_db_created = true;
	}
}

if (! is_writable($_MP['SETTINGS'])) {
	$progress['message'] = sprintf(__('Settings directory is not writeable: %s'),$_MP['SETTINGS']);
    if (utils_get_os() == 'linux') $progress['message'] .= "\n\n" . 'chmod 777 '.$_MP['SETTINGS'] . "\n" . 'chmod 777 -R '.$_MP['CACHE'] ;
    $progress['success'] = false;
	echo json_encode($progress);
	die();
}


try {
	$db = $m->selectDB($config['mongodb_name']); // creates db if not existing
} catch (Exception $e) {
	$error = sprintf(__('Error: %s'), get_class($e) . ' '.$e->getMessage());
	$progress['message']=__('Unexpected Error: ') . $error;
	$progress['success']=false;
	echo json_encode($progress);
	die();
}

$user_col = $config['collection_prefix'] . 'user';
$users_col = $config['collection_prefix'] . 'users';
$system_col = $config['collection_prefix'] . 'system';

try {
	$user = $db->$user_col;

	if ($user->find()->count() > 0) {
		// throw new installException('User already installed','Skip to next section');
		$first_time_user=false;
	} else {
		$first_time_user=true;
		$timestamp = time(); // used for random_salt later
		
		// create user.
		$key = array("un"=>$config['user_name']);
        $data = array("un"=>$config['user_name'],
					"email"=>$config['email'],
					"name"=>$config['display_name'],
					"created"=>$timestamp,
					"updated"=>$timestamp
				);
		// Broke on todays upgrade of mongoDB - 20110828 - suspect find and modify no longer upserts!
		$results = $db->command( array(
			'findAndModify' => $user_col,
			'query' => $key,
			'update' => $data,
			'new' => true,
			'upsert' => true,
			'fields' => array( '_id' => 1 )
		) );
        // TODO - validate the results here if OK.... blah blah.
		$user_id = (string)$results['value']['_id'];
	}
} catch (Exception $e) {
	$error = sprintf(__('Error: %s'), get_class($e) . ' '.$e->getMessage());
	$progress['message']=__('Unexpected Error: ') . $error;
	$progress['success']=false;
	echo json_encode($progress);
	die();
}


if ($first_time_user) {

	// We only want to do this if we haven't got config already
	$config['site_salt'] = mongopress_salt_generation();
	$config['cookie_salt'] = mongopress_salt_generation();
	$config['public_ttl'] = 64200 + rand(0,12200); // around 1 day
	$config['private_ttl'] = 2400 + rand(0,1200); // around 1 hour


	try {
	
		// THE MEAT OF THE SCRIPT
		install_create('config.php','mp-settings',$config);
		install_create('security.php','mp-settings',$config);

	} catch (installException $e) {
   		$error = sprintf(__('Error: %s'),$e->getMessage());
	    $e->jsonMessage(); // json message!
		die();
	} catch (Exception $e) {
		$error = sprintf(__('Error: %s'), get_class($e) . ' '.$e->getMessage());
		$progress['message']=__('Unexpected Error: ') . $error;
		$progress['success']=false;
		echo json_encode($progress);
		die();
	}

    try {
	    $system = $db->$system_col;

		$timestamp = time(); // used for random_salt later
		
        $data = mongopress_get_versions();
		$results = $db->command( array(
			'findAndModify' => $system_col,
			'update' => $data,
			'new' => true,
			'upsert' => true,
			'fields' => array( '_id' => 1 )
		) );
    } catch (Exception $e) {
    	$error = sprintf(__('Error: %s'), get_class($e) . ' '.$e->getMessage());
    	$progress['message']=__('Unexpected Error: ') . $error;
    	$progress['success']=false;
    	echo json_encode($progress);
    	die();
    }
    
	
	try {

		$users = $db->$users_col;
		$hashed_pw = hash('sha256',$config['site_salt'].$config['password'].$timestamp); // note - you change your salt - you lose access.
		$users->insert(array("uid"=>$user_id,"password"=>$hashed_pw));

	} catch (Exception $e) {
		$error = sprintf(__('Error: %s'), get_class($e) . ' '.$e->getMessage());
		$progress['message']=__('Unexpected Error: ') . $error;
		$progress['success']=false;
		echo json_encode($progress);
		die();
	}


} else {
	// we created a new config - so warn the user that their installation is screwed (for now)
	// TODO - how to refresh an installation?

		$progress['message']=__('Error: ') . __('User already exists - but config destroyed - please use mongodb to drop users and user (in order to keep content) or drop your '.$config['mongodb_name'].' database');
			// TODO refresh rather than kill user?
		$progress['success']=false;
		echo json_encode($progress);
		die();

}

$progress['message']=__('Successfully Added Config File');
$progress['success']=true;
$progress['temp_url']= 'http://'.$_SERVER['HTTP_HOST'].$_MP['HOME'].'mp-admin/'; // redirect? not working
echo json_encode($progress);

// now touch the flag file.
$flag_file = $_MP['CACHE'].'/flags/installed.flag';
@touch($flag_file);

// From this point on - we need to always use nonces
/* example

if(0 && !mp_verify_nonce($nonce,'create-config')){ // this is not going to work if we don't have a db... the problem with the whole installation process.
    $progress['success'] = false;
    $progress['message'] = __('Unidentified Object in The Imperial Vortex!!!');
    echo json_encode($progress);
    return false;
}

*/

function arrayed($these_objs){
       if(is_object($these_objs)){
            $objects = array();
            foreach($these_objs as $this_obj) {
                $this_object = array();
                foreach($this_obj as $key => $value){
                    $this_object[$key] = $value;
                } $objects[] = $this_object;
            }
            if(is_array($objects)){
                if(!empty($objects)){
                    return $objects;
                }
            }
        }
}
