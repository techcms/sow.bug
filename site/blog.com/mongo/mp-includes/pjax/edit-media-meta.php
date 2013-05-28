<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* COLLECT VARS */
if(isset($_POST['id'])){ $id = sanitize_text_field($_POST['id']); }else{ $id = false; }
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; }
if(isset($_POST['filename'])){ $filename = sanitize_text_field($_POST['filename']); }else{ $filename = false; }

mp_json_nonce_check($nonce,'media_action');

// MONGO
$m = mongopress_load_m();
$mp = mongopress_load_mp();
$mp_options = $mp->options();
$db = $m->$mp_options['db_name'];
$mongo_id = new MongoID($id);
$grid = $db->getGridFS();

$files = $db->fs->files;
$updated = $files->update(array("_id" => $mongo_id), array('$set' => array("filename" => $filename)));

if($updated){
    $progress['message'] = __('Media Filename Successfully Edited');
    $progress['success'] = true;
}else{
    $progress['success'] = false;
    $progress['message'] = __('Unable to Edit Media Filename');
}

// RETURN RESULTS
mp_json_send($progress);
