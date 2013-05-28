<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* CONNECT TO MONGO */
$mp = mongopress_load_mp();

/* COLLECT VARS */
if(isset($_POST['email'])){ $email = sanitize_text_field($_POST['email']); }else{ $email = false; }
if(isset($_POST['name'])){ $name = $mp->mp_sensible_formatting_filter($_POST['name']); }else{ $name = false; }
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; }
if(isset($_POST['user_id'])){ $user_id = sanitize_text_field($_POST['user_id']); }else{ $user_id = false; }

mp_json_nonce_check($nonce,'user-options');

/* SET-UP OPTIONS TO SEND */
$these_options = array(
    'email'     => $email,
    'name'		=> $name,
	'id'		=> $user_id
);

/* RUN FIRST ROUND OF CHECKS */
$progress = $mp->user_options($these_options);

if(!is_array($progress)){
    $progress['success']=false;
    $progress['message']=__('Unknown Error Updating Option');
}
mp_json_send($progress);