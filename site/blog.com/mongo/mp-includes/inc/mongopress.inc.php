<?php

function mongopress_pretty_style($print=true){
$string=<<<EOS
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
		body { font:13px/1.231 sans-serif; *font-size:small; } /* Hack retained to preserve specificity */
		html { font-size: 62.5%; }
		body { font-size: 14px; font-size: 1.4rem; } /* =14px */
        body {
            font-size: 10px;
            font-size:1rem;
            font-family:sans-serif;
            margin: 0;
            padding: 25px 10%;
            background: #F3F4EB;
            min-width: 428px;
            width: 80%;
            text-align: center;
        }
        .radius5 {
            border-radius: 5px;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
        }
        .button, .button-action {
            clear: none;
            font-weight: bold;
            font-size: 13px;
            font-size: 1.3rem;
            border:3px solid #BBB;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 10px;
            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            color: #999;
            background-color: #FFE;
            background-image: -moz-linear-gradient(100% 100% 90deg, #FFFFEE, #EEEEEE);
            background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#EEEEEE), to(#FFFFEE));
            background-image: -o-linear-gradient(#FFFFEE,#EEEEEE);
            box-shadow:1px 1px 5px #BBB;
            -moz-box-shadow:1px 1px 5px #BBB;
            -webkit-box-shadow:1px 1px 5px #BBB;
            font-style:normal;
            margin:10px 0;
        }
        .button:hover, .button:focus {
            color: #666;
            background-color: #EEE;
            background-image: -moz-linear-gradient(100% 100% 90deg, #EEEEEE, #FFFFEE);
            background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FFFFEE), to(#EEEEEE));
            background-image: -o-linear-gradient(#EEEEEE,#FFFFEE);
        }
        .button-action {
            color: #EEE;
            background-color: #545454;
            background-image: -moz-linear-gradient(100% 100% 90deg, #454545, #000000);
            background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#000000), to(#454545));
            background-image: -o-linear-gradient(#454545,#000000);
        }
        .button-action:hover, .button-action:focus {
            color: #FFF;
            background-color: #000;
            background-image: -moz-linear-gradient(100% 100% 90deg, #000000, #454545);
            background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#454545), to(#000000));
            background-image: -o-linear-gradient(#000000,#454545);
        }
        div#mp-content {
            background: #FFF;
            border: 1px solid #CCC;
            padding: 25px 5%;
            width: auto;
            display: block;
            border-radius: 5px;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            box-shadow:0 0 5px #CCC;
            -moz-box-shadow:0 0 5px #CCC;
            -webkit-box-shadow:0 0 5px #CCC;
        }
		form {
			font-size: 12px;
			font-size: 1.2rem;
		}
        h4 {
            font-size: 12px;
            font-size:1.2rem;
            color: #069;
            width: 100%;
            padding-bottom: 10px;
            border-bottom: 1px dotted #CCC;
        }
        form {
            display: block;
            width: 80%;
            padding: 25px 10% 45px;
        }
		p {
			clear: both;
			display: block;
			padding: 0 0 5px;
		}
		p.divider {
			padding-top: 25px;
			margin-top: 15px;
		}
		label {
            clear: both;
            display: block;
            padding: 10px 0 0;
            margin: 25px 0 10px;
            border-top: 1px dotted #CCC;
            color: #069;
        }
		form label:first-child {
			border-top: none;
			margin-top: -20px;
		}
		#mp-advanced-config {
			clear: both;
			display: block;
		}
		#mp-advanced-config label:first-child {
			margin-top: 20px;
		}
        .input-wrapper {
            clear: both;
            display: block;
            padding: 8px 2%;
            width: 96%;
            background: #EEE;
            border: 1px solid #CCC;
        }
        .input-wrapper .blanked {
            padding: 0;
            margin: 0;
            width: 100%;
            background: transparent;
            border: none;
            text-align: center;
        }
        input, textarea {
            font-style:italic;
            color:#AAA;
        }
        input:focus, textarea:focus {
            font-style:normal;
            color:#444;
        }
		form span.extras {
			font-size: 10px;
			font-size: 1rem;
			color: #999;
			font-weight: bold;
			font-style: italic;
			margin: 5px 0 -10px;
			display: block;
		}
		span.required {
			color: #990000;
			font-size: 22px;
			font-size: 2.2rem;
		}
    </style>
EOS;
if ($print) echo $string; else return $string;
}

function mongopress_pretty_page($content,$title,$wrap_in_h4=false,$code='503'){
	if ($code == '503') {
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		header('Retry-After: 7200'); // in seconds
	} elseif ($code == '404') {
		header('HTTP/1.0 404 Not Found');
		header('Status: 404 Not Found');
	} // extend as needed
    ?>
    <!doctype html>
    <html><head><title><?php echo $title; ?></title>
    <?php mongopress_pretty_style(); ?>
    </head>
    <body><div id="mp-content">
        <?php
        if($wrap_in_h4){
            echo '<h4>'.$content.'</h4>';
        }else{
            echo $content;
        }
        ?>
    </div></body></html>
    <?php
}

function mp_get_avatar($email=false, $s=80, $d='mm', $r='g', $img=false, $atts=array(), $get_profile=false, $id = false) {
	$mp = mongopress_load_mp();
	$mp_options = $mp->options();
	$default_url = $mp_options['root_url'].'mp-includes/images/add_image.png';
	if(empty($img)){ $img = $default_url; }
	if(!empty($email)){
		$mp_user = $mp->get_user_info($id);
		if(isset($mp_user['avatar'])){ $avatar_id = $mp_user['avatar']; }else{ $avatar_id = false; }
		if($avatar_id){
			$profile = false;
			$nonce = mp_create_nonce('media_view','public');
			$url = mp_get_media($nonce, $avatar_id, 'url');
		}else{
			$url = 'http://www.gravatar.com/avatar/';
			$url.= md5( strtolower( trim( $email ) ) );
			$url.= "?s=$s&d=$d&r=$r";
			if($img){
				$url = '<img src="' . $url . '"';
				if(is_array($atts)){
					foreach($atts as $key=>$val){
						$url.= ' '.$key.'="'.$val.'"';
					}
				} $url.= ' />';
			}
			if($get_profile){
				$email_hash = md5(strtolower(trim($email)));
				if(@file_get_contents('http://www.gravatar.com/'.$email_hash.'.php')){
					$str = @file_get_contents('http://www.gravatar.com/'.$email_hash.'.php');
					$profile = unserialize($str);
				}else{ 
					$no_gravatar = true;
				}
			}
		}
		if(isset($profile)){
			if((is_array($profile)) && isset($profile['entry'])){
					$profile['entry'][0]['img'] = $url;
					return $profile;
			}else{
				if($no_gravatar){
					return $default_url;
				}else{
					return $url;
				}
			}
		}else{
			return $default_url;
		}
	}else{
		$mp_user = $mp->get_user_info($id);
		if(isset($mp_user['avatar'])){ $avatar_id = $mp_user['avatar']; }else{ $avatar_id = false; }
		if($avatar_id){
			$profile = false;
			$nonce = mp_create_nonce('media_view','public');
			$url = mp_get_media($nonce, $avatar_id, 'url');
			return $url;
		}else{
			return $default_url;
		}
	}
}

function mongopress_simple_page($title,$content,$next='',$code='503') {
    if (!headers_sent()) { 
    	if ($code == '503') {
    		header('HTTP/1.1 503 Service Temporarily Unavailable');
    		header('Status: 503 Service Temporarily Unavailable');
    		header('Retry-After: 7200'); // in seconds
    	} elseif ($code == '404') {
    		header('HTTP/1.0 404 Not Found');
    		header('Status: 404 Not Found');
    	} // extend as needed
    }
    
    if (!empty($next)) $next = "<br style='clear:both;'><a href='$next' class='button' style='text-decoration:none;'>Next</a>";

		//affandy - block robot from indexing install script for security
		$meta = '';
		if ($_SERVER['REQUEST_URI'] === '/mp-admin/install.php') {
			$meta.= "<meta name='robots' content='noindex, nofollow'>";
		}

$css = mongopress_pretty_style(false);
$html = <<<EOH
    <!doctype html>
    <html><head><title>$title</title>
    $css
	$meta
    </head>
    <body><div id="mp-content">
            <h4>$title</h4>
            $content
            <br>$next
    </div></body></html>

EOH;

print $html;
}

function mongopress_load_mp(){
    if(isset($GLOBALS['_mp_cache']['mongo_mp'])) return $GLOBALS['_mp_cache']['mongo_mp'];
		if (!@class_exists(MONGOPRESS)) {
			include_once dirname(__file__) . DIRECTORY_SEPARATOR .'/mongopress.class.php';
		}
    $mp = new MONGOPRESS();
    $GLOBALS['_mp_cache']['mongo_mp'] = $mp;
    return $mp;
}

function mongopress_misconfig_form(){
    $error_content = '<p><strong>Alternatively, you could allow MongoPress to try and create the file for you</strong>,<br />by clicking on the "Edit Configuration File" below:</p>';
    $error_content.= '<p><input type="button" class="button" id="create-config" data-form-wrapper="config-creation-form" value="Edit Configuration File" /></p>';
    $error_content.= '<div id="config-creation-form" style="display:none"><form id="create-config" class="mp-form">';
        $error_content.= '<label for="site-name">'.__('Sitename (used in page titles, footers, etc)').'</label>';
        $error_content.= '<span class="input-wrapper"><input id="site-name" type="text" placeholder="'.__('The display name for your site - eg. My awesome project!').'" class="blanked radius5" /></span>';
        $error_content.= '<label for="mp-home">'.__('Directory (if stored in sub-folder - eg if installed at localhost/mongopress/ - this should be mongopress)').'</label>';
        $error_content.= '<span class="input-wrapper"><input id="mp-home" type="text" placeholder="'.__('The name of the folder(s) relative to the root of your install').'" class="blanked radius5" value="" /></span>';
        $error_content.= '<div id="mp-advanced-config" style="display:none;">';
            $error_content.= '<label for="site-description">'.__('Site Description').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="site-description" type="text" placeholder="'.__('A short explanation of your site').'" class="blanked radius5" /></span>';
            $error_content.= '<label for="mongodb-name">'.__('MongoDB Database Name').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="mongodb-name" type="text" placeholder="'.__('What would you like to call your database? - eg. mongopress').'" class="blanked radius5" value="mongopress" /></span>';
            $error_content.= '<label for="mongodb-addresse">'.__('MongoDB Host Address').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="mongodb-address" type="text" placeholder="'.__('IP address of the database host server - eg. localhost').'" class="blanked radius5" value="" /></span>';
            $error_content.= '<label for="mongodb-objs">'.__('Name of MongoPress Object Collection').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="mongodb-objs" type="text" placeholder="'.__('What would you like to name the collection where you store your objects? - eg. objs').'" class="blanked radius5" value="objs" /></span>';
            $error_content.= '<label for="mongodb-slugs">'.__('Name of MongoPress Slug Collection').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="mongodb-slugs" type="text" placeholder="'.__('What would you like to name the collection where you store your slugs? - eg. slugs').'" class="blanked radius5" value="slugs" /></span>';
            $error_content.= '<label for="mongodb-uns">'.__('Name of MongoPress Username Collection').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="mongodb-uns" type="text" placeholder="'.__('What would you like to name the collection where you store your usernames? - eg. users').'" class="blanked radius5" value="users" /></span>';
            $error_content.= '<label for="mongodb-users">'.__('Name of MongoPress User Collection').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="mongodb-users" type="text" placeholder="'.__('What would you like to name the collection where you store your users? - eg. user').'" class="blanked radius5" value="user" /></span>';
            $error_content.= '<label for="mongodb-db-user">'.__('MongoDB DB Username').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="mongodb-db-user" type="text" placeholder="'.__('What is your database username? - eg. db-user').'" class="blanked radius5" value="" /></span>';
            $error_content.= '<label for="mongodb-db-password">'.__('MongoDB DB Password').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="mongodb-db-password" type="text" placeholder="'.__('What is your database password?').'" class="blanked radius5" value="" /></span>';
            $error_content.= '<label for="mongodb-db-port">'.__('MongoDB DB Port').'</label>';
            $error_content.= '<span class="input-wrapper"><input id="mongodb-db-port" type="text" placeholder="'.__('What is your database port?').'" class="blanked radius5" value="27017" /></span>';
        $error_content.= '</div>';
        return $error_content;
}
function mongopress_misconfig_file($error_content){
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
        $temporary_url.=$folder.'/';
    };
?>
<!doctype html>
<html>
    <head>
        <?php mongopress_pretty_style(); ?>
        <title><?php _e('MongoPress Configuration Error'); ?></title>
    </head>
    <body>
        <div id="mp-content"><?php echo $error_content; ?></div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
        <script>
            jQuery(document).ready(function(){
                /* TODO: IMPROVE STABILITY OF HOW WE VERIFY THE FOLLOWING URL */
                <?php if($_SERVER['QUERY_STRING']){ ?>
                    var ajax_create_config = '../mp-includes/ajax/create-config.php';
                <?php }else{ ?>
                    var ajax_create_config = 'mp-includes/ajax/create-config.php';
                <?php } ?>
                jQuery('input#create-config').live('click',function(e){
                    e.preventDefault();
                    var wrapper_id = jQuery(this).attr('data-form-wrapper');
                    var wrapper = jQuery('div#'+wrapper_id);
                    jQuery(wrapper).toggle('slow');
                });
                jQuery('input#advanced').live('click',function(e){
                    e.preventDefault();
                    var wrapper_id = jQuery(this).attr('data-form-wrapper');
                    var wrapper = jQuery('div#'+wrapper_id);
                    jQuery(wrapper).toggle('slow');
                });
                jQuery('form#create-config').live('submit',function(e){
                    e.preventDefault();
                    var site_name = jQuery(this).find('input#site-name').val();
                    var site_description = jQuery(this).find('input#site-description').val();
                    var mp_address = jQuery(this).find('input#mp-address').val();
                    var mp_home = jQuery(this).find('input#mp-home').val();
                    var mongodb_name = jQuery(this).find('input#mongodb-name').val();
                    var mongodb_address = jQuery(this).find('input#mongodb-address').val();
                    var mongodb_objs = jQuery(this).find('input#mongodb-objs').val();
                    var mongodb_ids = jQuery(this).find('input#mongodb-ids').val();
                    var mongodb_slugs = jQuery(this).find('input#mongodb-slugs').val();
                    var mongodb_uns = jQuery(this).find('input#mongodb-uns').val();
                    var mongodb_users = jQuery(this).find('input#mongodb-users').val();
                    var nonce = jQuery(this).find('input[name="mp_nonce"]').val();
                    var mongodb_db_user = jQuery(this).find('input#mongodb-db-user').val();
                    var mongodb_db_password = jQuery(this).find('input#mongodb-db-password').val();
                    var mongodb_db_port = jQuery(this).find('input#mongodb-db-port').val();
                    var temp_url = '<?php echo $temporary_url; ?>';
					var user_name = jQuery(this).find('input#user_name').val();
					var password = jQuery(this).find('input#password').val();
                    /* FIRST ROUND OF CHECKS */
                    if(!mongodb_users){ mongodb_users = 'user' }
                    if(!mongodb_uns){ mongodb_uns = 'users' }
                    if(!mongodb_slugs){ mongodb_slugs = 'slugs' }
                    if(!mongodb_ids){ mongodb_ids = 'ids' }
                    if(!mongodb_objs){ mongodb_objs = 'objs' }
                    if(!mongodb_db_port){ mongodb_db_port = '27017' }						
                    if(!mongodb_name){
                        alert('MONGODB NAME REQUIRED');
                    }else if(!site_name){
                        alert('SITE NAME REQUIRED');
                    }else if(!user_name){
                        alert('USERNAME REQUIRED');
                    }else if(!password){
                        alert('PASSWORD REQUIRED');
                    }else{
                        var create_config = true;
                        if((mp_address=='localhost')&&(!mp_home)){
                            if(confirm('Are you sure you should have an empty "Base URL Directory"...?')) {
                                /* DO NOTHING */
                            }else{
                                create_config = false;
                            }
                        } if(create_config==true){
                            var this_input = jQuery(this).find('input[type="submit"]');
                            jQuery(this_input).addClass('loading_dark');
                            jQuery.ajax({
                                url: ajax_create_config,
                                data:({ site_name: site_name, site_description: site_description, mp_address: mp_address, mp_home: mp_home, mongodb_name: mongodb_name, mongodb_address: mongodb_address, mongodb_objs: mongodb_objs, mongodb_ids: mongodb_ids, mongodb_slugs: mongodb_slugs, mongodb_uns: mongodb_uns, mongodb_users: mongodb_users, nonce: nonce, mongodb_db_user: mongodb_db_user, mongodb_db_password: mongodb_db_password, mongodb_db_port: mongodb_db_port, temp_url: temp_url }),
                                type: "POST",
                                dataType: 'json',
                                success: function(result){
                                    jQuery(this_input).removeClass('loading_dark');
                                    if(result.success!=true){
                                        alert('ERROR: '+result.message);
                                    }else{
                                        alert(result.message);
                                        window.location = result.temp_url;
                                    }
                                },
                                failure: function(){
                                    alert('ERROR FINDING SAMPLE CONFIG FILES');
                                }
                            });
                        }
                    }
                });
            });
        </script>
    </body>
</html>
<?php
exit;
}

function mongopress_simple_form($data,$title,$message='',$id='default_id',$class='mp-form'){
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
foreach ($data as $key=>$v) {
	$v['key'] = $key;
	if ($key == '_nonce') $form .= ' TODO todo_none!';
	elseif ($key == '_action') $form .= "<input id='$key' placeholder='$key' type='hidden' name='$key' value='{$v['default']}' />";
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

$error['MONGODB NAME REQUIRED'] = __('MONGODB NAME REQUIRED');
$error['SITE NAME REQUIRED'] = __('SITE NAME REQUIRED');
$error['USERNAME REQUIRED'] = __('USERNAME REQUIRED');
$error['PASSWORD REQUIRED'] = __('PASSWORD REQUIRED');

$content = "<h4>$title</h4><p>$message</p><form id='$id' class='$class'>" . $form;
$content .= '<div id="mp-advanced-config" style="display:none;">'.$advanced .'</div>';

$content .= '<p class="divider"><input type="button" class="button" id="advanced" value="'.__('ADVANCE CONFIG').'" style="float:left; margin-bottom:35px;" data-form-wrapper="mp-advanced-config" /><input type="submit" class="button-action submit" id="submit" value="'.__('SAVE CONFIG').'" style="float:right; margin-bottom:35px;" /></p>';
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
        <script>
            jQuery(document).ready(function(){

				var mp_root = '{$_MP['HOME']}';
				var ajax_create_config = mp_root + 'mp-includes/ajax/install.php';

                jQuery('input#create-config').live('click',function(e){
                    e.preventDefault();
                    var wrapper_id = jQuery(this).attr('data-form-wrapper');
                    var wrapper = jQuery('div#'+wrapper_id);
                    jQuery(wrapper).toggle('slow');
                });
                jQuery('input#advanced').live('click',function(e){
                    e.preventDefault();
                    var wrapper_id = jQuery(this).attr('data-form-wrapper');
                    var wrapper = jQuery('div#'+wrapper_id);
                    jQuery(wrapper).toggle('slow');
                });
				// This list needs trimmed and automated TODO
                jQuery('form#create-config').live('submit',function(e){
                    e.preventDefault();
                    $vals_js
                    var temp_url = '$temporary_url';
          						
                    if(!mongodb_name){
                        alert('{$error["MONGODB NAME REQUIRED"]}');
                    }else if(!site_name){
                        alert('SITE NAME REQUIRED');
                    }else if(!user_name){
                        alert('USERNAME REQUIRED');
                    }else if(!password){
                        alert('PASSWORD REQUIRED');
                    }else{
                        var create_config = true;
                        
						if(create_config==true){
                            var this_input = jQuery(this).find('input[type="submit"]');
                            jQuery(this_input).addClass('loading_dark');
                            jQuery.ajax({
                                url: ajax_create_config,
								// automate this too TODO
                                data:({ $ajax_data }),
                                type: "POST",
                                dataType: 'json',
                                success: function(result){
                                    jQuery(this_input).removeClass('loading_dark');
                                    if(result.success!=true){
                                        alert('ERROR: '+result.message);
                                    }else{
                                        alert(result.message);
                                        window.location = result.temp_url;
                                    }
                                },
                                failure: function(){
                                    alert('ERROR CREATING CONFIG FILES');
                                }
                            });
                        }
                    }
                });
            });
        </script>
    </body>
</html>
EOH;

print $html;
}

function mongopress_content_folder($folders, $content_folder){
    echo '<h3 class="widget-title header">'.__('Content Template Detected').'</h3>';
    echo '<p>'.__('This means that if you import the detected content template that you will be setting-up the following object types with the (labeled) counts for each of the types...').'</p><br /><br />';
    $content = array();
    $object_list = '';
    while (false !== ($folder = readdir($folders))) {
        if(!strstr($folder, '.')){
            $this_object_count = 0;
            if($files = opendir($content_folder.'/'.$folder.'/')){
                while (false !== ($file = readdir($files))) {
                    if(strstr($file, '.html')){
                        $this_object_count++;
                    }
                }
                closedir($files);
            }
            $content['types'][$folder] = true;
            if($this_object_count==1){
                $object_list.= '<li><strong>'.$folder.'</strong> ( '.$this_object_count.' '.__('Object').' )</li>';
            }elseif($this_object_count>1){
                $object_list.= '<li><strong>'.$folder.'</strong> ( '.$this_object_count.' '.__('Objects').' )</li>';
            }
        }
    }
    closedir($folders);
    $object_type_count = count($content['types']);
    if($object_type_count==1){ $object_text = __('Object'); }else{ $object_text = __('Objects'); }
    echo '<ul>'.$object_list.'</ul>';
}





function mongopress_load_core($object_id)
{
	global $_MP;
    
    $mp = mongopress_load_mp();
    $mongo = mongopress_load_perma();
    $perma = $mongo->current();
    $options = $mp->options();

    
    
	if(! (($options['db_name'])&&($options['obj_col'])&&($options['slug_col'])&&($options['user_names'])&&($options['user_col'])&&($options['site_name']))) mongopress_install_error($object_id,'db_settings'); // db settings problem
			

    try {
		$m = mongopress_load_m();
		$db = $m->selectDB($options['db_name']);
        // TODO - users?
		$users = $db->$options['user_names'];
		$user_count = $users->find()->count();

		if($options['skip_htaccess']) {
			$GLOBALS['direct_object_id'] = $object_id;
			$GLOBALS['current_object_id'] = $object_id;
		} else {

            if (isset($_SERVER['HTTP_MOD_REWRITE']) &&  $_SERVER['HTTP_MOD_REWRITE'] == 'On') $mod_rewrite = true;
            else $mod_rewrite = false;

            if (!$mod_rewrite && !$options['skip_htaccess']) mongopress_install_error($object_id,'htaccess misconfiguration');
        }
            
		$admin_slug = $options['admin_slug'];
		$admin_len = strlen($admin_slug);

		$media_slug = $options['media_slug'];
		$media_len = strlen($media_slug);

		if(substr($perma,0,$admin_len) == $admin_slug || substr($perma,0,8)=='mp-admin') { // Catches all admin section

			if (substr($perma,0,8)=='mp-admin') $perma = substr($perma, 3);
			else $perma = 'admin' . substr($perma,$admin_len);
			require_once($_MP['DOCUMENT_ROOT'].'/mp-admin/admin.php');
			
			//mp_refresh_cookies();
			$theme = $options['theme'];
			$theme_index = $_MP['THEME_ROOT'].'/'.$theme.'/admin/index.php';
			$theme_base = $_MP['THEME_ROOT'].'/'.$theme.'/';

			if(file_exists($theme_index)) $use_theme = true;
			else $use_theme = false;
			//echo '$perma = '.$perma; exit;
			switch ($perma) {
				case 'admin':
					if (!$mp->is_logged_in()) {
						mp_admin_login_form($options);
						die();
					}else{
						if($use_theme) require_once($theme_index);
						else require_once($_MP['DOCUMENT_ROOT'].'/mp-admin/mp-index.php');
						break;
					}
				case 'admin/logout':
				case 'admin/logout.php':
					require_once($_MP['DOCUMENT_ROOT'].'/mp-admin/logout.php');
					break;
				case 'admin/install.php':
					require_once($_MP['DOCUMENT_ROOT'].'/mp-admin/install.php');
					break;
				default:
					$theme_file = $_MP['THEME_ROOT'].'/'.$theme.'/'.$perma;
					$default_file = $_MP['DOCUMENT_ROOT'].'/mp-'.$perma;
					if ($use_theme && file_exists($theme_file.'.php')) require_once($theme_base.$perma.'.php');
                    elseif (file_exists($default_file.'.php')) require_once($default_file.'.php');
					else throw new Exception(__('Which part of Admin do you want?'));
					break;
			}

		} elseif(substr($perma,0,$media_len) == $media_slug || substr($perma,0,8)=='mp-media') { 	

			if (substr($perma,0,8)=='mp-media') $perma = substr($perma, 3);
			else $perma = 'media' . substr($perma,$admin_len);

			// magic media section
			$filename_array = explode('/', $perma);
			$actual_name = $filename_array[1];
			if(!empty($actual_name)) mp_display_media($actual_name,'image');

		} else {

			// LOGIC FOR PUBLIC PAGES

			if(empty($perma) && $options['skip_htaccess'] && $object_id == '') $theme_file = $options['theme'].'/index.php';
			elseif (empty($perma)) $theme_file = $options['theme'].'/index.php';
	 		else $theme_file = $options['theme'].'/single.php';
			
			if (!file_exists($_MP['THEME_ROOT'].'/'.$theme_file)) $theme_file = $options['theme'].'/index.php';


			//overwrite perma to use plugin perma setting, ie. rss feed
			//affandy - add new logic so user can create perma link from plugin
			$this_theme_file = $_MP['THEME_ROOT'].'/'.$theme_file;
			
			if (file_exists($this_theme_file)) {
				//be carefull here. this may be open to LFI/RFI attack 
				//should we use realpath function?
				$plugin_theme_file = apply_filters('mp_theme_file_perma', $this_theme_file, $perma);
				if ($plugin_theme_file !== NULL) {
				 require_once($plugin_theme_file);
				} else {
				 require_once($this_theme_file);
				}
			} else { 
				throw new Exception(sprintf(__('Missing Theme File : %s'),$this_theme_file)); 
			}
		}

	} catch(MongoConnectionException $e) {
        $error = __('Error connecting to MongoDB Server');
        mongopress_pretty_page($error,'MongoPress - MongoDB Error',true,'503');
    } catch(MongoException $e) {
        $error = __('Error: '). $e->getMessage();
				$error .= __(' - Error code: ') . $e->getCode();
        mongopress_pretty_page($error,'MongoPress - MongoDB Error',true,'503');
    } catch (Exception $e) {
		$error = __('Error: ').get_class($e) .' '. $e->getMessage();
        mongopress_pretty_page($error,'Error',true,'404');
	}

}

function mp_json_send($object){
	if((is_array($object))||(is_object($object))){
		$json_obj = json_encode($object);
	}else{
		$json_obj['success'] = false;
		$json_obj['message'] = __('mp_json_end requires an object or an array');
	} echo $json_obj;
	exit;
}

function mp_json_nonce_check($nonce, $key){
	if(!mp_verify_nonce($nonce,$key)){
		$progress['success'] = false;
		$progress['message'] = __('Unidentified Object in The Imperial Vortex!!!');
		mp_json_send($progress);
	}
}
