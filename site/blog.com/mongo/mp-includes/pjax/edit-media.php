<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* COLLECT VARS */
$id = sanitize_text_field($_POST['id']);
$nonce = sanitize_text_field($_POST['nonce']);

mp_json_nonce_check($nonce,'media_action');

// MONGO
$m = mongopress_load_m();
$mp = mongopress_load_mp();
$mp_options = $mp->options();
$db = $m->$mp_options['db_name'];
$mongo_id = new MongoID($id);
$grid = $db->getGridFS();

// DELETE OBJECT
$this_obj = $mp->arrayed($grid->find(array("_id"=>$mongo_id)));
if(isset($this_obj)){
    $progress['message'] = __('Editing Object');
    $progress['success'] = true;
    $progress['results'] = $this_obj;
}else{
    $progress['success'] = false;
    $progress['message'] = __('Unable to Delete Media');
}

// RETURN RESULTS
mp_json_send($progress);
