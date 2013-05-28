<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* COLLECT VARS */
$mongo_id = sanitize_text_field($_POST['mongo_id']);
$nonce = sanitize_text_field($_POST['nonce']);

mp_json_nonce_check($nonce,'objects-form');

/* MONGO */
$mp = mongopress_load_mp();

/* GET OBJECT */
$object = $mp->find_one($mongo_id);
$slug = $mp->get_slug($object['slug_id']);
$object['slug']=$slug;

/* RETURN OBJECT */
mp_json_send($object);