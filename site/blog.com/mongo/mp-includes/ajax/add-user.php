<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* COLLECT VARS */
if(isset($_POST['username'])){ $username = sanitize_text_field($_POST['username']); }else{ $username = false; }
if(isset($_POST['email'])){ $email = sanitize_text_field($_POST['email']); }else{ $email = false; }
if(isset($_POST['password'])){ $password = sanitize_text_field($_POST['password']); }else{ $password = false; }
if(isset($_POST['name'])){ $name = sanitize_text_field($_POST['name']); }else{ $name = false; }
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; }

mp_json_nonce_check($nonce,'add-user');

/* BUILD ARRAY */
$this_user = array(
    "type"      => 'user',
    "username"  => $username,
    "email"     => $email,
    "password"  => $password,
    "name"      => $name
);

/* CONNECT TO MONGO */
$mp = mongopress_load_mp();
$mp_options = $mp->options();

/* RUN FIRST ROUND OF CHECKS */
$errors = false; $id = false;
if(empty($name)){ $errors['message'] = __('Need Name to Add User'); }
if(empty($email)){ $errors['message'] = __('Need Email to Add User'); }
if(empty($username)){ $errors['message'] = __('Need Username to Add User'); }
if(empty($password)){ $errors['message'] = __('Need Password to Add User'); }

/* PUSH OBJECT */
if($errors){ $progress = $errors; $progress['success'] = false; }else{
$progress = $mp->push($mp_options,$this_user,$id);
if(!is_array($progress)){ $progress['success']=true; }
} mp_json_send($progress);