<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

$i = sanitize_text_field((int)$_POST['i']);
$nonce = sanitize_text_field($_POST['nonce']);
if(isset($_POST['key'])){ $this_key = sanitize_text_field($_POST['key']); }else{ $this_key = false; }
if(isset($_POST['value'])){ $this_value = sanitize_text_field($_POST['value']); }else{ $this_value = false; }

mp_json_nonce_check($nonce,'object');
$id = 1+$i;

ob_start();
mp_custom_field_row($id,$this_key,$this_value);
$content = ob_get_clean();

if(!empty($content)){
    $progress['content'] = $content;
} if(is_array($progress)){
    $progress['success']=true;
}else{
    $progress['success'] = false;
    $progress['message'] = __('Unknown Error Whilst Creating Custom Field');
}
mp_json_send($progress);