<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');
$mp = mongopress_load_mp();

/* COLLECT VARS */
$mongo_id = sanitize_text_field($_POST['mongo_id']);
$nonce = sanitize_text_field($_POST['nonce']);
if(isset($_POST['shortcodes'])){ $apply_shortcodes = sanitize_text_field($_POST['shortcodes']); }else{ $apply_shortcodes = false; }

mp_json_nonce_check($nonce,'objects-form');

/* MONGO */
$mp = mongopress_load_mp();

/* GET OBJECT */
$object = $mp->find_one($mongo_id, false, $apply_shortcodes);
$slug = $mp->get_slug($object['slug_id']);
$object['slug']=$slug;

/* RETURN OBJECT */
mp_json_send($object);