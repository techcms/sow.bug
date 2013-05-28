<?php
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

$mp = mongopress_load_mp();
$default_options = $mp->options();

/* debug code */
if(isset($_POST['user_id'])){ $user_id = sanitize_text_field($_POST['user_id']); }else{ $user_id = false; };
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; };

/* NOW CHECK FOR NONCE */
mp_json_nonce_check($nonce,'public-avatar');

/* GET BASICS */
$user_info = $mp->get_user_info($user_id);

$default_avatar = $default_options['root_url'].'mp-includes/images/add_image.png';
$gravatar = mp_get_avatar($user_info['email'],80,'mm','g',false,false,true,$user_id);

if((isset($user_info['email']))&&(!empty($user_info['email']))){
	if($gravatar==$default_avatar){
		$progress['success']=false;
		$progress['message']=false;
	}else{
		$progress['success']=true;
		$progress['message']=$gravatar;
	}
	mp_json_send($progress);
}else{
	if($gravatar == $default_avatar){
		$progress['success']=false;
		$progress['message']=false;
	}else{
		$progress['success']=true;
		$progress['message']=$gravatar;
	}
	mp_json_send($progress);
}