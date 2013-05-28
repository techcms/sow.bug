<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* COLLECT VARS */
$title = sanitize_text_field($_POST['title']);
$nonce = sanitize_text_field($_POST['nonce']);

// This prevents indefined index notice, which in-turn prevents the AJAX from working if error notices are set...
// This field occasionally has nothing set - unlike those above, but does that mean we should use this method everywhere...?
if(isset($_POST['slug_id'])){ $slug_id = sanitize_text_field($_POST['slug_id']); }else{ $slug_id = false; }

mp_json_nonce_check($nonce,'object');

/* CONNECT TO MONGO */
$mp = mongopress_load_mp();
/* TODO: CHECK PERFORMANCE */
/* FLUSH SLUGS */
$mp->flush_slugs();

/* THEN RE-GROUP */
if($slug_id){
    $slug = $mp->get_slug($slug_id);
    $verified_slug = $slug;
}else{
    /* CONVERT TO SLUG */
    $slug = sanitize_title_with_dashes($title);
    $verified_slug = $mp->slugs($slug);
}

/* SEND BACK SLUG */
$progress['success'] = true;
$progress['message'] = $verified_slug;
mp_json_send($progress);