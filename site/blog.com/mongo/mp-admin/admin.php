<?php

/* TODO: ADMIN CLASS */

function mp_admin_login_form($options) {
    // This is almost a generic set of header calls - TODO
	// Someone keeps replaching this! :-( 4 Times and Counting!!!!!!!!!
	mp_enqueue_script_theme('jquery', $options['root_url'].'mp-includes/js/jquery-1.6.4.min.js', false, '1.6.4');
	mp_enqueue_script_theme('languages', $options['root_url'].'mp-includes/js/languages.php', false);
	mp_enqueue_script_theme('ready', $options['root_url'].'mp-includes/js/ready.js', array('jquery'));
	mp_get_simple_header(sprintf(__('%s | Log-In'),$options['site_name']));
 	echo "<script> var mp_admin_url = '{$options['admin_url']}'; </script>";
	echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-includes/css/reset.css">';
	echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-includes/css/basics.css">';
	echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-admin/css/admin.css">';
	echo '<link rel="stylesheet" href="'.$options['root_url'].'mp-admin/css/forms.css">';
    $logo_url = apply_filters('mp_logo_login',$options['root_url'].'mp-admin/images/mp-logo-wide.png');
    ?>
    <style>
    div#site-wrapper.login-form {
        width: 50%;
        margin: 45px 20%;
        padding-left:5%;
        padding-right: 5%;
        box-shadow:0 0 5px #CCC;
        -moz-box-shadow:0 0 5px #CCC;
        -webkit-box-shadow:0 0 5px #CCC;
    }
    div#site-wrapper.login-form form.mp-form {
        display: block !important;
    }
    div#site-wrapper.login-form form.mp-form label {
        padding: 15px 0 5px;
        display: block;
    }
    div#site-wrapper.login-form form.mp-form input#mp-login-submit {
        clear: both;
        float: right;
        margin: 15px 0 25px;
    }
    div#site-wrapper.login-form h4 {
        font-size: 18px;
        font-size:1.8rem;
        color: #069;
        width: 100%;
        padding-bottom: 10px;
        border-bottom: 1px dotted #CCC;
        text-align: center;
        margin-bottom: 25px;
    }
    header#logo {
        text-align: center;
    }
    header#logo a {
        display: inline-block;
        width: auto;
        height: auto;
        background: transparent;
        margin: 65px auto 15px -10px;
    }
    header#logo a img {
        float: left;
        width: auto;
        height: auto;
    }
    .button {
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
    }
    .button:hover {
        color: #666;
        background-color: #EEE;
        background-image: -moz-linear-gradient(100% 100% 90deg, #EEEEEE, #FFFFEE);
        background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FFFFEE), to(#EEEEEE));
        background-image: -o-linear-gradient(#EEEEEE,#FFFFEE);
    }
	input[type="text"], input[type="password"] {
		outline: none;
	}
	textarea, select {
		outline: none;
	}
	input:-webkit-autofill {
		color: red !important;
		background-color: transparent !important;
	}
    </style>
    <header id="logo"><a href="http://mongopress.org" <?php mp_attr_filter('index.php','a','http://mongopress.org','','',''); ?>><img src="<?php echo $logo_url; ?>" id="default-logo" <?php mp_attr_filter('index.php','img',$logo_url,'default-logo','',''); ?> /></a></header>
    <div id="site-wrapper" class="radius5 login-form">
        <h4><?php printf(__('Log-In to %s'),$options['site_name']); ?></h4>
        <?php mp_login_form(); ?>
    </div>
	<script>
	/* DIRTY HACKS FOR REMOVING UGLY YELLOW BOXES IN CHROME */
	if ($.browser.webkit) {
		$('input[name="mp[password]"]').attr('autocomplete', 'off');
	}
	if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
		$(window).load(function(){
			$('input:-webkit-autofill').each(function(){
				var text = $(this).val();
				var name = $(this).attr('name');
				$(this).after(this.outerHTML).remove();
				$('input[name=' + name + ']').val(text);
			});
		});
	}
	</script>
    <?php
    mp_get_footer();
}

function mp_get_admin_header(){
    global $mp_scripts_admin, $mp_styles_admin;
    $m = mongopress_load_m();
    $mp = mongopress_load_mp();
    $mongo = mongopress_load_perma();
    $perma = $mongo->current();
    $mp_options = $mp->options();
    $db = $m->$mp_options['db_name'];

    $logged_in = $mp->is_logged_in();

    $default_meta_charset = 'utf-8';
    $default_meta_description = 'mongopress';
    $default_meta_author = 'http://mongopress.org';
    $default_options = array(
        'meta_charset'      => apply_filters('mp_admin_meta_charset',$default_meta_charset,$perma),
        'meta_description'  => apply_filters('mp_admin_meta_description',$default_meta_description,$perma),
        'meta_author'       => apply_filters('mp_admin_meta_author',$default_meta_author,$perma)
    );
    $stylesheet_reset_dir = $mp_options['root_url'].'mp-includes/css/';
    $stylesheet_admin_dir = $mp_options['root_url'].'mp-admin/css/';
    $js_admin_dir = $mp_options['root_url'].'mp-admin/js/';
	
	// TODO custom urls!
    if($perma == $mp_options['admin_slug'].'/media'){
        $page_class_admin='';
        $page_class_media='current';
        $page_class_options = '';
    }elseif($perma == $mp_options['admin_slug'].'/options'){
        $page_class_admin='';
        $page_class_media='';
        $page_class_options = 'current';
    }else{
        $page_class_admin='current';
        $page_class_media='';
        $page_class_options = '';
    }

    //Please be carefull when playing with cookies
    if($logged_in){ $user_id = $GLOBALS['_MP']['COOKIE']['mp_user_id']; }else{ $user_id=''; }
    if($user_id != ''){
        mp_refresh_cookies();
	//save db connection. get data from cookie
	$user_name = $GLOBALS['_MP']['COOKIE']['mp_username'];
        ?>
        <!doctype html>
        <html class="<?php echo mp_html_class(); ?>" lang="<?php echo mp_html_lang(); ?>">
        <head>
            <title><?php _e('MongoPress Admin'); ?></title>
            <meta charset="<?php echo $default_options['meta_charset']; ?>">
            <meta name="description" content="<?php echo $default_options['meta_description']; ?>">
            <meta name="author" content="<?php echo $default_options['meta_author']; ?>">
            <meta id="viewport" name="viewport" content="initial-scale=1.0, user-scalable=1, minimum-scale=1.0, maximum-scale=2.0" />
            <!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
            <link rel="shortcut icon" href="<?php echo $mp_options['root_url']; ?>favicon.ico">
            <link rel="apple-touch-icon" href="<?php echo $mp_options['root_url']; ?>apple-touch-icon.png">
            <script src="<?php echo $mp_options['root_url']; ?>mp-includes/js/body.css.js"></script>
            <script>
			<?php if((strstr($_SERVER['HTTP_USER_AGENT'],'iPad'))||(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone'))){ ?>
			var is_ios = true;
			<?php }else{ ?>
			var is_ios = false;
			<?php } ?>
            var mp_root_url = '<?php echo $mp_options['full_url']; ?>';
			var mp_media_url = '<?php echo $mp_options['full_url'].$mp_options['media_slug'] ?>';
            var mp_admin_url = '<?php echo $mp_options['admin_url']; ?>';
            var mp_theme_url = '<?php echo $mp_options['full_url']; ?>mp-content/themes/<?php echo $mp_options['theme']; ?>/';
            </script>
            <!--[if lt IE 9]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script><![endif]-->
            <?php
            /* PRINT STYLES */
            $stylesheet_reset_dir = $mp_options['root_url'].'mp-includes/css/';
            $stylesheet_admin_dir = $mp_options['root_url'].'mp-admin/css/';
            mp_register_style('reset', $stylesheet_reset_dir.'reset.css', false, 1, 'screen');
			mp_register_style('aprint', $stylesheet_admin_dir.'aprint.css', array('reset'), 1, 'print');
            mp_register_style('admin', $stylesheet_admin_dir.'admin.css', array('reset'), 1, 'screen');
            mp_register_style('media', $stylesheet_admin_dir.'media.css', array('admin'), 1, 'screen');
            mp_register_style('forms', $stylesheet_admin_dir.'forms.css', array('reset'), 1, 'screen');
            mp_register_style('jqueryui', $stylesheet_admin_dir.'jqueryui.css', array('reset'), 1, 'screen');
            mp_register_style('jdashboard', $stylesheet_admin_dir.'jdashboard.css', array('reset'), 1, 'screen');
            mp_register_style('wysiwyg', $stylesheet_admin_dir.'wysiwyg.css', array('reset'), 1, 'screen');

			/* NEW ADMIN STYLES */
			mp_register_style('iadmin', $stylesheet_admin_dir.'iadmin.css', array('reset'), 1, 'screen');

			/* ADDED THESE DUE TO NOW USING AJAX TO LOAD PAGES */
			mp_enqueue_style_admin('datatables', $mp_options['root_url'].'mp-admin/css/tables.css', array('reset'), 1, 'screen');
			mp_enqueue_style_admin('media', $mp_options['root_url'].'mp-admin/css/media.css', array('reset'), 1, 'screen');

			/* FORCE THE LOADING OF PLUPLOAD FOR AJAX ADMIN */
			mp_enqueue_style_admin('plupload', $mp_options['root_url'].'mp-admin/js/plupload/css/jquery.plupload.queue.css', false, 1, 'screen');
			mp_enqueue_style_admin('jcrop', $mp_options['root_url'].'mp-admin/css/jcrop.css', false, 1, 'screen');

            $default_styles = array('reset','aprint','admin','iadmin','forms','jqueryui','jdashboard','wysiwyg', 'datatables', 'plupload', 'jcrop');
            if(is_array($mp_styles_admin)){
                $styles_to_load = array_merge($mp_styles_admin,$default_styles);
            }else{
                $styles_to_load = $default_styles;
            }
            $filtered_styles = apply_filters('mp_admin_css_to_load_header',$styles_to_load);
            // mp_print_styles($filtered_styles);
			
            /* PRINT SCRIPTS */
			if((strstr($_SERVER['HTTP_USER_AGENT'],'iPad'))||(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone'))){
				mp_register_script('ios', $mp_options['root_url'].'mp-includes/js/ios.js', false, 1);
				mp_register_script('iscroll', $js_admin_dir.'iscroll.js', array('jquery'), 1);
				mp_register_script('itouch', $js_admin_dir.'touch.js', array('jquery'), 1);
			}

            mp_register_script('languages', $mp_options['root_url'].'mp-includes/js/languages.php', false, 1);
            mp_register_script('jquery', $mp_options['root_url'].'mp-includes/js/jquery-1.6.4.min.js', array('languages'), '1.6.4');
            mp_register_script('ready', $js_admin_dir.'ready.js', array('jquery'), 1);
            mp_register_script('jwysiwyg', $js_admin_dir.'jquery.wysiwyg.js', array('ready'), 1);
            mp_register_script('jwysiwyglink', $js_admin_dir.'wysiwyg.link.js', array('jwysiwyg'), 1);
            mp_register_script('plugins', $js_admin_dir.'plugins.js', array('jquery'), 1);

			/* FORCE THE LOADING OF PLUPLOAD FOR AJAX ADMIN */
			mp_enqueue_script_admin('scroll', $mp_options['root_url'].'mp-includes/js/scroll.js', array('jquery'));
			mp_enqueue_script_admin('plupload', $mp_options['root_url'].'mp-admin/js/plupload/plupload.js', array('jquery'));
			mp_enqueue_script_admin('plupload_html4', $mp_options['root_url'].'mp-admin/js/plupload/plupload.html4.js', array('plupload'));
			mp_enqueue_script_admin('plupload_html5', $mp_options['root_url'].'mp-admin/js/plupload/plupload.html5.js', array('plupload'));
			mp_enqueue_script_admin('jcrop', $mp_options['root_url'].'mp-admin/js/jcrop.js', array('plupload'));
			mp_enqueue_script_admin('plupload_jquery_que', $mp_options['root_url'].'mp-admin/js/plupload/jquery.plupload.queue.js', array('plupload'));
			mp_enqueue_script_admin('plupload_mp', $mp_options['root_url'].'mp-admin/js/plupload/plupload_mp.js', array('plupload_jquery_que'));

			$default_scripts = array('ios','jquery','ready','iscroll','itouch','datatables','jwysiwyg','jwysiwyglink','jsinit','plugins','plupload','plupload_html4','plupload_html5','jcrop','plupload_jquery_que','plupload_mp');
            if(is_array($mp_scripts_admin)){
                $scripts_to_load = array_merge($mp_scripts_admin,$default_scripts);
            }else{
                $scripts_to_load = $default_scripts;
            }
            $filtered_scripts = apply_filters('mp_admin_js_to_load_header',$scripts_to_load);
            mp_print_scripts($filtered_scripts);
			ob_start();
			mp_print_styles($filtered_styles);
			$styles = ob_get_clean();
			/* THIS IS AN EXTREMELY SCARY WAY TO HANDLE MIN-WIDTHS AND STYLE-SHEETS - OR IS IT...? */
			/* AFTER ALL - THIS ENSURE THAT ONLY IF SCREEN IS BIG ENOUGH AND JS IS ACTIVATED WILL THE STYLES GET USED */
			/* TODO: TALK ABOUT THIS AND (OR) MAKE LESS FLAKEY */
            ?>
			<link rel="stylesheet" href="<?php echo $mp_options['root_url']; ?>mp-includes/css/handheld.css" type="text/css" media="handheld" />
			<link rel="stylesheet" href="<?php echo $mp_options['root_url']; ?>mp-includes/css/handheld.css" type="text/css" media="screen" />
			<script language="javascript" type="text/javascript">
			function mp_load_styles_via_js(){
				$('head').append("<?php echo str_replace(array("\n", "\r"), '', $styles); ?>");
			}
			</script>
			<?php /* END OF VERY SCRAY STUFF */ ?>
        </head>
        <body>
		<?php
    }else{
        ?>
        <!doctype html>
        <html class="" lang="en">
        <head><title><?php _e('You do not have permission to view this page!'); ?></title>
        <style>
        body {
            display: block;
            margin: 0 auto;
            max-width: 1880px;
            min-width: 480px;
            background: none repeat scroll 0 0 #F3F4EB;
            padding: 0 20%;
            width: 60%;
            font-size: 10px;
            font-size: 1rem;
            font-family:Sans-serif;
        }
        a {
            text-decoration: none;
            color: #069;
        }
        a:hover {
            color: #757575;
            text-decoration: underline;
        }
        div#site-wrapper {
            background: none repeat scroll 0 0 #FFFFFF;
            border: 1px solid #CCCCCC;
            display: block;
            margin: 25px auto;
            padding: 25px;
            width: auto;
        }
        .radius5 {
            -moz-border-radius: 5px 5px 5px 5px;
        }
        div#site-wrapper.login-form {
            width: 50%;
            margin: 45px 20%;
            padding-left:5%;
            padding-right: 5%;
            box-shadow:0 0 5px #CCC;
            -moz-box-shadow:0 0 5px #CCC;
            -webkit-box-shadow:0 0 5px #CCC;
        }
        div#site-wrapper.login-form form.mp-form {
            display: block !important;
        }
        div#site-wrapper.login-form form.mp-form label {
            padding: 15px 0 5px;
            display: block;
        }
        div#site-wrapper.login-form form.mp-form input#mp-login-submit {
            clear: both;
            float: right;
            margin: 15px 0 25px;
        }
        div#site-wrapper.login-form h4 {
            font-size: 18px;
            font-size:1.8rem;
            color: #069;
            width: 100%;
            padding-bottom: 10px;
            border-bottom: 1px dotted #CCC;
            text-align: center;
            margin: 0 0 25px;
        }
        </style></head><body>
        <div id="site-wrapper" class="radius5 login-form">
            <h4><?php _e('MONGOPRESS PRIVACY'); ?></h4>
            <p style="text-align:center;"><?php _e('You do not have permission to view this page!'); ?></p>
            <p style="text-align:center;"><a href="<?php echo $mp_options['root_url']; ?>" <?php mp_attr_filter('admin.php','a','','',''); ?>><?php _e('RETURN TO HOMEPAGE'); ?></a></p>
        </div>
        </body></html>
        <?php
        die();
    }
    ?>
    <?php
}

function mp_get_admin_footer(){
    global $mp_scripts_admin, $mp_styles_admin;
    $mp = mongopress_load_mp();
    $mp_options = $mp->options();
    $js_admin_dir = $mp_options['root_url'].'mp-admin/js/';
    do_action('mp_before_admin_footer');
    /* PRINT STYLES */
    $filtered_styles = apply_filters('mp_admin_css_to_load_footer',$mp_styles_admin);
    mp_print_styles($filtered_styles);
    /* PRINT SCRIPTS */
	mp_register_script('readyjs', $js_admin_dir.'ready.js', false, 1);
	mp_register_script('iadminjs', $js_admin_dir.'iadmin.js', array('readyjs'), 1);
	mp_register_script('jsinit', $js_admin_dir.'jsinit.js', array('iadminjs'), 1);
	/* ADDED THESE DUE TO NOW USING AJAX TO LOAD PAGES */
    mp_enqueue_script_admin('datatables', $mp_options['root_url'].'mp-admin/js/dataTables.js', array('jquery'), 1);
	mp_enqueue_script_admin('functions', $mp_options['root_url'].'mp-includes/js/functions.js', array('jquery'), 1);
	mp_enqueue_script_admin('options', $mp_options['root_url'].'mp-admin/js/options.js', array('jquery'));
	/* END OF NEWLY MANUALLY ADDED ENQUES */
    $default_scripts = array('ready','iadminjs','jsinit','datatables','options');
    if(isset($mp_scripts_admin)){ 
        if(is_array($mp_scripts_admin)){ 
            $scripts_to_load = array_merge($mp_scripts_admin,$default_scripts);
        }else{
            $scripts_to_load = $default_scripts;
        }
    }else{
        $scripts_to_load = $default_scripts;
    }
    $filtered_scripts = apply_filters('mp_admin_js_to_load_footer',$scripts_to_load);
    mp_print_scripts($filtered_scripts);
    do_action('mp_after_admin_footer');
    echo '</body>';
    echo '</html>';
}

function mp_initialize_jwysiwyg(){
    $mp = mongopress_load_mp();
    $mp_options = $mp->options();
    ?>
    <script type="text/javascript" src="<?php echo $mp_options['root_url']; ?>mp-admin/js/ready.js"></script>
    <script type="text/javascript" src="<?php echo $mp_options['root_url']; ?>mp-admin/js/jquery.wysiwyg.js"></script>
    <script type="text/javascript" src="<?php echo $mp_options['root_url']; ?>mp-admin/js/wysiwyg.link.js"></script>
    <script>
        jQuery(document).ready(function(){
            init_jwysiwyg();
            initForms();
        });
    </script>
    <?php
}

function mp_object_type_dropdown($allow_new_types=false){
    $mp = mongopress_load_mp();
    $object_types = $mp->types();
    if(!empty($object_types)){
        if($allow_new_types){
            echo '<a href="#" id="mp-add-object-type" class="button mini" data-form-id="object" '.mp_get_attr_filter('admin.php','a','#','mp-add-object-type','button mini').'>'.__('New Type').'</a>';
        }
        echo '<div class="select_wrapper object_control">';
            echo '<select id="type" name="mp[type]">';
            foreach($object_types as $type){
                echo '<option value="'.$type.'">'.$type.'</option>';
            }
            echo '</select>';
        echo '</div>';
    }else{
        echo '<span class="input-wrapper radius5"><input id="type" name="mp[type]" placeholder="'.__('Define your object type').'" class="blanked" autocomplete="off" /></span>';
    }
}

function mp_objects_dropdown($id='mp-objects',$name='mp[objects]',$class=false,$extra_attributes=false,$selected_id=false,$filter_by=false){
    $m = mongopress_load_m();
    $mp = mongopress_load_mp();
    $default_options = $mp->options();
    $db = $m->$default_options['db_name'];
    $objs = $db->$default_options['obj_col'];
    if($filter_by){
        $objects = $mp->arrayed($objs->find(array("type"=>$filter_by)));
    }else{
        $objects = $mp->arrayed($objs->find());
    }
    if(is_array($objects)){
        echo '<div class="select_wrapper">';
            echo '<select id="'.$id.'" name="'.$name.'" class="'.$class.'" '.$extra_attributes.'>';
            echo '<option value="">'.__('-- None --').'</option>';
            foreach($objects as $object){
                if($selected_id==$mp->get_mongoid_as_string($object["_id"])){
                    echo '<option value="'.$mp->get_mongoid_as_string($object["_id"]).'" selected="selected">'.$object['title'].'</option>';
                }else{
                    echo '<option value="'.$mp->get_mongoid_as_string($object["_id"]).'">'.$object['title'].'</option>';
                }
            }
            echo '</select>';
        echo '</div>';
    }else{
        echo '<span class="input-wrapper radius5">';
            echo '<input class="blanked" id="'.$id.'" name="'.$name.'" value="'.sprintf(__('The database does not contain any \'%s\' objects'), $filter_by).'" readonly="readonly" />';
        echo '</span>';
    }
}

function mp_media_dropdown($id='mp-objects',$name='mp[objects]',$class=false,$extra_attributes=false,$selected_id=false,$filter_by=false){
    $m = mongopress_load_m();
    $mp = mongopress_load_mp();
    $options = $mp->options();
    $db = $m->$options['db_name'];
    $all_media = $mp->arrayed($db->fs->files->find());
    if(isset($all_media)){
        echo '<div class="select_wrapper">';
            echo '<select id="'.$id.'" name="'.$name.'" class="'.$class.'" '.$extra_attributes.'>';
            echo '<option value="">'.__('-- None --').'</option>';
            foreach($all_media as $media){
                if($filter_by){
                    if($filter_by=='download'){
                        if(($media['type']=='application/download')||($media['type']=='application/x-zip')||($media['type']=='application/octet-stream')||($media['type']=='application/zip')){
                            if($selected_id==$mp->get_mongoid_as_string($media["_id"])){
                                echo '<option value="'.$mp->get_mongoid_as_string($media["_id"]).'" selected="selected">'.$media['filename'].'</option>';
                            }else{
                                echo '<option value="'.$mp->get_mongoid_as_string($media["_id"]).'">'.$media['filename'].'</option>';
                            }
                        }
                    }elseif($filter_by=='image'){
                        if(($media['type']=='image/png')||($media['type']=='image/gif')||($media['type']=='image/jpg')||($media['type']=='image/jpeg')){
                            if($selected_id==$mp->get_mongoid_as_string($media["_id"])){
                                echo '<option value="'.$mp->get_mongoid_as_string($media["_id"]).'" selected="selected">'.$media['filename'].'</option>';
                            }else{
                                echo '<option value="'.$mp->get_mongoid_as_string($media["_id"]).'">'.$media['filename'].'</option>';
                            }
                        }
                    }else{
                        if($selected_id==$mp->get_mongoid_as_string($media["_id"])){
                            echo '<option value="'.$mp->get_mongoid_as_string($media["_id"]).'" selected="selected">'.$media['filename'].'</option>';
                        }else{
                            echo '<option value="'.$mp->get_mongoid_as_string($media["_id"]).'">'.$media['filename'].'</option>';
                        }
                    }
                }else{
                    if($selected_id==$mp->get_mongoid_as_string($media["_id"])){
                        echo '<option value="'.$mp->get_mongoid_as_string($media["_id"]).'" selected="selected">'.$media['filename'].'</option>';
                    }else{
                        echo '<option value="'.$mp->get_mongoid_as_string($media["_id"]).'">'.$media['filename'].'</option>';
                    }
                }
            }
            echo '</select>';
        echo '</div>';
    }else{
        echo '<span class="input-wrapper radius5">';
            echo '<input class="blanked" id="'.$id.'" name="'.$name.'" value="'.__('The selected database does not yet contain any media').'" readonly="readonly" />';
        echo '</span>';
    }
}

function mp_custom_field_row($i=1,$this_key=false,$this_value=false){
    ?>
    <div id="custom-row-<?php echo $i; ?>" class="mp-custom-field-wrapper" data-i="<?php echo $i; ?>">
        <div class="custom-left">
            <label for="cfk<?php echo $i; ?>"><?php _e('Custom Key:'); ?></label>
            <span class="input-wrapper radius5"><input id="cfk<?php echo $i; ?>" name="mp[custom][cfk<?php echo $i; ?>]" placeholder="<?php _e('the key for the custom field'); ?>" class="blanked" autocomplete="off" value="<?php echo $this_key; ?>" /></span>
        </div>
        <div class="custom-right">
            <label for="cfv<?php echo $i; ?>"><?php _e('Custom Value:'); ?></label>
            <span class="input-wrapper radius5"><input id="cfv<?php echo $i; ?>" name="mp[custom][cfv<?php echo $i; ?>]" placeholder="<?php _e('the value of the custom field'); ?>" class="blanked" autocomplete="off" value="<?php echo $this_value; ?>" /></span>
        </div>
        <div class="custom-actions">
            <label for="remove-field-<?php echo $i; ?>"><?php _e('Actions:'); ?></label>
            <a href="#" class="button mini remove-fields" id="remove-field-<?php echo $i; ?>" data-i="<?php  echo $i; ?>" <?php mp_attr_filter('admin.php','a','#','remove-field-'.$i,'button mini remove-fields'); ?>><?php _e('Remove'); ?></a>
        </div>
    </div>
    <?php
}

function mp_add_object_form($settings=false){
    $default_options = array(
        'force_css'                 => false,
        'force_js'                  => false,
        'object_type_dropdown'      => false,
        'allow_new_object_types'    => false,
        'allow_custom_fields'       => false
    );
    if(is_array($settings)){
        $options = array_merge($default_options,$settings);
    }else{
        $options = $settings;
    }
    if(($options['force_css'])||($options['force_js'])){
        $mp = mongopress_load_mp();
        $mp_options = $mp->options();
    } if($options['force_css']){
        ?>
        <link rel="stylesheet" href="<?php echo $mp_options['root_url']; ?>mp-admin/css/forms.css">
        <link rel="stylesheet" href="<?php echo $mp_options['root_url']; ?>mp-admin/css/wysiwyg.css">
        <?php
    } if($options['force_js']){
        add_action('mp_footer','mp_initialize_jwysiwyg');
    }
    ?>
    <form id="object" class="mp-form">
        <input id="id" name="mp[id]" type="hidden" autocomplete="off" />
        <input id="slug_id" name="mp[slug_id]" type="hidden" autocomplete="off" />
        <input id="mongo_id" name="mp[mongo_id]" type="hidden" autocomplete="off" />
        <span class="notification"><?php _e('Add &amp Edit Objects:'); ?></span>
        <label for="title"><?php _e('Object Title:'); ?></label>
        <span class="input-wrapper radius5"><input id="title" name="mp[title]" placeholder="<?php _e('Give your object a title'); ?>" class="blanked" autocomplete="off" /></span>
        <?php if($options['object_type_dropdown']){ ?>
            <div id="mp-object-compact">
                <label for="type"><?php _e('Object Type:'); ?></label>
                <?php mp_object_type_dropdown($options['allow_new_object_types']); ?>
                <div id="mp-object-type-wrapper" style="display:none; padding-top:15px;" class="closed">
                    <label for="type"><?php _e('New Object Type:'); ?></label>
                    <span class="input-wrapper radius5"><input id="type" name="mp[type]" placeholder="<?php _e('Select an object type'); ?>" class="blanked" autocomplete="off" /></span>
                </div>
                <a id="mp-object-slug-switcher" class="button mini" href="#" style="display:none;clear:both;" <?php mp_attr_filter('admin.php','a','#','mp-object-slug-switcher','button mini'); ?>><?php _e('Edit Slug'); ?></a>
                <label for="slug" id="object-slug-label" style="display:none"><?php _e('Object Slug:'); ?></label>
                <div id="temporary-slug" style="display:none"></div>
            </div>
            <div id="mp-object-slug-wrapper" style="display:none">
                <span class="input-wrapper radius5"><input id="slug" name="mp[slug]" placeholder="<?php _e('Give your object a slug'); ?>" class="blanked" autocomplete="off" /></span>
            </div>
        <?php }else{ ?>
            <label for="type"><?php _e('Object Type:'); ?></label>
            <span class="input-wrapper radius5"><input id="type" name="mp[type]" placeholder="<?php _e('Select an object type'); ?>" class="blanked" autocomplete="off" /></span>
            <label for="slug"><?php _e('Object Slug:'); ?></label>
            <span class="input-wrapper radius5"><input id="slug" name="mp[slug]" placeholder="<?php _e('Give your object a slug'); ?>" class="blanked" autocomplete="off" /></span>
        <?php } ?>
        <label for="content"><?php _e('Object Content:'); ?> <span class="mp-data-view"><a href="#" class="switch-to-publish button mini" <?php mp_attr_filter('admin.php','a','#','','switch-to-publish button mini'); ?>><?php _e('Switch to WYSIWYG Mode'); ?></a></span><span class="mp-publish-view" style="display:none;"><a href="#" class="switch-to-data button mini" <?php mp_attr_filter('admin.php','a','#','','switch-to-data button mini'); ?>><?php _e('Switch to HTML Mode'); ?></a></span></label>
        <span class="input-wrapper input-wrapper-area radius5"><textarea id="content" name="mp[content]" class="blanked" autocomplete="off" placeholder="<?php _e('Give your object contents'); ?>"></textarea></span>
        <?php if($options['allow_custom_fields']){ ?>
            <div id="mp-object-custom-wrapper" style="display:none" data-i="0">
                <?php mp_custom_field_row(); ?>
            </div>
            <a id="mp-object-toggle-custom-fields" class="button mini" href="#" <?php mp_attr_filter('admin.php','a','#','mp-object-toggle-custom-fields','button mini','style="float:left;margin:0"'); ?>><?php _e('Custom Fields'); ?></a>
            <a id="mp-object-add-custom-field" class="button mini hidden" href="#" <?php mp_attr_filter('admin.php','a','#','mp-object-add-custom-field','button mini hidden','style="float:left;margin:0 0 0 10px"'); ?>><?php _e('Add Another Field'); ?></a>
        <?php } ?>
        <input type="button" class="button hidden" data-button-type="add-new-object" data-form="object" value="<?php _e('ADD NEW OBJECT'); ?>" style="clear:both;" />
        <span id="mp-object-form-lower-notification" class="notification"><?php _e('Add &amp Edit Objects:'); ?></span>
        <input type="submit" id="submit" value="<?php _e('SAVE OBJECT'); ?>" />
        <?php mp_nonce_field('object','mp_nonce'); ?>
    </form>
	<div style="clear:both; display: block;"></div>
    <?php
}

/**
 * mingo_meantime()
 *
 * Based on bp_core_time_since(), which was based on function created by Dunstan Orchard - http://1976design.com
 *
 * This function will return an English representation of the time elapsed
 * since a given date.
 * eg: 2 hours and 50 minutes
 * eg: 4 days
 * eg: 4 weeks and 6 days
 *
 * @package BuddyPress Core
 * @param $older_date int Unix timestamp of date you want to calculate the time since for
 * @param $newer_date int Unix timestamp of date to compare older date to. Default false (current time).
 * @return str The time since.
 */
function mingo_meantime($first_datetime=false, $second_datetime = false, $format = false ){
	if (!$format) return mp_core_time_since($first_datetime, $second_datetime);
	if ($format == 'date') return mp_core_date();
	if ($format == 'time') return mp_core_time();
	if ($format == 'datetime') return mp_core_datetime();
	return date($format,$first_datetime);
}

function mp_core_datetime($time=false, $offset = 0) {
	// offset = secs to offset by - eg. 60 * 60 * 24 * 7 == one week.
	$default_format = __('l jS \of F Y h:i:s A');
	$format = apply_filters('mp_core_datetime_format',$default_format);
	if (!$time) $time = time(); // now	
	return date($format,$time);
}
function mp_core_date($time=false, $offset = 0) {
	// offset = secs to offset by.
	$default_format = __('l jS \of F Y');
	$format = apply_filters('mp_core_date_format',$default_format);
	if (! $time) $time = time(); // now	
	return date($format,$time);
}
function mp_core_time($time=false, $offset = 0) {
	// offset = secs to offset by.
	$default_format = __('h:i:s A');
	$format == apply_filters('mp_core_time_format',$default_format);
	if (! $time) $time = time(); // now	
	return date($format,$time);
}

function mp_core_time_since($older_date=false,$newer_date = false) {
    if(!$newer_date){
        $newer_date = time();
    }
    if(!$older_date){
        $older_date = time();
    }
    // array of time period chunks
    $chunks = array(
        array( 60 * 60 * 24 * 365 , __( 'year'), __( 'years')),
        array( 60 * 60 * 24 * 30 , __( 'month'), __( 'months')),
        array( 60 * 60 * 24 * 7, __( 'week'), __( 'weeks')),
        array( 60 * 60 * 24 , __( 'day'), __( 'days')),
        array( 60 * 60 , __( 'hour'), __( 'hours')),
        array( 60 , __( 'minute'), __( 'minutes')),
        array( 1, __( 'second'), __( 'seconds'))
    );
    if ( !is_numeric( $older_date ) ) {
        $time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
        $date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
        $older_date = gmmktime( (int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0] );
    }
    /* $newer_date will equal false if we want to know the time elapsed between a date and the current time */
    /* $newer_date will have a value if we want to work out time elapsed between two known dates */
    // NOT NEEDED DUE TO FIRST LINES -> $newer_date = ( !$newer_date ) ? strtotime( bp_core_current_time() ) : $newer_date;
    /* Difference in seconds */
    $since = $newer_date - $older_date;
    /* Something went wrong with date calculation and we ended up with a negative date. */
    if(0>$since){
        return __( 'sometime');
    }
    /* Step one: the first chunk */
    for ( $i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        /* Finding the biggest chunk (if the chunk fits, break) */
        if(($count = floor($since / $seconds))!= 0){
            break;
        }
    }
    /* Set output var */
    if((isset($chunks[$i][1]))&&($chunks[$i][2])){
        $output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];
    }else{
        $output = ( 1 == $count ) ? '1 ' : $count;
    }
    /* Step two: the second chunk */
    if ( $i + 2 < $j ) {
        $seconds2 = $chunks[$i + 1][0];
        if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
            /* Add to output var */
            $output .= ( 1 == $count2 ) ? _x( ',', 'Separator in time since' ) . ' 1 '. $chunks[$i + 1][1] : _x( ',', 'Separator in time since' ) . ' ' . $count2 . ' ' . $chunks[$i + 1][2];
        }
    }
    if(!(int)trim($output)){
        $output = '0 ' . __( 'seconds' );
    }
    return $output;
}
function mp_get_login_form(){
    ob_start();
    mp_login_form('display:block;');
    $login_form = ob_get_clean();
    return $login_form;
}
function mp_login_form($style='display:none'){
    $mp = mongopress_load_mp();
	
    $options = $mp->options();
    $logged_in = $mp->is_logged_in();

    if($logged_in){
        $current_user = $mp->get_current_user();
        $user_name = $current_user['name'];
        $logout_link = '<a href="'.$options['admin_url'].'logout.php" '.mp_get_attr_filter('admin.php','a','mp-admin/logout.php','','').'>'.__('here').'</a>';
        $admin_link = '<a href="'.$options['admin_url'].'" '.mp_get_attr_filter('admin.php','a','mp-admin/','','').'>'.__('admin dashboard').'</a>';
        ?>
        <form id="mp-login-form" style="display:none" class="mp-form">
        <?php
            do_action('mp_login_form_start',$logged_in);
            ?>
            <span class="notification">
                <p><?php printf(__('You are currently logged-in as %s. Please click %s to log-out, or use your %s as required.'), $user_name, $logout_link, $admin_link); ?></p>
            </span>
            <?php
            do_action('mp_login_form_end',$logged_in);
        ?>
        </form>
        <?php
    }else{
        ?>
        <form id="mp-login-form" style="<?php echo $style;?>" class="mp-form">
            <?php do_action('mp_login_form_start',$logged_in); ?>
            <label for="mp-login-username"><?php _e('Username'); ?>:</label>
            <span class="input-wrapper radius5">
                <input id="mp-login-username" required placeholder="<?php _e('Your unique display name'); ?>" class="blanked" name="mp[username]" /></span>
            <label for="mp-login-password"><?php _e('Password'); ?>:</label>
            <span class="input-wrapper radius5">
                <input id="mp-login-password" required placeholder="<?php _e('Do you have the rights to access?'); ?>" class="blanked" type="password" name="mp[password]" />
            </span>
            <?php do_action('mp_login_form_end',$logged_in); ?>
            <input type="submit" id="mp-login-submit" class="button" value="<?php _e('Login'); ?>" />
            <?php mp_nonce_field('mp-login-form','mp_nonce',true,true,'public'); ?>
            <br style="clear:both; display:block" />
        </form>
        <?php
    }
}

/* TODO: NEED TO TURN THESE OPTION VARS INTO ARG ARRAY */
function mp_get_media($nonce=false,$id=false,$type='image',$file_name=false,$css_id=false,$css_class=false,$start_count=false,$use_pretty_media=false){
    if(($id)&&($nonce)){
        $mp = mongopress_load_mp();
        $options = $mp->options();
        if($type=='image'){
            if($use_pretty_media){
				echo '<img src="'.$options['media_url'].$file_name.'" id="'.$css_id.'" class="'.$css_class.'" '.mp_get_attr_filter('admin.php','img',$options['media_url'].$file_name,$css_id,$css_class,'data-slug="'.$file_name.'"').' />';
			}else{
				echo '<img src="'.$options['root_url'].'mp-includes/mp-images.php?id='.$id.'&nonce='.$nonce.'" id="'.$css_id.'" class="'.$css_class.'" '.mp_get_attr_filter('admin.php','img','mp-includes/mp-images.php?id='.$id.'&nonce='.$nonce,$css_id,$css_class,'data-slug="'.$file_name.'"').' />';
			}
        }elseif($type=='download'){
            echo '<a href="'.$options['root_url'].'mp-includes/mp-downloads.php?id='.$id.'&nonce='.$nonce.'&start='.$start_count.'" id="'.$css_id.'" class="'.$css_class.'" '.mp_get_attr_filter('admin.php','a','mp-includes/mp-downloads.php?id='.$id.'&nonce='.$nonce.'&start='.$start_count,$css_id,$css_class,'target="_blank"').'>'.$file_name.'</a>';
        }elseif($type=='url'){
            return $options['root_url'].'mp-includes/mp-downloads.php?id='.$id.'&nonce='.$nonce.'&start='.$start_count;
        }else{
            echo '<p>'.__('Unsupported Media Type').'</p>';
        }
    }
}

function mp_display_media($mp_media_actual_name,$media_type){
    $m = mongopress_load_m();
    $mp = mongopress_load_mp();
    $options = $mp->options();
    $db = $m->$options['db_name'];
    $grid = $db->getGridFS();
    $gridded_media = $mp->arrayed($db->fs->files->find(array("actual"=>$mp_media_actual_name)));
    if(is_array($gridded_media)){
        $mp_media_id = $mp->get_mongoid_as_string($gridded_media[0]["_id"]); // Need to get based on filename...
        $mp_media_mongo_id = new MongoId($mp_media_id);
        $file_name = $gridded_media[0]['filename'];
        //$nonce = mp_create_nonce('media_view');
        //mp_get_media($nonce,$mp_media_id,$media_type,$file_name);
        $image = $grid->findOne(
            array("_id" => $mp_media_mongo_id)
        );
		$expires_in = 3600;
		$expires_in = apply_filters('theme_header_expires_in',$expires_in);
		header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $expires_in));
        header('Content-type: '.$image->file['type']);
        echo $image->getBytes();
        if($image->file['downloads']<1){
            $grid->update(array("_id" => $mp_media_mongo_id), array('$inc' => array("views" => 1)));
        }else{
            $grid->update(array("_id" => $mp_media_mongo_id), array('$inc' => array("downloads" => 1)));
        }
    }else{
        /* REDIRECT TO HOMEPAGE ...? */
        header("Status: 404 Not Found");
        _e('Unidentified Object in Imperial Vortex!!!');
    }
}

function mp_add_media_form(){
    $m = mongopress_load_m();
    $mp = mongopress_load_mp();
    $mp_options = $mp->options();
    $db = $m->$mp_options['db_name'];
    echo '<form id="media" class="mp-form" method="post" enctype="multipart/form-data">';
    if('POST' == $_SERVER['REQUEST_METHOD']){
        $files = $_FILES;
        if(isset($_POST['mp_nonce'])){ $nonce = $_POST['mp_nonce']; }else{ $nonce=false; }
        if(isset($_POST['mp_media_title'])){ $title = $_POST['mp_media_title']; }else{ $title = false; }
        if(isset($_POST['mongo_id'])){ $mongo_id = $_POST['mongo_id']; }else{ $mongo_id = false; }
        if((!mp_verify_nonce($nonce,'media_upload'))&&(!empty($nonce))){
            _e('Unidentified Object in The Imperial Vortex!!!');
        }else{
            $limit_size = 1000000;
            $mb_limit = $limit_size/1000000;
            if(empty($mongo_id)){
                if(!empty($files)) {
                    foreach($files as $file) {
                        $media_object = $file;
                        if($title){
                            $filename = $title;
                            $actual_name = sanitize_title_with_dashes($file['name'],false);
                        }else{
                            $filename = sanitize_title_with_dashes($file['name'],false);
                            $actual_name = $filename;
                        }
                        $media_meta = array(
                            'filename'  => $filename,
                            'actual'    => $actual_name,
                            'type'      => $file['type'],
                            'downloads' => 0,
                            'views'     => 0
                        );
                        if($file["size"] > 1) {
                            if(($file['type']=='image/png')||($file['type']=='image/gif')||($file['type']=='application/download')||($file['type']=='application/x-zip')||($file['type']=='application/octet-stream')||($file['type']=='application/zip')||($file['type']=='image/jpg')||($file['type']=='image/jpeg')){
                                $grid = $db->getGridFS();
                                $image = $grid->findOne(
                                    array("filename" => $filename)
                                );
                                $display_error = false;
                                if(isset($image->file['length'])){
                                    if($image->file['length']==$file['size']){
                                        $display_error = true;
                                    }
                                } if($display_error){
                                    echo '<span class="notification error"><p>'.__('This File Already Exists').'</p></span>';
                                }else{
                                    $mp_media_object_id = $grid->put($media_object['tmp_name'],$media_meta);
                                    if(!empty($mp_media_object_id)){
                                        echo '<span class="notification success"><p>'.__('Successfully Uploaded Media').'</p></span>';
                                    }else{
                                        echo '<span class="notification error"><p>'.__('Unknown Error Uploading Media').'</p></span>';
                                    }
                                }
                            }else{
                                echo '<span class="notification error"><p>'.__('Unsupported Media Type').'</p></span>';
                            }
                        }
                    }
                }
            }else{
                if($title){
                    $filename = $title;
                    if(isset($file['name'])){
                        $actual_name = sanitize_title_with_dashes($file['name'],false);
                    }
                }else{
                    if(isset($file['name'])){
                        $filename = $file['name'];
                        $actual_name = sanitize_title_with_dashes($filename,false);
                    }
                }
                $mongo_id = new MongoID($mongo_id);
                $grid = $db->getGridFS();
                if(!empty($filename)){
                    $updated = $grid->update(array("_id" => $mongo_id), array('$set'=>array('filename' => $filename)));
                    if($updated){
                        echo '<span class="notification success"><p>'.__('Successfully Updated Media').'</p></span>';
                        /* NOW NEED TO UPDATE THE GALLERY */
                        ?>
                        <script type="text/javascript">
                            var this_download = document.getElementById('download_filename_<?php echo $mongo_id; ?>');
                            var this_image = document.getElementById('image_filename_<?php echo $mongo_id; ?>');
                            if(this_download){
                                this_download.innerHTML = '<?php printf(__('Download Link:').'<br /> %s', $filename); ?>';
                            }
                            if(this_image){
                                this_image.innerHTML = '<?php echo $filename; ?>';
                            }
                        </script>
                        <?php
                    }else{
                        echo '<span class="notification error"><p>'.__('Error Updating Media').'</p></span>';
                    }
                }else{
                    echo '<span class="notification error"><p>'.__('Cannot Rename File to Empty String').'</p></span>';
                }
            }
        }
    }
    ?>
    <input type="hidden" id="mongo_id" name="mongo_id" value="" />
    <label for="mp_media_title"><?php _e('Title of Media'); ?></label>
    <span class="input-wrapper radius5"><input id="mp_media_title" name="mp_media_title" class="blanked" value="" automcomplete="off" /></span>
    <label for="mp_file"><?php _e('File to Upload'); ?></label>
    <span class="input-wrapper radius5 container"><input type="file" name="mp_file" id="mp_file" onchange="this.form.mp_file_fake.value = this.value;" class="really blanked" size="999" /></span>
    <input name="mp_file_fake" id="mp_file_fake" onchange="" class="blanked" value="" automcomplete="off" />
    <input type="submit" class="button" id="media_submit" value="<?php _e('Upload Media'); ?>" />
    <?php mp_nonce_field('media_upload','mp_nonce'); ?>
    <?php
    echo '</form>';
	echo '<div style="clear:both; display: block;"></div>';
}

function mp_misc_options_form($public=false){
    $mp = mongopress_load_mp(); $options = $mp->options();
    $site_options = $mp->mp_site_options();
    if(is_array($site_options)){
        if(isset($site_options['site_name'])){ $site_name = $site_options['site_name']; }else{ $site_name = false; }
        if(isset($site_options['site_description'])){ $site_description = $site_options['site_description']; }else{ $site_description = false; }
        if(isset($site_options['cookie_ttl'])){ $cookie_ttl = $site_options['cookie_ttl']; }else{ $cookie_ttl = false; }
    }else{
        $site_name = false;
        $site_description = false;
        $cookie_ttl = false;
    }
    echo '<p style="padding:15px 0 25px; text-align:center;">'.__('More options coming soon, but for now, you can edit the following:').'</p>';
    mp_enqueue_style_admin('forms', $options['root_url'].'mp-admin/css/forms.css');
    ?>
    <form id="misc-options" class="mp-form">
        <div style="display:inline-block; width: 48%;">
            <label for="mp_site_name"><?php _e('Site Name:'); ?></label>
            <span class="input-wrapper radius5">
                <input class="blanked" id="mp_site_name" name="mp_site_name" placeholder="<?php _e('Override your config settings by changing site name here'); ?>" value="<?php echo $site_name; ?>" />
            </span>
        </div>
        <div style="display:inline-block; width: 3%;"></div>
        <div style="display:inline-block; width: 47%; float:right;">
            <label for="mp_site_description"><?php _e('Site Description:'); ?></label>
            <span class="input-wrapper radius5">
                <input class="blanked" id="mp_site_description" name="mp_site_description" placeholder="<?php _e('Override your config settings by changing site description here'); ?>" value="<?php echo $site_description; ?>" />
            </span>
        </div>
        <div style="display:inline-block; width: 48%;">
            <label for="mp_cookie_ttl"><?php _e('Cookie TTL:'); ?></label>
            <span class="input-wrapper radius5">
                <input class="blanked" id="mp_cookie_ttl" name="mp_cookie_ttl" placeholder="<?php _e('Override your cookie time to live settings by changing type here - must be INT or word session'); ?>" value="<?php echo $cookie_ttl; ?>" />
            </span>
        </div>
        <input type="submit" id="submit_misc_options" class="button" value="<?php _e('Save Options'); ?>" style="float:right;" />
        <?php
		if($public){
			mp_nonce_field('plugin-options','mp_nonce','public'); 
		}else{
			mp_nonce_field('plugin-options','mp_nonce');
		}
		?>
    </form>
    <?php
}

function mp_user_options_form(){
    $mp = mongopress_load_mp(); $options = $mp->options();
	$current_user_info = $mp->get_current_user();
	$user_id = $mp->get_mongoid_as_string($current_user_info["_id"]);
    $users_options = $mp->mp_user_options($user_id);
	$user_options = $users_options[0];
    if(is_array($user_options)){
        if(isset($user_options['email'])){ $email = $user_options['email']; }else{ $email = false; }
        if(isset($user_options['name'])){ $name = $user_options['name']; }else{ $name = false; }
    }else{
        $email = false;
        $name = false;
    }
    echo '<p style="padding:15px 0 25px; text-align:center;">'.__('More options coming soon, but for now, you can edit the following:').'</p>';
    mp_enqueue_style_admin('forms', $options['root_url'].'mp-admin/css/forms.css');
    ?>
    <form id="user-options" class="mp-form">
        <div style="display:inline-block; width: 48%;">
            <label for="mp_email"><?php _e('Your Email:'); ?></label>
            <span class="input-wrapper radius5">
                <input class="blanked" id="mp_email" name="mp_email" placeholder="<?php _e('Currently used for fall-back Gravatars'); ?>" value="<?php echo $email; ?>" />
            </span>
        </div>
        <div style="display:inline-block; width: 3%;"></div>
        <div style="display:inline-block; width: 47%; float:right;">
            <label for="mp_name"><?php _e('Your Name:'); ?></label>
            <span class="input-wrapper radius5">
                <input class="blanked" id="mp_name" name="mp_name" placeholder="<?php _e('Used throughout the site'); ?>" value="<?php echo $name; ?>" />
            </span>
        </div>
		<input type="hidden" id="mp_user_id" name="mp_user_id" value="<?php echo $user_id; ?>" />
        <input type="submit" id="submit_user_options" class="button" value="<?php _e('Save Options'); ?>" style="float:right;" />
        <?php mp_nonce_field('user-options','mp_nonce'); ?>
		<div style="clear:both"></div>
    </form>
    <?php
}

function mp_objects_table($options=false){
    $mp = mongopress_load_mp();
    $mp_options = $mp->options();
    $m = mongopress_load_m();
    /* TODO: Make This an ARG */
    $allow_edits = false;
    if(isset($options)){ if(is_array($options)){ extract($options); }}
    /* END OF TODO: */
    $db = $m->$mp_options['db_name'];
    $objs = $db->$mp_options['obj_col'];
    $total_object_count = $objs->find()->count();
    mp_enqueue_style_admin('datatables', $mp_options['root_url'].'mp-admin/css/tables.css');
    mp_enqueue_style_theme('datatables', $mp_options['root_url'].'mp-admin/css/tables.css');
    mp_enqueue_script_theme('datatables', $mp_options['root_url'].'mp-admin/js/dataTables.js', array('ready'), 1);
    mp_enqueue_script_admin('datatables', $mp_options['root_url'].'mp-admin/js/dataTables.js', array('jquery'), 1);
    ?>
    <form id="objects-form" class="mp-form" data-allow-edits="<?php echo $allow_edits; ?>">
		<select id="object-actions" autocomplete="off">
			<option value=""><?php _e('--- --- Select Action --- ---'); ?></option>
			<option value="delete"><?php _e('Delete Selected Objects'); ?></option>
		</select>
        <table id="objects" class="data-tables"></table>
        <?php
        mp_nonce_field('objects-form','mp_nonce');
        if($total_object_count<1){
            echo '<a id="mp-import-html" href="'.$mp_options['admin_url'].'import/" class="button" '.mp_get_attr_filter('objects.php','a','admin/import/','mp-import-html','button','style="float:right;" data-nonce="'.mp_create_nonce('mp-install','private').'"').'>'.__('Import Objects').'</a>';
        }
        ?>
    </form>
	<div style="clear:both; display: block;"></div>
    <?php
}

function mp_media_gallery(){
    $m = mongopress_load_m();
    $mp = mongopress_load_mp();
    $options = $mp->options();
    $db = $m->$options['db_name'];
    $gridded_media = $db->fs->files->find();
    $gridded_media_count = $gridded_media->count();
    $nonce = mp_create_nonce('media_view','public');
	$action_nonce = mp_create_nonce('media_action','private');
    echo '<ul id="mp-media-gallery" data-nonce="'.$nonce.'" data-nonce-action="'.$action_nonce.'">';
    if($gridded_media_count>0){
        mp_enqueue_style_admin('media', $options['root_url'].'mp-admin/css/media.css');
        foreach($gridded_media as $media){
            $mp_media_object_id = $mp->get_mongoid_as_string($media["_id"]);
            echo '<li id="id_'.$mp_media_object_id.'">';
            if(($media['type']=='image/png')||($media['type']=='image/gif')||($media['type']=='image/jpg')||($media['type']=='image/jpeg')){
                echo '<span class="media-frame">';
                    mp_get_media($nonce, $mp_media_object_id, 'image', false, false, 'the_filename');
                echo '</span>';
                echo '<span class="media-meta">';
                    echo '<p><strong>'.__('Filename:').'</strong> <span id="image_filename_'.$mp_media_object_id.'">'.$media['filename'].'</span></p>';
                    echo '<p><strong>'.__('Actual Name:').'</strong> <span id="actual_name_'.$mp_media_object_id.'">'.$media['actual'].'</span></p>';
                    echo '<p><strong>'.__('Pretty URL:').'</strong><br /><span id="pretty_url_'.$mp_media_object_id.'"><a href="'.$options['root_url'].'media/'.$media['actual'].'">media/'.$media['actual'].'</a></span></p>';
                    echo '<p><strong>'.__('File Type:').'</strong> '.$media['type'].'</p>';
                    echo '<p><strong>'.__('Views:').'</strong> '.$media['views'].'</p>';
                    echo '<p><a href="#" class="edit-media" '.mp_get_attr_filter('media-gallery.php','a','#','','edit-media','data-mongo-id="'.$mp_media_object_id.'"').'>'.__('Edit').'</a> | <a href="#" class="delete-media" '.mp_get_attr_filter('media-gallery.php','a','#','','delete-media','data-mongo-id="'.$mp_media_object_id.'"').'>'.__('Delete').'</a></p>';
                echo '</span>';
            }elseif(($media['type']=='application/download')||($media['type']=='application/x-zip')||($media['type']=='application/octet-stream')||($media['type']=='application/zip')){
                echo '<span class="media-frame"><p style="display:block; text-align:center; padding: 25px 5% 15px;">'.__('ZIP File').'</p></span>';
                echo '<span class="media-meta">';
                    echo '<p>';
                        mp_get_media($nonce, $mp_media_object_id,'download',__('Download Link:').'<br />'.$media['actual'], 'download_filename_'.$mp_media_object_id, 'the_filename');
                    echo '</p>';
					echo '<p><strong>'.__('Filename:').'</strong> <span id="image_filename_'.$mp_media_object_id.'">'.$media['filename'].'</span></p>';
                    echo '<p><strong>'.__('Actual Name:').'</strong> <span id="actual_name_'.$mp_media_object_id.'">'.$media['actual'].'</span></p>';
                    echo '<p><strong>'.__('Pretty URL:').'</strong><br /><span id="pretty_url_'.$mp_media_object_id.'"><a href="'.$options['root_url'].'media/'.$media['actual'].'">media/'.$media['actual'].'</a></span></p>';
                    echo '<p><strong>'.__('Downloads:').'</strong> '.$media['downloads'].'</p>';
                    echo '<p><a href="#" class="edit-media" data-mongo-id="'.$mp_media_object_id.'">'.__('Edit').'</a> | <a href="#" class="delete-media" data-mongo-id="'.$mp_media_object_id.'">'.__('Delete').'</a></p>';
                echo '</span>';
            }else{
                echo '<span class="media-frame"></span>';
                echo '<span class="media-meta">';
                    echo '<p>'.__('Unsupported Media Type').'</p>';
                echo '</span>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }else{
        echo '<br><p style="font-weight:bold;">'.__('No media added yet.').'</p><br />';
    }
}

function mp_refresh_cookies(){
    $mp = mongopress_load_mp();
    $default_options = $mp->options();
    $m = mongopress_load_m();
    $db = $m->$default_options['db_name'];
    $user = $db->$default_options['user_col'];
    $current_user = $mp->get_current_user();
    $username = $current_user['un'];
    $user_name = $current_user['name'];
    $user_obj = $mp->arrayed($user->find(array("un"=>$username)));
    $user_mongo_id = $mp->get_mongoid_as_string($user_obj[0]["_id"]);
    $cookie_ttl = $default_options['cookie_ttl'];
    if ($cookie_ttl == 'session') $ttl = 0;
    elseif ($cookie_ttl == 'never') $ttl = time() + 3600*24*365*10;// 10 years! not never but sheesh.
    else $ttl = time() + intval($cookie_ttl);
    setcookie('mp_logged_in_'.$default_options['cookie_salt'],true,$ttl,'/');
    setcookie('mp_logged_in_user_id_'.$default_options['cookie_salt'],$user_mongo_id,$ttl,'/');
    setcookie('mp_username',$user_name,$ttl,'/');
}

function mp_admin_page($options){
	$default_settings = array(
		'header'	=> true,
		'page'		=> 'dashboard',
		'parent'	=> false
	);
	if(is_array($options)){
		$settings = array_merge($default_settings,$options);
	}else{
		$settings = $default_settings;
	}
	if($settings['header']){
		mp_get_admin_header();
	}
	$mp = mongopress_load_mp();
	$mp_options = $mp->options();
	$display_name = $GLOBALS['_MP']['COOKIE']['mp_display_name'];
	$current_user_id = $GLOBALS['_MP']['COOKIE']['mp_user_id'];
	$user_info = $mp->get_current_user();
	$nonce = mp_create_nonce('mp_admin_pages','private');
	$avatar_nonce = mp_create_nonce('public-avatar','public');
	$default_avatar = $mp_options['root_url'].'mp-includes/images/add_image.png';
	$current_avatar = mp_get_avatar($user_info['email'],80,'mm','g',false,false,true,$current_user_id);
	if($settings['header']){
	?>

	<div id="left-column">
		<div class="admin-header">
			<span>
				<a href="<?php echo $mp_options['root_url']; ?>"><?php printf(__('Return to %s'),$mp_options['site_name']); ?></a>
			</span>
		</div>
		<div id="sidebar">
			<div class="scroll-wrapper">
				<ul class="menu-lists">
					
					<input id="mp-admin-nonce" type="hidden" value="<?php echo $nonce; ?>" />

					<li id="dashboard" class="menus <?php if(($settings['page']=='dashboard')||($settings['parent']=='dashboard')){ echo 'current'; }; ?> got-submenu" data-page="dashboard">
						<span class="title"><?php _e('Dashboard'); ?></span>
						<span class="sub-menu">
							<div class="user-meta">
								<span class="user-avatar"><a href="profile/" data-page="profile"><img class="avatar fetch-avatar" src="<?php echo $default_avatar; ?>" data-avatar-nonce="<?php echo $avatar_nonce; ?>" data-user-id="<?php echo $current_user_id; ?>" /></a></span>
								<span class="user-status">
									<span class="user-name"><?php printf(__('Logged-in as %s'), $display_name); ?></span>
									<a href="profile/" data-page="profile" class="button"><?php _e('Edit Profile'); ?></a>
									<a href="logout/" data-page="logout" class="button"><?php _e('Log-Out'); ?></a>
								</span>
							</div>
						</span>
					</li>

					<li id="objects" class="menus <?php if(($settings['page']=='objects')||($settings['parent']=='objects')){ echo 'current'; }; ?>" data-page="objects">
						<span class="title"><?php _e('Objects'); ?></span>
						<span class="sub-menu">

						</span>
					</li>

					<li id="media" class="menus <?php if(($settings['page']=='media')||($settings['parent']=='media')){ echo 'current'; }; ?>" data-page="media">
						<span class="title"><?php _e('Media'); ?></span>
						<span class="sub-menu">

						</span>
					</li>

					<li id="settings" class="menus <?php if(($settings['page']=='settings')||($settings['parent']=='settings')){ echo 'current'; }; ?>" data-page="settings">
						<span class="title"><?php _e('Settings'); ?></span>
						<span class="sub-menu">

						</span>
					</li>

				</ul>
			</div>
		</div>
		<div class="fixed-footer">
			<span>
				<?php
				$versions = mongopress_get_versions();
				$php_version = $versions['current']['php'];
				$mongodb_version = $versions['current']['mongodb'];
				$phpd_version = $versions['current']['phpd'];
				$mongopress_version = $versions['mongopress'];
				$core_contributors = '<a href="'.$mp_options['admin_url'].'contributors/">'.__('Core Contributors').'</a>';
				echo '<p>'.sprintf(__('%s: MongoPress %s | PHP %s | MongoDB %s | MongoDB PHP Drivers %s'), $core_contributors, $mongopress_version, $php_version, $mongodb_version, $phpd_version).'</p>';
				?>
			</span>
		</div>
	</div>

	<div id="right-column">
		<div class="admin-header">
			<span>
				<?php
				$log_out_button = '<a id="mp-admin-logout" href="#">'.__('Log-Out').'</a>';
				printf(__('Logged-in as %s %s'), $display_name, $log_out_button);
				?>
			</span>
		</div>
		<div id="mp-content">
			<div class="scroll-wrapper">
				<div id="current-content">
					<?php
					} // End of IF HEADER
					if($settings['page']=='dashboard'){
						$db_info = mongopress_get_db_info();
						$total_objs = $db_info['total_objs'];
						echo '<div id="mp-notifications" class="notification" style="display:none;">'.sprintf(__('Your Current Version of MongoPress is Out-of-Date - %s'),'<a href="http://mongopress.org">'.__('Please Download Latest Version').'</a>').'</div>';
						if($total_objs<1){
							mp_enqueue_script_admin('import', $mp_options['root_url'].'mp-admin/js/import.js', array('ready'));
							echo '<div id="mp-welcome" class="notification">'.sprintf(__('Your Have Successfully Installed MongoPress - You May Now:<br /><br />%s or %s'),'<a id="mp-import-html" class="button" data-nonce="'.mp_create_nonce('mp-install','private').'" href="'.$mp_options['admin_url'].'import/">'.__('Import Content').'</a>', '<a class="button" href="'.$mp_options['admin_url'].'objects/">'.__('Add Objects').'</a>').'</div>';
						}
					}elseif($settings['page']=='objects'){
						$db_info = mongopress_get_db_info();
						$total_objs = $db_info['total_objs'];
						if($total_objs<1){
							mp_enqueue_script_admin('import', $mp_options['root_url'].'mp-admin/js/import.js', array('ready'));
						}
					}elseif($settings['page']=='profile'){
						if($current_avatar==$default_avatar){
							echo '<div id="mp-welcome" class="notification">'.__('No Avatar Present - add your email address and use externally loaded <a href="http://gravatar.com">Gravatars</a> or upload and use your own internal avatars below!').'</div>';
						}
					}
					if(file_exists(dirname(__FILE__).'/inc/mp-admin-'.$settings['page'].'.php')){
						include_once(dirname(__FILE__).'/inc/mp-admin-'.$settings['page'].'.php');
					}else{
						include_once(dirname(__FILE__).'/inc/mp-admin-objects.php');
					}
					if($mp_options['debug']===true){
						include_once(dirname(__FILE__).'/inc/mp-admin-debug.php');
					} if($settings['header']){ ?>
				</div>
			</div>
		</div>
		<div class="fixed-footer">
			<span>
				<?php
				$current_year =  date("Y");
				echo '&copy; '.$current_year.' '.$mp_options['site_name'].' | '.__('Proudly Powered by <a href="http://mongopress.org">MongoPress</a> and <a href="http://www.gnu.org/licenses/gpl.html">GPLv3</a> Freedoms');
				?>
			</span>
		</div>
	</div>

	<?php
	} // End of second IF HEADER
	if($settings['header']){
		mp_get_admin_footer();
	}
}

function mp_table_hook($id, $data, $titles){
	$mp = mongopress_load_mp();
	$columns = ''; $column_count = 0;
	foreach($titles as $title => $class) { 
		$column_count++;
		if($column_count<2){
			$columns.= "{'sTitle': '$title', 'sClass': '$class'}"; 
		}else{
			$columns.= ",{'sTitle': '$title', 'sClass': '$class'}"; 
		}
	}
	$this_function = "mp_table_hook_$id";
	$this_js_function = "<script> $(window).load(function() { $('#$id').dataTable({ 'aaData':$data, 'aoColumns': [ $columns ] }); }); </script>";
	$eval_action = "function $this_function(){ ?> $this_js_function <?php }";
	eval($eval_action);
	if(function_exists($this_function)){
		echo $this_js_function;
	}
}

function mp_table($options=false){
	$mp = mongopress_load_mp();
	$mp_options = $mp->options();
	$default_options = array(
		'id'	=> 'mp-table',
		'class'	=> 'data-tables',
		'data'	=> false,
		'title'	=> false
	);
	if(is_array($options)){
		$settings = array_merge($default_options,$options);
	}else{
		$settings = $default_options;
	}
	if((isset($settings['data']['rows']))&&(isset($settings['data']['titles']))){
		foreach($settings['data']['rows'] as $key => $this_data){
			$data[] = $this_data;
		}
		$data_array = json_encode($data);
		mp_enqueue_style_admin('datatables', $mp_options['root_url'].'mp-admin/css/tables.css');
		mp_enqueue_style_theme('datatables', $mp_options['root_url'].'mp-admin/css/tables.css');
		mp_enqueue_script_theme('datatables', $mp_options['root_url'].'mp-admin/js/dataTables.js', false, 1);
		mp_enqueue_script_admin('datatables', $mp_options['root_url'].'mp-admin/js/dataTables.js', false, 1);
		mp_table_hook($settings['id'], $data_array, $settings['data']['titles']);
		?>
		<div class="non-ajax">
			<?php if(isset($settings['title'])){ echo '<h3 class="table-title">'.$settings['title'].'</h3>'; } ?>
			<table id="<?php echo $settings['id']; ?>" class="<?php echo $settings['class']; ?>"></table>
		</div>
		<div style="clear:both; display: block;"></div>
		<?php
	}else{
		/* THROW ERROR ...? */
		_e('No data sent to function!');
	}
}
