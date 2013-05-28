<?php
mongopress_check_installation();

function mongopress_check_installation(){

// RUN THROUGH ALL OUT INSTALLATION TESTS - at the end write - mp-cache/flags/installed.flag
// TODO - need to double check everything here - all requirements for MP - not sure what I'm missing atm

	Global $_MP;

    if(!class_exists('Mongo')){
        mongopress_pretty_page(__('MongoDB PHP Drivers Not Installed.').'<br />'.__('( you could use pecl to install the driver with "pecl install mongo" ) - or download from:').'<br /><a href="http://www.mongodb.org/display/DOCS/PHP+Language+Center">http://www.mongodb.org/display/DOCS/PHP+Language+Center</a>',__('MongoPress - Platform Dependency Errors'),true);
        exit();
    }

    $error_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."mp-includes/error.php";
    $install_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."mp-admin/install.php";

    if (! file_exists(dirname(dirname(__FILE__)).'/mp-settings/config.php')){
        echo '<script>window.location="'.$install_url.'?problem=missing_config"</script>';
        exit;
    }
    if (! file_exists(dirname(dirname(__FILE__)).'/mp-settings/security.php')){
        echo '<script>window.location="'.$install_url.'?problem=missing_config"</script>';
        exit;
    }

    $mp = mongopress_load_mp();
    $mongo = mongopress_load_perma();
    $perma = $mongo->current();
    $options = $mp->options();
    if ($options['root_url'] == '/') {
        $options['root_url'] = $_SERVER['REQUEST_URI'];
    }

	if ($options['root_path'] <> $_MP['DOCUMENT_ROOT'].'/') {
		$suggest = preg_replace("#{$_SERVER['DOCUMENT_ROOT']}/#",'',$_MP['DOCUMENT_ROOT']);
		if(empty($options['home_directory'])){ $this_home_dir_msg = 'an empty string!'; }else{ $this_home_dir_msg = $options['home_directory']; }
		mongopress_pretty_page(__('<h4>MongoPress - Configuration Problem</h4><br />Your config value for BASE_URL_DIRECTORY should probably be: ') .$suggest.sprintf(__(' and not %s'),$this_home_dir_msg),__('MongoPress - Config Errors'),false);
		die();
	}

    try{
        /* RUN SOME ADDITIONAL INTERNAL CHECKS FOR SPECIFIC PLATFORMS VERSIONS ETC */
        if(($options['db_name'])&&($options['obj_col'])&&($options['slug_col'])&&($options['user_names'])&&($options['user_col'])&&($options['site_name'])){

            $m = mongopress_load_m();
            $db = $m->selectDB($options['db_name']);
            $users = $db->$options['user_names'];
            $user_count = $users->find()->count();
			
			if($user_count<1){
					echo '<script>window.location="'.$error_url.'?problem=missing_user"</script>';
                    exit;
			}
                    

            $webserver = strtolower($_SERVER['SERVER_SOFTWARE']);
            if (strstr($webserver, "nginx")) {
                $server = 'nginx';
                $htaccess_exists = true;
            } elseif (strstr($webserver, "apache")) {
                $server = 'apache';
            } else {
                mongopress_pretty_page(__('Error: Unsupported Web Server - currently only nginx and apache supported') . ' '. $_SERVER['SERVER_SOFTWARE'],__('Unsupported Web Server'),false);
                exit;
            }
            if($options['skip_htaccess']){
                $htaccess_exists = true;
            }else{
                $htaccess_exists = false;
            }

            if ($_SERVER['HTTP_MOD_REWRITE'] != 'On' &&  !$options['skip_htaccess'] ) {
                mongopress_pretty_page(__('Error: mod_rewrite is not working on your system. Please set config HT_SKIP to true') . ' '. $_SERVER['SERVER_SOFTWARE'],__('mod_rewrite problem'),false);
                exit;
            }
            //affandy - hacks and overwrite htaccess checking for nginx

       		
			$theme_file_index = $options['theme'].'/index.php';
			$theme_file_single = $options['theme'].'/single.php';

 			if ( !file_exists($_MP['THEME_ROOT'].$theme_file_index)) {
					mongopress_pretty_page(__('This theme is missing an index.php file!'),__('Missing File in Theme'),false);
			}

			if ( !file_exists($_MP['THEME_ROOT'].$theme_file_single)) {
					mongopress_pretty_page(__('This theme is missing an single.php file!'),__('Missing File in Theme'),false);
			}
			

            if(! @fopen($_MP['DOCUMENT_ROOT'].'/.htaccess', "r")) {
				echo '<script>window.location="'.$error_url.'?problem=htaccess"</script>';
                exit;
			}

        } else {
            $error = '';
            echo '<style>h4{border-bottom:none !important;}</style>';
            if(!$options['db_name']){
                $error.= __('Config is Missing Database Name').'<br /><br />';
            } if(!$options['obj_col']){
                $error.= __('Config is Missing Name of Object Collection').'<br /><br />';
            } if(!$options['slug_col']){
                $error.= __('Config is Missing Name of Slug Collection').'<br /><br />';
            } if(!$options['cookie_col']){
                $error.= __('Config is Missing Name of Cookie Collection').'<br /><br />';
            }if(!$options['user_names']){
                $error.= __('Config is Missing Name of Usernames Collection').'<br /><br />';
            } if(!$options['user_col']){
                $error.= __('Config is Missing Name of User Collection').'<br /><br />';
            }
            if(!$options['server_address']){
                $error.= __('Config is Missing Website Address').'<br /><br />';
            } if(!$options['site_name']){
                $error.= __('Config is Missing Site Name');
            }
            mongopress_pretty_page($error,'MongoPress - Platform Dependency Errors',true);
        }
    }catch(MongoConnectionException $e) {
        $error = __('Error connecting to MongoDB Server');
        mongopress_pretty_page($error,'MongoPress - MongoDB Error',true);
    }catch(MongoException $e) {
        $error = __('Error: '). $e->getMessage();
        mongopress_pretty_page($error,'MongoPress - MongoDB Error',true);
    }

	$flag_file = $_MP['CACHE'].'/flags/installed.flag';
	
	if (@touch($flag_file)) 
	mongopress_pretty_page(__('MongoPress is installed correctly'),'MongoPress - is successfully installed',true);
	else {
		mongopress_pretty_page('Need write permissions on ' . $_MP['CACHE'] . ' and subdirectories',__('Error: MongoPress cache incorrectly set'),false);
	}
}
