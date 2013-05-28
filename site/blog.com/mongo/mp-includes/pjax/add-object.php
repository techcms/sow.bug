<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

$mp = mongopress_load_mp(); $errors = false;

if (isset($_GET['nonce']) && !isset($_POST['nonce'])) $_POST['nonce'] = $_GET['nonce'];

// SECURITY FOR COOKIES.
/*

http://dev.travelblog.org:8000/mp-includes/pjax/add-object.php?nonce=4e09c8f447 -- example!

1 - we need a cache collection on mongodb (later to be migrated to filesystem/memory for performance
2 - we can have cookies stored in the cache - we set on login - or refresh
	-- collection[cache][_cookies][mp_id] - array(
		our cookie values [mp-user] = 'blah blah'
	)
3 - if any of the values differ from the cookies - they have been tampered - destroy
4 - we can store sensitive values here
5 - on log out - delete the cache as well. (filesystem/memory needs a listener on each of the webserver pool)

New way would be 
 - check nonce - quickest sec check
    == two nonces - one is public_nonce - other private-nonce (validates user valid)
 - check cookie tampering - a little more complex
 - check system rights - for multi-user system v0.2
 - do stuff.
*/

/* COLLECT VARS */
if(isset($_POST['mongo_id'])){ $mongo_id = sanitize_text_field($_POST['mongo_id']); }else{ $mongo_id = false; }
if(isset($_POST['type'])){ $type = sanitize_text_field($_POST['type']); }else{ $type = false; }
if(isset($_POST['slug'])){ $slug = sanitize_text_field($_POST['slug']); }else{ $slug = false; }
if(isset($_POST['slug_id'])){ $slug_id = sanitize_text_field($_POST['slug_id']); }else{ $slug_id = false; }
if(isset($_POST['title'])){ $title = sanitize_text_field($_POST['title']); }else{ $title = false; }
if(isset($_POST['content'])){ $content = $mp->mp_sensible_formatting_filter($_POST['content']); }else{ $content = false; }
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; }
if(isset($_POST['custom'])){ $custom = sanitize_text_field($_POST['custom']); }else{ $custom = false; }

$custom_array = '';
$custom_keys = explode('___',$custom);
if(is_array($custom_keys)){
    $temp_count = 0;
    foreach($custom_keys as $this_key => $this_value){
        $these_keys_and_values = explode('__',$this_value);
        $keys_and_values = explode('===',$these_keys_and_values[0]);
        $this_key_array = explode('===',$these_keys_and_values[0]);
        if(count($these_keys_and_values)>1){
            $this_value_array = explode('===',$these_keys_and_values[1]);
            if((is_array($this_key_array))&&(is_array($this_value_array))){
                $custom_array[$this_key_array[1]] = $this_value_array[1];
            }
        }
        $temp_count++;
    }
}

mp_json_nonce_check($nonce,'object');

/* HARD-CODED ENSURITY THAT USER DOES NOT GET PICKED AS OBJECT TYPE */
if($type=='user'){ $type='users'; }

/* CONNECT TO MONGO */
$mp_options = $mp->options();
$mp_options['validate']=true;
$m = mongopress_load_m();
$db = $m->$mp_options['db_name'];
$objs = $db->$mp_options['obj_col'];

/* TODO: CHECK PERFORMANCE */
/* FLUSH SLUGS */
$mp->flush_slugs();

/* RUN FIRST ROUND OF CHECKS */
if(empty($title)){ $errors['message'] = __('Need Title to Add Object'); }
if(empty($type)){ $errors['message'] = __('Need Type to Add Object'); }
if(empty($slug_id)){
    if(empty($slug)){
        $slug = sanitize_title_with_dashes($title);
    }else{
        $slug = sanitize_title_with_dashes($slug);
    }
}else{
    $slug = $mp->get_slug($slug_id);
}
if(empty($slug)){ $errors['message'] = __('Need Slug to Add Object'); }

if(is_array($custom_array)){
    $this_object = array(
        "mongo_id"  => $mongo_id,
        "type"      => $type,
        "slug"      => $slug,
        "slug_id"   => $slug_id,
        "title"     => $title,
        "validate"  => true,
        "content"   => $content,
        "custom"    => $custom_array
    );
}else{
/* BUILD ARRAY */
    $this_object = array(
        "mongo_id"  => $mongo_id,
        "type"      => $type,
        "slug"      => $slug,
        "slug_id"   => $slug_id,
        "title"     => $title,
        "validate"  => true,
        "content"   => $content
    );
}


/* PUSH OBJECT */
if($errors){ $progress = $errors; $progress['success'] = false; }else{
$progress = $mp->push($mp_options,$this_object,$mongo_id);
if(!is_array($progress)){ $progress['success']=true; }
} mp_json_send($progress);
