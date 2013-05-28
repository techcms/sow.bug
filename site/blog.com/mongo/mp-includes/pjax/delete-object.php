<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* COLLECT VARS */
$mongo_id = sanitize_text_field($_POST['mongo_id']);
$nonce = sanitize_text_field($_POST['nonce']);

mp_json_nonce_check($nonce,'objects-form');

// MONGO
$mp = mongopress_load_mp();

// DELETE OBJECT
$mp->remove(false,$mongo_id);
$progress['message'] = __('Object Deleted');
$progress['success'] = true;

// RETURN RESULTS
mp_json_send($progress);