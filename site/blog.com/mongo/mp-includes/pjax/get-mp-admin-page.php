<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* COLLECT VARS */
$nonce = sanitize_text_field($_POST['nonce']);
$page = sanitize_text_field($_POST['page']);

mp_json_nonce_check($nonce,'mp_admin_pages');

ob_start();
$admin_settings = array(
	'header'	=> false,
	'page'		=> $page
);
mp_admin_page($admin_settings);
$content = ob_get_clean();
$progress['success']=true;
$progress['message']=$content;
$progress['page']=$page;

// RETURN RESULTS
mp_json_send($progress);