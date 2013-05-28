<?php
/* reclaim a mongopress installation.

1 -- assumptions - username / password is lost.
2 -- or security got corrupted and salt is no longer valid.

at some point we'lll have to have a better way of reclaiming stuff.
*/

die(); // UN-COMMENT THIS LINE IF YOU LOSE YOUR DETAILS


$message = '';

require_once(dirname(dirname(__FILE__)).'/mp-includes/includes.php');
require_once(dirname(dirname(__FILE__)).'/mp-includes/inc/install.inc.php');

$config = $_mp_cache['mp_options'];


if (isset($config['db_username']) && !empty($config['db_username'])) $unpw = "{$config['db_username']}:{$config['db_password']}@"; else $unpw = '';
$connect_str = "mongodb://$unpw{$config['db_server']}:{$config['db_port']}";

try {
	$m = new Mongo($connect_str);

} catch (Exception $e) {
    $progress['message'] = sprintf(__('MongoDB Connection Failed - please check your advanced settings and if MongoDB is running on %s'),$config['db_server']);
    $progress['message'] .= sprintf("\n\n" . __('Verbose Error: %s'), get_class($e) . ' '.  $e->getMessage() . __(' connection string:') . $connect_str);
    $progress['success'] = false;
	echo json_encode($progress);
	die();
}


try {
	$db = $m->selectDB($config['db_name']); // creates db if not existing
} catch (Exception $e) {
	$error = sprintf(__('Error: %s'), get_class($e) . ' '.$e->getMessage());
	$progress['message']=__('Unexpected Error: ') . $error;
	$progress['success']=false;
	echo json_encode($progress);
	die();
}



$user_col = $config['collection_prefix'] . 'user';
$users_col = $config['collection_prefix'] . 'users';
$system_col = $config['collection_prefix'] . 'system';

$users = $db->$users_col;
$user = $db->$user_col;
// get exisiting user - assume one for now.
$existing_users = $user->find();

foreach ($existing_users as $doc) {
    $username = $doc['un'];
    $id = (string)$doc['_id'];
}

$user_config = array(
    'username'		=> array('label'=>__('Username <span class="required">*</span>'),'extras'=>__('not to reset'),'default'=>$username,'type'=>'text'),
    'password'		=> array('label'=>__('Password <span class="required">*</span>'),'extras'=>__('something easy to remember'),'default'=>'','type'=>'password'),
	'_action' 		=> 'reset-password',
);

// get the mongouser...
$existing_users = $user->find()->limit(1);

foreach ($existing_users as $user_obj) {
    $username = $user_obj['un'];
    $id = (string)$user_obj['_id'];
    continue; // TODO - multiple users?
}

$existing_users = $users->find(array('uid'=>$id))->limit(1);

foreach ($existing_users as $users_obj) {
    continue; // TODO - multiple users?
}



if (isset($_POST['_action']) && $_POST['_action'] == 'reset-password') {

   	try {
		$hashed_pw = hash('sha256',$config['fixed_salt'].$_POST['password'].$user_obj['created']); // note - you change your salt - you lose access - which is why we're here.
        
		$users->update(array("uid"=>$id),array("password"=>$hashed_pw,"uid"=>$id ));
        
        $user->update(array("un"=>$user_obj['un']),array("un"=>$_POST['username'],'email'=>$user_obj['email'],'name'=>$user_obj['name'],'display_name'=>$user_obj['display_name'],'created'=>$user_obj['created'], 'updated'=>time() ));


        mongopress_simple_page(__('SUCCESS - Password Reset'),sprintf(__('You have reset the password on your MongoPress installation. %s'), $message),$_MP['HOME'].'mp-admin/');
        die();
	} catch (Exception $e) {
        mongopress_simple_page(__('FAILED - Password Reset'),sprintf(__('some error here TODO catch it. %s'), $message),$_MP['HOME'].'mp-admin/');
		die();
	}
} else {
    echo mongopress_reset_password_form($user_config,__('MongoPress Numpty Recovery '),__('( we really do have the simplest set-up imaginable - but you seem to have screwed up )'),'reset-password','mp-form');
	die();
}


mongopress_simple_page(__('THIS IS DANGEROUS! ').__('Password Reset'),sprintf(__('Reset the password on your MongoPress installation. %s'), $message),$_MP['HOME'].'mp-admin/reclaim-user.php?reset=true');










// ================ functions

function mongopress_reset_password_form($data,$title,$message='',$id='default_id',$class='mp-form'){
    $http_host = $_SERVER['HTTP_HOST'];
    $php_self = $_SERVER['PHP_SELF'];
    $query_string = $_SERVER['QUERY_STRING'];
    $php_self_array = explode('/',$php_self);
    if($query_string){
        array_pop($php_self_array);
        array_pop($php_self_array);
    }else{
        array_pop($php_self_array);
    }
    $temporary_url = 'http://'.$http_host;
    foreach($php_self_array as $key => $folder){
        $temporary_url .= $folder.'/';
    };

$form = ''; $advanced = ''; 

$vals_js = '';
$array_data = '';
$ajax_data = '';
foreach ($data as $key => $v) {
    if (is_array($v)) $v['key'] = $key;
	if ($key == '_nonce') $form .= ' TODO todo_nonce!';
	elseif ($key == '_action') $form .= "<input id='$key' placeholder='$key' type='hidden' name='$key' value='{$v}' />";
	elseif (is_array($v)) {
		$vals_js .= "var $key = $('input#$key').val();";
		$ajax_data .= " $key: $key ,";
        $extras = '';
        if (! isset($v['extras'])) $v['extras'] = '';
		if (isset($v['type']) && $v['type'] == 'advanced')
			$advanced .= "<label for='$key'>{$v['label']}</label><span class='input-wrapper'><input id='$key' placeholder='$key' type='text' name='$key' value='{$v['default']}' autocomplete='off' class='blanked  radius5' /></span><span class='extras'>{$v['extras']}</span>";
		elseif (is_array($v)) {
            $type = 'text';
            if (isset($v['type'])) $type = $v['type'];
			$form .= "<label for='$key'>{$v['label']}</label><span class='input-wrapper'><input id='$key' placeholder='$key' type='{$type}' name='$key' value='{$v['default']}' autocomplete='off' class='blanked radius5' /></span><span class='extras'>{$v['extras']}</span>";
        } else $form .= "<input id='$key' placeholder='$key' type='hidden' name='$key' value='$v' />";
	}
}
$ajax_data = substr($ajax_data,0,-1);

$error['PASSWORD REQUIRED'] = __('PASSWORD REQUIRED');

$content = "<h4>$title</h4><p>$message</p><form id='$id' class='$class' action='{$_SERVER['PHP_SELF']}' method='POST'>" . $form;

$content .= '<p class="divider"><input type="submit" class="button-action submit" id="submit" value="'.__('RESET PASSWORD').'" style="float:right; margin-bottom:35px;" /></p>';
$content .= '</form>';


Global $_MP;
$css = mongopress_pretty_style(false);

$html =<<<EOH
<!doctype html>
<html>
    <head>
		$css
        <title>$title</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
    </head>
    <body>
        <div id="mp-content">$content</div>
    </body>
</html>
EOH;

print $html;
}

?>
