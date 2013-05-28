<?php


function mp_salt($scheme = 'auth') {
    if(defined('SITE_SALT')&&(''!=SITE_SALT)){
        $salt = SITE_SALT;
    }else{
        /* NEED TO GENERATE SALTS */
        $salt = 'put-your-unique-salt-here';
    } return apply_filters('salt', $salt, $scheme);
}

function mp_hash($data, $scheme = 'auth') {
    $salt = mp_salt($scheme);
    $hash = hash_hmac('md5', $data, $salt);
	return $hash;
}




function mp_nonce_tick($nonce_life) {
    return ceil(time() / ( $nonce_life / 2 ));
}

function mp_make_nonce($data) { // data is string string
	//print $data;
	return substr(mp_hash($data, 'nonce'), -12, 10);
}

function mp_user_id($options) {
    if (isset($GLOBALS['_MP']) && isset($GLOBALS['_MP']['COOKIE'])) $cookies = $GLOBALS['_MP']['COOKIE']; 
	else $cookies = array();
    
	$uid = 'Z'; // our default public user
    if((isset($cookies['mp_logged_in']))&&(isset($cookies['mp_user_id']))){
        $uid=$cookies['mp_user_id'];
    }
    return $uid;
}


function mp_verify_nonce($nonce, $action = -1, $level='auto') {
    $mp = mongopress_load_mp(); 
	$options = $mp->options();
	$GLOBALS['_MP']['COOKIE'] = $mp->get_cookies();
    // setup
    $uid = mp_user_id($options);
    if ($level == 'auto') $level =  mp_determine_security_level();
    if ($level == 'private' && $uid == 'Z') {
		$msg = __('Ming the Merciless has stopped this transaction - invalid level on nonce create');
		if (mp_in_ajax()) {
			$a = array('success'=>false,'message'=>$msg); 
			echo json_encode($a);
			die();
		} else
			die($msg);
	}

    if ($level == 'public') {
        $uid = 'Z';
        $nonce_life = apply_filters('nonce_public_life',$options['nonce_public_ttl']);
    }
    else $nonce_life = apply_filters('nonce_private_life', $options['nonce_private_ttl']);

    $i = mp_nonce_tick($nonce_life);

    $valid = mp_make_nonce($i . $action . $uid);
    // $mp->dump("$level : $valid  ==  $nonce tick: $i action: $action user: $uid",false);
    if ( $valid == $nonce )
    return 1;

    $valid = mp_make_nonce(($i - 1) . $action . $uid);

    //$mp->dump($valid . ' == ' . $nonce,false);
    if ( $valid == $nonce )
    return 2;
    // Else Invalid nonce
    return false;
}

function mp_referer_field( $echo = true ) {
    $ref = esc_attr( $_SERVER['REQUEST_URI'] );
    $referer_field = '<input type="hidden" name="_mp_http_referer" value="'. $ref . '" />';
    if ( $echo )
            echo $referer_field;
    return $referer_field;
}

function mp_determine_security_level() {
	$mp = mongopress_load_mp(); $options = $mp->options();
	$base = $options['root_url'];
	$url = $_SERVER['REQUEST_URI'];

	$script = preg_replace("#^$base#",'',$url);

	$admin_slug = $options['admin_slug'];
	$admin_len = strlen($admin_slug);

	if (substr($script,0,$admin_len + 1) == $admin_slug . '/') $level = 'private';
	elseif (substr($script,0,9) == 'mp-admin/') $level = 'private';
	elseif (substr($script,0,17) == 'mp-includes/pjax/') $level = 'private';
	else $level = 'public';

	return $level;
}


function mp_in_ajax() {
	$mp = mongopress_load_mp(); $options = $mp->options();

	$base = $options['root_url'];
	$url = $_SERVER['REQUEST_URI'];

	$script = preg_replace("#^$base#",'',$url);

	$admin_slug = $options['admin_slug'];
	$admin_len = strlen($admin_slug);

	if (substr($script,0,16) == 'mp-includes/ajax') return true;
	elseif (substr($script,0,16) == 'mp-includes/pjax') return true;

	return false;
}


function mp_create_nonce($action = -1, $level='auto') {
    $mp = mongopress_load_mp(); $options = $mp->options();
	
	$uid = mp_user_id($options);
	if ($level == 'auto') $level =  mp_determine_security_level();
	
	if ($level == 'private' && $uid == 'Z') die('Ming the merciless has stopped this transation - invalid level on nonce create');
	
	if ($level == 'public') {
		$uid = 'Z'; // back to our public uid - as publicstuff
		$nonce_life = apply_filters('nonce_public_life',$options['nonce_public_ttl']);
	}
	else $nonce_life = apply_filters('nonce_private_life', $options['nonce_private_ttl']);

   
    $i = mp_nonce_tick($nonce_life);

    $nonce = mp_make_nonce($i . $action . $uid);
	// $mp->dump($nonce.' $i . $action . $uid = '.$i . $action . $uid,false);
	return $nonce;
}

function mp_nonce_field( $action = -1, $name = "_mpnonce", $referer = true , $echo = true, $level='auto') {
    $name = esc_attr($name);
    $nonce_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . mp_create_nonce( $action,$level ) . '" />';
    if($echo)
        echo $nonce_field;
    if($referer)
        mp_referer_field( $echo );
    return $nonce_field;
}

