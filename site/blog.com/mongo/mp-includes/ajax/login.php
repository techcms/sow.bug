<?php
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

$mp = mongopress_load_mp();
$default_options = $mp->options();

/* debug code */
if (isset($_GET['user_name']) && !isset($_POST['user_name'])) $_POST['user_name'] = $_GET['user_name'];
if (isset($_GET['password']) && !isset($_POST['password'])) $_POST['password'] = $_GET['password'];
if (isset($_GET['nonce']) && !isset($_POST['nonce'])) $_POST['nonce'] = $_GET['nonce'];
if (isset($_GET['referrer']) && !isset($_POST['referrer'])) $_POST['referrer'] = $_GET['referrer'];

$domain_arr = explode(':',$_SERVER['HTTP_HOST']); // gets rid of ports! important in dev situations.
$cookie_domain = $domain_arr[0];
if(isset($_POST['user_name'])){ $username = (string)sanitize_text_field($_POST['user_name']); }else{ $username = false; }
if(isset($_POST['password'])){ $password = (string)sanitize_text_field($_POST['password']); }else{ $password = false; }
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; }
if(isset($_POST['referrer'])){ $referrer = sanitize_text_field($_POST['referrer']); }else{ $referrer = $default_options['admin_url']; }
$referrer = apply_filters('mp_login_referrer',$referrer);

if (empty($username) || empty($password)) {
	$progress['success'] = false;
	$progress['message'] = __('Unidentified Object in The Imperial Vortex!!! - empty data');
	echo json_encode($progress);
	return false;
}

/* NOW CHECK FOR NONCE -- for debug - ! isset($_GET['password']) &&  */
mp_json_nonce_check($nonce,'mp-login-form');

/* GET BASICS */
$mp_db = array();
$m = mongopress_load_m();
$db = $m->$default_options['db_name'];
$users = $db->$default_options['user_names'];
$user = $db->$default_options['user_col'];

// Broke on todays upgrade of mongoDB - 20110828
$user_obj = $mp->arrayed($user->find(array("un"=>$username)));

if (!isset($user_obj[0])) {
	$progress['success'] = false;
	$progress['message'] = __('Unidentified Object in The Imperial Vortex!!!') . __(' No Such User');
	echo json_encode($progress);
	die();
}

$user_mongo_id = $mp->get_mongoid_as_string($user_obj[0]["_id"]);
$users_obj = $mp->arrayed($users->find(array("uid"=>$user_mongo_id)));

$user_name = $user_obj[0]['name'];

/* USE THINGS */ 
$created = $user_obj[0]['created'];
$random_salt = $created;
$hashed_pw = hash('sha256',$default_options['fixed_salt'].$password.$random_salt);
/* for debuggin on 1.8.2 
$mp->dump($user_obj,false);
$mp->dump($users_obj,false);
print "\n\n Fixed Salt: " .  $default_options['fixed_salt'];
print "\n\n Random Salt: ". $random_salt;
print "\n\n Hashed PW: ". $hashed_pw;
print "\n\n Stored PW: ". $users_obj[0]['password'] . "\n\n";
*/
if ($hashed_pw == $users_obj[0]['password']) {
	$mp_db[0]['passed']=true;
	$mp_db[0]['referrer']=$referrer;
} else {
	$mp_db[0]['passed']=false; 
}

if(is_array($mp_db)){
	
	$cookie_ttl = $default_options['cookie_ttl'];
	if ($cookie_ttl == 'session') $ttl = 0;
	elseif ($cookie_ttl == 'never') $ttl = time() + 3600*24*365*10;// 10 years! not never but sheesh.
	else $ttl = time() + intval($cookie_ttl); // 3600 = 1 hour.

    if($mp_db[0]['passed']==true){

		$data['mp_user_id'] = $user_mongo_id;
		$data['mp_display_name'] = $user_name;
		$data['mp_username'] = 'deprecated'; // flag stuff for now
		$data['mp_logged_in'] = true;
		$data['mp_ttl'] = $ttl;
		$mp_db[0]['success']=true;
		$mp_db[0]['referrer']=$referrer;

		$mongo_key = $mp->set_cookies($data);
		if (headers_sent()) { print ' HEADERS SENT';}
		setcookie('mp_'.$default_options['cookie_salt'],$mongo_key,$ttl,'/');
		setcookie('mp_'.$default_options['cookie_salt'],$mongo_key,$ttl,'/',$_SERVER['HTTP_HOST']);

		//print("setcookie('mp_'.{$default_options['cookie_salt']},$mongo_key,$ttl,'/');");

        // Note to Affandy - if changing cookie settings
        // Please double-check IE and Chrome on http://localhost/
        // These browsers cannot handle cookies on localhost
        /* THIS NEEDS TO BE RE-EVALUATED FOR 0.2
        setcookie('mp_logged_in_'.$default_options['cookie_salt'],true,$ttl,'/',$cookie_domain,false,1);
        setcookie('mp_logged_in_user_id_'.$default_options['cookie_salt'],$user_mongo_id,$ttl,'/',$cookie_domain,false,1);
        setcookie('mp_username',$user_name,$ttl,'/',$cookie_domain,false,1);
        */
		

	/*
        setcookie('mp_logged_in_'.$default_options['cookie_salt'],true,$ttl,'/');
        setcookie('mp_logged_in_user_id_'.$default_options['cookie_salt'],$user_mongo_id,$ttl,'/');
        setcookie('mp_username',$user_name,$ttl,'/');
*/
    }else{
        setcookie('mp_'.$default_options['cookie_salt'],false,-1,'/');
		// drop when finished below
        setcookie('mp_logged_in_'.$default_options['cookie_salt'],false,-1,'/');
        setcookie('mp_logged_in_user_id_'.$default_options['cookie_salt'],false,-1,'/');
        setcookie('mp_username_'.$default_options['cookie_salt'],false,-1,'/');
    }
}

mp_json_send($mp_db[0]);