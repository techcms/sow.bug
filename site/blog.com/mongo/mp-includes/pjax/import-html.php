<?php

set_time_limit(0);

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; }
/* TODO; USE mp_json_nonce_check */
if((!mp_verify_nonce($nonce,'mp-install'))&&(!mp_verify_nonce($nonce,'add-user'))){
    /* BREAK NOW OR FOREVER HOLD YOUR PEACE */
    $progress['success'] = false;
    $progress['message'] = __('Unidentified Object in The Imperial Vortex!!!');
    echo json_encode($progress);
    return false;
}

/* CONNECT TO MONGO */
$mp = mongopress_load_mp();
$mp_options = $mp->options();
$progress = $mp->import_html();
if(!is_array($progress)){ $progress['success']=true; }
mp_json_send($progress);