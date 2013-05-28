<?php

/* REQUIRE ESSENTIALS */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* INITIALIZE ESSENTIALS */
$mp = mongopress_load_mp();
$options = $mp->options();

/* GET POST PARAMS */
$name = mp_strip_mail_injection(sanitize_text_field($_POST['name']));
$email = mp_strip_mail_injection(sanitize_text_field($_POST['email']));
$email_to = mp_strip_mail_injection(sanitize_text_field($_POST['email_to']));
$message = mp_strip_mail_injection($mp->mp_sensible_formatting_filter($_POST['message']));
$min_length = (int)sanitize_text_field($_POST['min_length']);
$nonce = sanitize_text_field($_POST['nonce']);

/* CHECK NONCE */
/* TODO: Use mp_json_nonce_check */
if(!mp_verify_nonce($nonce,'email')){
    $progress['success'] = false;
    $progress['message'] = __('Unidentified Object in The Imperial Vortex!!!');
    mp_json_send($progress);
}else{
    if(strlen($name)<2){
        $progress['success']=false;
        $progress['message']=__('Please Enter a Real Name');
        mp_json_send($progress);
    }elseif(strlen($message)<$min_length){
        $progress['success']=false;
        $progress['message']=__('Please Enter a Real Message');
        mp_json_send($progress);
    }elseif(filter_var($email, FILTER_VALIDATE_EMAIL)){
        if(function_exists('mail')){
            $headers = "From: {$email}" . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $emailbody = __('<p>You have received a new message from the enquiries form on your website.</p>');
            $emailbody.= sprintf(__('<p><strong>Name: </strong> %s</p>'),$name);
            $emailbody.= sprintf(__('<p><strong>Email Address: </strong> %s</p>'),$email);
            $emailbody.= sprintf(__('<p><strong>Message: </strong> %s</p>'),$message);
            if(mail($email_to,__('New Enquiry'),$emailbody,$headers)){
                $progress['success']=true;
                $progress['message']=__('Email Successfully Sent');
                mp_json_send($progress);
            }else{
                $progress['success']=false;
                $progress['message']=__('Unable to Send Email');
                mp_json_send($progress);
            }
        }else{
            $progress['success']=false;
            $progress['message']=__('PHP Mail Function not Available');
            mp_json_send($progress);
        }
    }else{
        $progress['success']=false;
        $progress['message']=__('Please Enter a Valid Email Address');
        mp_json_send($progress);
    }
}