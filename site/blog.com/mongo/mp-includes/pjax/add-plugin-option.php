<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* CONNECT TO MONGO */
$mp = mongopress_load_mp();

/* COLLECT VARS */
$plugin = sanitize_text_field($_POST['plugin']);
$key = sanitize_text_field($_POST['key']);
$value = $mp->mp_sensible_formatting_filter($_POST['value']);
$nonce = sanitize_text_field($_POST['nonce']);
$plugin_settings = false;

mp_json_nonce_check($nonce,'plugin-options');

/* SET-UP OPTIONS TO SEND */
$these_options = array(
    'action'    => 'insert',
    'plugin'    => $plugin,
    'key'       => $key,
    'value'     => $value
);

/* RUN FIRST ROUND OF CHECKS */
$progress = $mp->plugin_options($these_options,$plugin_settings);
if(!is_array($progress)){
    $progress['success']=false;
    $progress['message']=__('Unknown Error Updating Option');
}
mp_json_send($progress);