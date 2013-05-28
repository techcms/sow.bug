<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* CONNECT TO MONGO */
$mp = mongopress_load_mp();

/* COLLECT VARS */
if(isset($_POST['plugin'])){ $plugin = sanitize_text_field($_POST['plugin']); }else{ $plugin = false; }
if(isset($_POST['key'])){ $key = sanitize_text_field($_POST['key']); }else{ $key = false; }
if(isset($_POST['value'])){ $values = $mp->mp_sensible_formatting_filter($_POST['value']); }else{ $values = false; }
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; }
$plugin_settings = false;

mp_json_nonce_check($nonce,'plugin-options');

$options = array();
$value_array = explode('-_-',$values);
foreach($value_array as $values){
    $key_and_values = explode('__',$values);
    $options[$key_and_values[0]]=$key_and_values[1];
}

/* SET-UP OPTIONS TO SEND */
$these_options = array(
    'action'    => 'insert',
    'key'       => $key,
    'value'     => $options
);

/* RUN FIRST ROUND OF CHECKS */
$progress = $mp->plugin_options($these_options,$plugin_settings);
if(!is_array($progress)){
    $progress['success']=false;
    $progress['message']=__('Unknown Error Updating Option');
}
mp_json_send($progress);