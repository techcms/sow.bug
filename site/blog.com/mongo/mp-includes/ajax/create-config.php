<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

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
if(isset($_POST['site_name'])){ $site_name = sanitize_text_field($_POST['site_name']); }else{ $site_name = false; }
if(isset($_POST['site_name'])){ $site_description = sanitize_text_field($_POST['site_description']); }else{ $site_description = false; }
if(isset($_POST['mp_address'])){ $mp_address = sanitize_text_field($_POST['mp_address']); }else{ $mp_address = false; }
if(isset($_POST['collection_prefix'])){ $collection_prefix = sanitize_text_field($_POST['collection_prefix']); }else{ $collection_prefix = false; }
if(isset($_POST['mongodb_name'])){ $mongodb_name = sanitize_text_field($_POST['mongodb_name']); }else{ $mongodb_name = false; }
if(isset($_POST['mongodb_name'])){ $mongodb_name = sanitize_text_field($_POST['mongodb_name']); }else{ $mongodb_name = false; }
if(isset($_POST['mongodb_address'])){ $mongodb_address = sanitize_text_field($_POST['mongodb_address']); }else{ $mongodb_address = false; }
if(isset($_POST['mongodb_objs'])){ $mongodb_objs = sanitize_text_field($_POST['mongodb_objs']); }else{ $mongodb_objs = false; }
if(isset($_POST['mongodb_slugs'])){ $mongodb_slugs = sanitize_text_field($_POST['mongodb_slugs']); }else{ $mongodb_slugs = false; }
if(isset($_POST['mongodb_uns'])){ $mongodb_uns = sanitize_text_field($_POST['mongodb_uns']); }else{ $mongodb_uns = false; }
if(isset($_POST['mongodb_users'])){ $mongodb_users = sanitize_text_field($_POST['mongodb_users']); }else{ $mongodb_users = false; }
if(isset($_POST['mongodb_db_user'])){ $mongodb_db_user = sanitize_text_field($_POST['mongodb_db_user']); }else{ $mongodb_db_user = false; }
if(isset($_POST['mongodb_db_password'])){ $mongodb_db_password = sanitize_text_field($_POST['mongodb_db_password']); }else{ $mongodb_db_password = false; }
if(isset($_POST['mongodb_db_port'])){ $mongodb_db_port = sanitize_text_field($_POST['mongodb_db_port']); }else{ $mongodb_db_port = false; }
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; }
if(isset($_POST['temp_url'])){ $temp_url = sanitize_text_field($_POST['temp_url']); }else{ $temp_url = false; }
if(isset($_POST['mp_home'])){ $mp_home = sanitize_text_field($_POST['mp_home']); }else{ $mp_home = false; }

mp_json_nonce_check($nonce,'create-config');

/* RUN CHECKS */
if(empty($mongodb_address)){ $mongodb_address = 'localhost'; };
if(empty($mongodb_name)){ $mongodb_name = 'mongopress'; };
if(empty($collection_prefix)){ $collection_prefix = ''; };
if(empty($mp_home)){ $mp_home = ''; };
if(empty($mp_address)){ $mp_address = ''; };
if(empty($site_name)){ $site_name = __('MongoPress'); };
if(empty($site_description)){ $site_description = __('The High-Performance, Object-Based NoSQL CMS'); };
if(empty($mongodb_db_port)){ $mongodb_db_port = '27017'; };
if(empty($mongodb_db_user)){ $mongodb_db_user = ''; };
if(empty($mongodb_db_password)){ $mongodb_db_password = ''; };

/* MANIPULATE THINGS */
if($mp_home=='/'){
    $mp_home='';
}

/* DOUBLE-CHECK PERMISSION AGAIN */
if(is_writable(dirname(dirname(dirname(__FILE__))).'/mp-settings/')){

$config_content =<<<EOC
<?php
// THESE ARE OUR OPTIONS / CONFIGURATION SETTINGS
define('MONGODB_HOST','$mongodb_address');
define('MONGODB_NAME', '$mongodb_name' );
define('COLLECTION_PREFIX', '$collection_prefix' );
define('SITE_NAME', '$site_name' );
define('SITE_DESCRIPTION', '$site_description' );
define('BASE_URL_DIRECTORY', '$mp_home' );
define('DB_PORT', '$mongodb_db_port' );
// END OF OPTIONS

// AT PRESENT - THIS IS THE ONLY WAY TO SWITCH THEMES
define('MONGOPRESS_THEME','default');

// RESERVED NAMESPACES FOR PERMA QUERIES AND SEARCHES
define('QUERY_PERMA','mp');
define('SEARCH_PERMA','search');

// TIME AND DATE LOCAL SETTINGS
define('TIMEZONE','UTC'); // needs to be valid timezone eg. Europe/London, America/Los_Angeles, UTC

// TO DEBUG OR NOT TOP DEBUG - THAT IS THE QUESTION
define('MP_DEBUG',false);

// MISC OPTIONS THAT WILL SOON HAVE AN INTERFACE
define('SKIP_HT',false);
define('OBJS_PP',25);
define('OPTIONS_COL','options');

// THIS ALLOWS FOR MONGO REPLIOA-SETS TO BE USED
define('MONGODB_REPLICAS',false);


define('COOKIE_TTL','session'); // cookies last the life of the browser session.

// CUSTOM SLUGS
define('ADMIN_SLUG','admin');
define('MEDIA_SLUG','media'); 
EOC;

$site_salt = mongopress_salt_generation();
$cookie_salt = mongopress_salt_generation();
$public_ttl = 64200 + rand(0,12200); // around 1 day
$private_ttl = 2400 + rand(0,1200); // around 1 hour


$security_content =<<<EOS
<?php
/*
Highly sensitive security settings! - do not give these to anyone (except your system admin)
Do not paste this file in a forum, or send to emails

The security of your site depends to an extent on the secrecy of this file.
*/
define('DB_USERNAME', '$mongodb_db_user' );
define('DB_PASSWORD', '$mongodb_db_password' );

// GENERATE NEW SITE SALT - http://mongopress.com/salt-generation.php
// PICK ANY TWO RANDOMLY GENERATED VALUES OR SIMPLY ADD YOUR OWN UNIQUE SALTS
define('SITE_SALT','$site_salt');
define('COOKIE_SALT','$cookie_salt');

define('NONCE_PRIVATE_TTL','$private_ttl'); // roughly the time we allow for making actions in the admin panel - 1 hour
define('NONCE_PUBLIC_TTL','$public_ttl');  // the time we allow for public actions - like media, logins. - 1 day

EOS;

// TODO! - make security and config - in mp-settings dir as two sections.
    $config_file = fopen(dirname(dirname(dirname(__FILE__))).'/mp-settings/config.php', "w");
    $security_file = fopen(dirname(dirname(dirname(__FILE__))).'/mp-settings/security.php', "w");
    if(fwrite($config_file, $config_content) === false || fwrite($security_file, $security_content) === false) {
        $progress['message']=__('Problem trying to remotely create file');
        $progress['success']=false;
    }else{
        $progress['message']=__('Successfully Added Config File');
        $progress['success']=true;
        $progress['temp_url']=$temp_url;
    }
}else{
    $progress['message']=__('Cannot write to folder');
    $progress['success']=false;
} mp_json_send($progress);