<?php
/*
Needs work.

- after we have created a user - we need to do nonce validated installation actions.
- in order to be quick - I've cloned this from install.php

*/
if (isset($_GET['problem'])) $type_of_problem = $_GET['problem']; else $type_of_problem = false;
require_once(dirname(dirname(__FILE__)).'/mp-includes/includes.php');
if (!$type_of_problem){
	mongopress_pretty_page(__('Ming the Merciless has terminated this action - What is your problem Earthling?'),__('MongoPress Error'));
	exit;
}

try{

    $mp = mongopress_load_mp(); $options = $mp->options();
    
    if(($type_of_problem!='htaccess')&&($type_of_problem!='missing_user')&&($type_of_problem!='missing_objs')){
        if(file_exists(dirname(dirname(__FILE__)).'/mp-settings/config.php')){
            require_once(dirname(dirname(__FILE__)).'/mp-settings/config.php');
            require_once(dirname(dirname(__FILE__)).'/mp-settings/security.php');
            if(@fopen($options['root_url'].'mp-admin/index.php', "r")) {
                echo '<script>window.location="http://'.$_SERVER['HTTP_HOST'].$options['root_url'].'"</script>';
                exit;
            }
        }
    }

    $webserver = $_SERVER["SERVER_SOFTWARE"];
    if (strstr($webserver, "nginx")) { //skip htaccess check and put redirect rules here
        $config = htmlentities('try_files $uri $uri/ /index.php;');
        $config_text = '<p>'.__('Nginx Webserver Detected - Make sure you check this configuration exists in your nginx server config.').'</p>';
        $config_text.= '<p>'.__('Mongopress redirect rules').'</p>';
        $config_text.= '<p>'.$config.'</p>';
        mongopress_pretty_page(__($config_text),__('MISSING CONFIG'),true);
    }

    if($type_of_problem=='htaccess'){
        if(file_exists(dirname(dirname(__FILE__)).'/mp-settings/config.php')){
            require_once(dirname(dirname(__FILE__)).'/mp-settings/config.php');
            require_once(dirname(dirname(__FILE__)).'/mp-settings/config.php');
            mp_get_simple_header(__('Installation'));
            mp_enqueue_script_theme('install', $options['root_url'].'mp-includes/js/install.js', array('jquery'));
            echo '<div id="site-wrapper" class="radius5">';
            $options = $mp->options();
            $got_content_folder = false;
            $content_folder = dirname(dirname(__FILE__)).'/mp-content/themes/'.$options['theme'].'/content/';
            if($folders = opendir($content_folder)){
                $got_content_folder = true;
                $m = mongopress_load_m();
                $db = $m->$options['db_name'];
                $objects = $db->$options['obj_col'];
                $object_count = $objects->find()->count();
                if($object_count>0){
                    $got_content_folder = false;
                }
            } if($got_content_folder){
                $import_button = '<form id="mp-install" class="mp-form"><br style="clear:both; display:block;"><p><input type="button" id="mp-import-html" class="button" value="IMPORT CONTENT" /></p><br style="clear:both; display:block; ">'.mp_nonce_field('mp-install','mp_nonce',true,false).'</form>';
                ?>
                <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
                <script>
                    jQuery('input#mp-import-html').live('click',function(e){
                        var nonce = jQuery('form#mp-install').find('input[name="mp_nonce"]').val();
                        e.preventDefault();
                        var this_input = jQuery(this).find('input[type="button"]');
                        jQuery(this_input).addClass('loading_dark');
                        jQuery.ajax({
                            url:mp_root_url+'mp-includes/pjax/import-html.php',
                            data:({ nonce: nonce }),
                            type: "POST",
                            dataType: 'json',
                            success: function(result){
                            jQuery(this_input).removeClass('loading_dark');
                                if(result.success!=true){
                                    alert('<?php _e('OBJECT IMPORT ERROR: ') ?>'+result.message);
                                }else{
                                    alert('<?php _e('OBJECTS IMPORTED') ?>');
                                    window.location = mp_root_url;
                                }
                            },
                            failure: function(){
                                alert('<?php _e('ERROR FINDING IMPORT FILES') ?>');
                            }
                        });
                    });
                </script>
                <?php
            }
            if($type_of_problem=='htaccess'){
                $problem_text = __('.htaccess does not exist!');
                $file = $options['root_url'].'.htaccess';
                if($options['home_directory']){
                    $temp_home_directory = '/'.$options['home_directory'].'/';
                }else{
                    $temp_home_directory = '/';
                }
                $htaccess_content = "
        # BEGIN MongoPress Rules
        <IfModule mod_rewrite.c>

            # MONGOPRESS ESSENTIALS
            RewriteEngine On
            RewriteBase ".$temp_home_directory."
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule . ".$temp_home_directory."index.php [L]

            # CANONICAL REWRITES
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_URI} !.html
            RewriteCond %{REQUEST_URI} !.php
            RewriteCond %{REQUEST_URI} !(.*)/$
            RewriteRule ^(.*)$ http://%{HTTP_HOST}/$1/ [L,R=301]

            # PREVENT DUPLICATES
            RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /[^?]*\?\ HTTP/
            RewriteRule (.*) http://%{HTTP_HOST}/$1? [R=301,L]

        </IfModule>
        # END MongoPress Rules
                ";
                $htaccess_content_html = "
                    <pre>
        # BEGIN MongoPress Rules
        &lt;IfModule mod_rewrite.c&gt;

            # MONGOPRESS ESSENTIALS
            RewriteEngine On
            RewriteBase ".$temp_home_directory."
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule . ".$temp_home_directory."index.php [L]

            # CANONICAL REWRITES
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_URI} !.html
            RewriteCond %{REQUEST_URI} !.php
            RewriteCond %{REQUEST_URI} !(.*)/$
            RewriteRule ^(.*)$ http://%{HTTP_HOST}/$1/ [L,R=301]

            # PREVENT DUPLICATES
            RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /[^?]*\?\ HTTP/
            RewriteRule (.*) http://%{HTTP_HOST}/$1? [R=301,L]

        &lt;/IfModule&gt;
        # END MongoPress Rules
                    </pre>
                ";
                $home_url = "<a href='{$options['root_url']}'>".__('Return to the Homepage')."</a>";
                $htaccess_content_html .= "<p><strong>".sprintf(__("If not using .htaccess you will need to modify config.php to:")."<br><br>define('SKIP_HT',true);<br><br>".__("If you have already done either of these, please %s to continue with the installation."),$home_url)."</strong></p>";
                $progress = true;
                if($options['home_directory']){
                    $handle = @fopen($_SERVER['DOCUMENT_ROOT'].'/'.$options['home_directory'].'/.htaccess', "w");
                }else{
                    $handle = @fopen($_SERVER['DOCUMENT_ROOT'].'/.htaccess', "w");
                }
                if (!@fwrite($handle, $htaccess_content) ===  true) {
                    $progress = false;
                }
                if($progress != false){
                    $problem_text = '<h3 class="article-title header">'.__('We noticed that you did not have a .htaccess file, but were succesfully able to add it for you.').'</h3>';
                    if($got_content_folder){
                        $problem_text.= '<p>'.__('Additionally, you may wish to consider importing the available content template.').'</p>';
                        $problem_text.= $import_button;
                    } if($object_count>0){
                        $problem_text.= '<h3 class="article-title header"><a href="'.$mp_options['root_url'].'">'.__('Return to Homepage').'</a></h3>';
                    }else{
                        $problem_text.= '<br /><br /><p>'.__('Alternatively, you could simply ').'<a href="'.$mp_options['root_url'].'">'.__('Return to Homepage').'</a></p>';
                    }
                }else{
                    $problem_text = '<p>'.__('We do not have the necessary permissions required to create the .htaccess file for you, so you will need to manually add one with the following lines of code to the root of this installation:').'</p>';
                    $problem_text.= '<p>'.$htaccess_content_html.'</p>';
                    if($got_content_folder){
                        $problem_text.= '<p>'.__('Additionally, you may wish to consider importing the available content template.').'</p>';
                        $problem_text.= $import_button;
                    }
                }
            }else{
                $problem_text = __('Unknown error in the imperial vortex - Please contact Overlord Ming');
            }
            echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-includes/css/reset.css">';
            echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-includes/css/basics.css">';
            echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-admin/css/admin.css">';
            echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-admin/css/forms.css">';
            echo '<div id="primary-content">';
                echo '<article><header><h3 class="article-title header">'.__('MongoPress Error').'</h3></header><div class="content">';
                    try{
                        $errors = mongopress_check_versions();
                        if(!$errors){
                            echo $problem_text;
                        }
                    }catch(MongoConnectionException $e) {
                        echo __('Error connecting to MongoDB Server');
                    }catch(MongoException $e) {
                        echo __('Error: '). $e->getMessage();
                    }
                echo '</div></article>';
            echo '</div>';
            echo '<div id="secondary-content">';
                echo '<div class="widget">';
                    if($got_content_folder){
                        mongopress_content_folder($folders, $content_folder);
                    }else{
                        if($object_count>0){
                            echo '<h3 class="widget-title header">'.__('Content Already Added').'</h3>';
                        }else{
                            echo '<h3 class="widget-title header">'.__('No Content Templates').'</h3>';
                        }
                    }
                echo '</div>';
            echo '</div>';
            echo '</div>';
            mp_get_footer();
        }else{
            echo __('The imperial guards are dazed and confused trying to locate config.php!');
        }
    }elseif($type_of_problem=='site_address'){
        $error_content = '<h4>'.__('OOPS - IT SEEMS THAT MONGOPRESS HAS BEEN MIS-CONFIGURED').'</h4><p>'.__('This error is due to an incorrect <strong>SITE_ADDRESS</strong> within the <strong>config.php</strong> file at the root of this installation. If your are developing locally, this is often as simple as localhost, or localhost:80, etc. Do not include any http:// statements, which is the same as if you have deployed this on a live public server, where the site address should be your-domain.com').'</p>';
        if(is_writable(dirname(dirname(__FILE__)).'/')){
                $misconfig = mongopress_misconfig_form();
                if (!empty($misconfig)) {
                    $error_content .= $misconfig;
                }
                $error_content.= '<p><input type="button" class="button" id="advanced" value="'.__('ADVANCE CONFIG').'" style="float:left; margin-bottom:35px;" data-form-wrapper="mp-advanced-config" /><input type="submit" class="button-action submit" id="submit" value="'.__('SAVE CONFIG').'" style="float:right; margin-bottom:35px;" /></p>';
                $error_content.= '<br style="clear:both; display:block"/>';
                $error_content.= mp_nonce_field('create-config','mp_nonce',true,false);
            $error_content.= '</form></div>';
        }else{
            $error_content.= '<p>'.__('Unfortunately, you do not have the required permissions needed to create the config.php file remotely, so please add and configure it manually.').'</p>';
        }
        mongopress_misconfig_file($error_content);
    }elseif ($type_of_problem=='home_directory'){
        $error_content = '<h4>'.__('OOPS - IT SEEMS THAT MONGOPRESS HAS BEEN MIS-CONFIGURED').'</h4><p>'.__('This error is due to an incorrect <strong>BASE_URL_DIRECTORY</strong> within the <strong>config.php</strong> file at the root of this installation. If your are developing locally, this is often as simple as adding the name of the folder that the files have been added to, which is the same as if it was online and hosted at your-domain.com/mp/ the BASE_URL_DIRECTORY would be just <strong>mp</strong>').'</p>';
        if(is_writable(dirname(dirname(__FILE__)).'/')){
                $misconfig = mongopress_misconfig_form();
                if (!empty($misconfig)) {
                    $error_content .= $misconfig;
                }
                $error_content.= mp_nonce_field('create-config','mp_nonce',true,false);
                $error_content.= '<p><input type="button" class="button" id="advanced" value="'.__('ADVANCE CONFIG').'" style="float:left; margin-bottom:35px;" data-form-wrapper="mp-advanced-config" /><input type="submit" class="button-action submit" id="submit" value="'.__('SAVE CONFIG').'" style="float:right; margin-bottom:35px;" /></p>';
                $error_content.= '<br style="clear:both; display:block"/>';
            $error_content.= '</form></div>';
        }else{
            $error_content.= '<p>'.__('Unfortunately, you do not have the required permissions needed to create the config.php file remotely, so please add and configure it manually.').'</p>';
        }
        mongopress_misconfig_file($error_content);
    }elseif ($type_of_problem=='missing_config'){
        $error_content = '<h4>'.__('IT APPEARS YOU ARE MISSING THE "config.php" FILE').'</h4><p>'.__('If this is your first time, you probably just need to rename some of the included files:<br /><br />mp-settings/config-sample.php to mp-settings/config.php <strong>and</strong><br />mp-settings/security-sample.php to mp-settings/security.php<br /><br />(before you do, please be sure to make the necessary changes and properly configure things)').'</p>';
        if(is_writable(dirname(dirname(__FILE__)).'/mp-settings/')){
                $misconfig = mongopress_misconfig_form();
                if (!empty($misconfig)) {
                    $error_content .= $misconfig;
                }
                $error_content.= '<p><input type="button" class="button" id="advanced" value="'.__('ADVANCE CONFIG').'" style="float:left; margin-bottom:35px;" data-form-wrapper="mp-advanced-config" /><input type="submit" class="button-action submit" id="submit" value="'.__('SAVE CONFIG').'" style="float:right; margin-bottom:35px;" /></p>';
                $error_content.= '<br style="clear:both; display:block"/>';
                $error_content.= mp_nonce_field('create-config','mp_nonce',true,false);
            $error_content.= '</form></div>';
        }else{
            $error_content.= '<p>'.__('Unfortunately, you do not have the required permissions needed to create the config.php file remotely.').'</p>';
            $error_content.= '<p>'.__('If you would like to enable these features, you will need to alter your file permissions, which can be done by running the following shell command:').'</p>';
            $error_content.= '<h4>'.__('chmod 777 '.dirname(dirname(__FILE__))).'/mp-settings</h4><p><strong>'.__('Please remember to chmod 755 after the installation is complete!').'</strong></p>';
        }
        mongopress_misconfig_file($error_content);
    }elseif(($type_of_problem=='missing_user')||($type_of_problem=='missing_objs')){
        /* SCAN FOR AND IMPORT CONTENT */
        if(file_exists(dirname(dirname(__FILE__)).'/mp-settings/config.php')){
            require_once(dirname(dirname(__FILE__)).'/mp-settings/config.php');
            $m = mongopress_load_m();
            $db = $m->$options['db_name'];
            $objects = $db->$options['obj_col'];
            $object_count = $objects->find()->count();
            mp_enqueue_script_theme('jquery', $options['root_url'].'mp-includes/js/jquery-1.6.4.min.js', false, '1.6.4');
            mp_enqueue_script_theme('languages', $options['root_url'].'mp-includes/js/languages.php', false);
            mp_enqueue_script_theme('install', $options['root_url'].'mp-includes/js/install.js', array('jquery'));
			mp_get_simple_header(__('Installation'));
			/* TODO: ENQUE THESE STYLES */
            echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-includes/css/reset.css">';
            echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-includes/css/basics.css">';
            echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-admin/css/admin.css">';
            echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-admin/css/forms.css">';
            $got_content_folder = false;
            $content_folder = dirname(dirname(__FILE__)).'/mp-content/themes/'.$options['theme'].'/content/';
            if($folders = @opendir($content_folder)){
                $got_content_folder = true;
            }
            echo '<div id="site-wrapper" class="radius5">';
			if($type_of_problem=='missing_objs'){
				echo '<div id="primary-content">';
			}else{
				echo '<div id="primary-content" class="no-sidebar">';
			}
                echo '<article><header><h3 class="article-title header">'.__('Installing MongoPress').'</h3></header><div class="content">';
                    try{
                        $errors = mongopress_check_versions();
                        if(!$errors){
                            echo '<h3 class="article-title header">'.__('Successfully connected to MongoDB Server').'</h3>';
                            if(is_writable(dirname(dirname(__FILE__)).'/')){
                                echo '<h3 class="article-title header">'.__('Please Note That The Root Folder Has Write Permissions Enabled - You Really Should Resolve This').'</h3>' . '<br><br><pre> chmod 755 '.dirname(dirname(__FILE__)). "/\n chmod 755 ".dirname(dirname(__FILE__)). "/mp-settings/\n</pre>";
                            }
                            $users = $db->$options['user_names'];
                            $objects = $db->$options['obj_col'];
                            $user_count = $users->find()->count();
                            $object_count = $objects->find()->count();
                                if($type_of_problem!='missing_objs'){
                                    if($user_count<1){
                                        echo '<p><br /><br />'.__('No User Accounts Created Yet').'</p><br /><br />';
                                        echo '<form id="add-user" class="mp-form" style="padding-top:15px; padding-bottom:15px; border-top: 1px dotted #CCC;">';
                                        echo '<label for="username">'.__('Username').'</label>';
                                        echo '<span class="input-wrapper radius5">';
                                            echo '<input id="username" placeholder="'.__('Your unique display name').'" type="text" name="username" autocomplete="off" class="blanked" />';
                                        echo '</span>';
                                        echo '<label for="email">'.__('Email').'</label>';
                                        echo '<span class="input-wrapper radius5">';
                                            echo '<input id="email" placeholder="'.__('Confirmation functionality coming soon...').'" type="email" name="email" autocomplete="off" class="blanked" />';
                                        echo '</span>';
                                        echo '<label for="password">'.__('Password').'</label>';
                                        echo '<span class="input-wrapper radius5">';
                                            echo '<input id="password" placeholder="'.__('...so pick something you can remember!').'" type="password" name="password" autocomplete="off" class="blanked" />';
                                        echo '</span>';
                                        echo '<label for="name">'.__('Full Display Name').'</label>';
                                        echo '<span class="input-wrapper radius5">';
                                            echo '<input id="name" placeholder="'.__('Fullname').'" type="text" name="name" autocomplete="off" class="blanked" />';
                                        echo '</span>';
                                        echo '<input type="submit" id="mp-create-user" value="'.__('CREATE NEW USER').'" class="button" style="float:right;" />';
                                    }else{
                                        echo '<form id="add-user" class="mp-form">';
                                        echo '<h3 class="article-title header">'.__('User Account Already Created').'</h3>';
                                    }
                                    mp_nonce_field('add-user','mp_nonce','public');
                                }
								if($type_of_problem=='missing_objs'){
									if($object_count<1){
										echo '<div style="clear:both; float:left; width:100%; padding-top:15px; display:block; margin-top:15px; border-top: 1px dotted #CCC;"/>';
										echo '<p><strong>'.__('No Objects Created Yet').'</strong></p>';
										if($got_content_folder){
											echo '<p>'.__('Import the detected content template using the button below:').'</p>';
											echo '<input type="button" id="mp-import-html" class="button" value="'.__('IMPORT CONTENT').'" />';
											mp_nonce_field('mp-install','mp_nonce');
										} echo '</div>';
									}else{
										echo '<br style="clear:both">';
										echo '<h3 class="article-title header">'.__('Objects Already Included').'</h3>';
									}
								}
                                if(($object_count>0)&&($user_count>0)){
                                    echo '<h3 class="article-title header"><a href="'.$options['root_url'].'">'.__('Return to Homepage').'</a></h3>';
                                }
                            echo '</form>';
                        }
                    }catch(MongoConnectionException $e) {
                        $error = __('Error connecting to MongoDB Server');
                        mongopress_pretty_page($error,__('MongoDB Error'),true);
                    }catch(MongoException $e) {
                        $error = sprintf(__('Error: %s'),$e->getMessage());
                        mongopress_pretty_page($error,__('MongoDB Error'),true);
                    }
                echo '</div></article>';
            echo '</div>';
			/* NO LONGER NEEDED...? */
			if($type_of_problem=='missing_objs'){
				echo '<div id="secondary-content">';
					echo '<div class="widget">';
						if($object_count>0){
							echo '<h3 class="widget-title header">'.__('Content Already Added').'</h3>';
						}else{
							if($got_content_folder){
								mongopress_content_folder($folders, $content_folder);
								}else{
								echo '<h3 class="widget-title header">'.__('No Content Templates').'</h3>';
							}
						}
					echo '</div>';
				echo '</div>';
				echo '</div>';
			}
            mp_get_footer();
        }else{
            mongopress_pretty_page(__('Missing Config - Refresh Root of Install to Continue'),__('MISSING CONFIG'),true);
        }
    }else {
        mongopress_pretty_page(__('Unknown Error'),__('UNKNOWN ERROR'),true);
    };
}catch(MongoConnectionException $e) {
    $error = __('Error connecting to MongoDB Server');
    mongopress_pretty_page($error,__('MongoDB Error'),true);
}catch(MongoException $e) {
    $error = sprintf(__('Error: %s'),$e->getMessage());
    mongopress_pretty_page($error,__('MongoDB Error'),true);
}
