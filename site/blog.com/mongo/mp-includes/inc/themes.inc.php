<?php

/* TODO: ADD THIS ALL TO TEMPLATE CLASS */
function load_template( $_template_file, $require_once = true ) {
    if ( $require_once ){
        require_once( $_template_file );
    }else{
        require( $_template_file );
    }
}
function locate_template($template_names, $load = false, $require_once = true ) {
    $mp = mongopress_load_mp();
    $options = $mp->options();

    $located = '';
    foreach ( (array) $template_names as $template_name ) {
        if ( !$template_name )
            continue;
        if ( file_exists($options['root_path'].'mp-content/themes/'.$options['theme'].'/'.$template_name)) {
            $located = $options['root_path'].'mp-content/themes/'.$options['theme'].'/'.$template_name;
            break;
        }
    }
    if ( $load && '' != $located ){
        load_template( $located, $require_once );
    }
    return $located;
}
function get_template_part( $slug, $name = null ) {
    do_action( "get_template_part_{$slug}", $slug, $name );
    $templates = array();
    if ( isset($name) ){
        $templates[] = "{$slug}-{$name}.php";
    }
    $templates[] = "{$slug}.php";
    locate_template($templates, true, false);
}

function mp_html_class(){
    /* TODO: USE THE BIT FROM HEADJS THAT ADDS THE HTML CLASSES */
    $class = false;
    $class = apply_filters('mp_html_class',$class);
    return $class;
}

function mp_body_class(){
    /* TODO: USE THE BIT FROM HEADJS THAT ADDS THE HTML CLASSES */
    $class = false;
    $class = apply_filters('mp_body_class',$class);
    return $class;
}

function mp_html_lang(){
    $lang = 'en';
    $lang = apply_filters('mp_html_lang',$lang);
    return $lang;
}

function mp_page_title($forced_title=false){
    global $current_object_title;
    if($forced_title){ $current_object_title = $forced_title; }
    $mp_perma = mongopress_load_perma();
    $perma = $mp_perma->current();
    global $mp_options;
    if($perma){
        if($perma=='admin'){
            $title = $mp_options['site_name'].__(' | Admin Settings');
        }else{
            $title = $current_object_title.' | '.$mp_options['site_name'];
        }
    }else{
        if(!empty($current_object_title)){
            if(isset($mp_options['site_description'])){
                $title = $current_object_title.' - '.$mp_options['site_description'];
            }else{
                $title = $current_object_title;
            }
        }else{
            if(isset($mp_options['site_description'])){
                $title = $mp_options['site_name'].' - '.$mp_options['site_description'];
            }else{
                $title = $mp_options['site_name'];
            }
        }
    }
    $title = apply_filters('mp_page_title',$title);
    return $title;
}

function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, -$testlen) === 0;
}

function mp_head($options=false,$objects=false,$hide_mongoid=true){
    /* ESTABLISH GLOBAL VARS AND LOAD CONFIG FILE */
    global $mp_options, $current_object_id, $current_slug_id, $current_object_title;
    global $current_object_group, $current_query, $direct_object_id, $mp_scripts_theme, $mp_styles_theme;
    $mp = mongopress_load_mp();
    $mp_options = $mp->options();
    $m = mongopress_load_m(); $db = $m->$mp_options['db_name'];
    $mp_perma = mongopress_load_perma();
    $perma = $mp_perma->current();
    $slugs = $db->$mp_options['slug_col'];
    $objs = $db->$mp_options['obj_col'];
    $m->close();
    if(($perma)&&($perma!='mp-includes/error.php')&&($perma!='mp-includes/error.php')){
        if(strstr($perma,'/')){
            $current_object_group = array();
            $perma_group_array = explode('/', $perma);
            foreach($perma_group_array as $group){
                $current_object_group[] = $group;
            }
            if($current_object_group[0]==$mp_options['query_perma_key']){
                $current_query['filter']=$current_object_group[1];
                $current_query['value']=$current_object_group[2];
                $current_query['limit']=(int)$current_object_group[3];
            }else{
                $count = (int)array_pop($current_object_group);
                foreach($current_object_group as $key => $value){
                    if(empty($search_term)){
                        $search_term=$value;
                    }else{
                        $search_term.='/'.$value;
                    }
                }
                $current_query[$mp_options['search_perma_key']]=$search_term;
                $current_query['search_limit']=$count;
            }
        }
        $this_slug_array = $mp->arrayed($slugs->find(array("slug"=>$perma)));
        if(count($this_slug_array)>0){
            $this_slug_id = $mp->get_mongoid_as_string($this_slug_array[0]['_id']);
            $object = $mp->arrayed($objs->find(array("slug_id"=>$this_slug_id)));
        }else{
            $this_slug_id = false;
        }
        if(is_array($current_query)){
            if(!empty($current_query['search'])){
                if(count($this_slug_array)<1){
                    $object = $mp->arrayed($objs->find(array("slug_id"=>$this_slug_id)));
                }
                if(isset($object)){
                    if(count($object)<1){
                        $current_object_title = __('Search');
                        $current_object_id = $mp_options['search_perma_key'];
                        $current_slug_id = $mp_options['search_perma_key'];
                    }else{
                        $current_object_id = $mp->get_mongoid_as_string($object[0]['_id']);
                        $current_slug_id = $object[0]['slug_id'];
                        $current_object_title = $object[0]['title'];
                    }
                }
            }else{
                if(isset($object)){
                    if(count($object)<1){
                        $current_object_title = __('Query');
                        $current_object_id = $mp_options['query_perma_key'];
                        $current_slug_id = $mp_options['query_perma_key'];
                    }else{
                        $current_object_id = $mp->get_mongoid_as_string($object[0]['_id']);
                        $current_slug_id = $object[0]['slug_id'];
                        $current_object_title = $object[0]['title'];
                    }
                }
            }
        }else{
            if(isset($object)){
                if(count($object)<1){
                    $current_object_id = 'error';
                    $current_slug_id = 'error';
                    $current_object_title = __('Error');
                }else{
                    $current_object_id = $mp->get_mongoid_as_string($object[0]['_id']);
                    $current_slug_id = $object[0]['slug_id'];
                    $current_object_title = $object[0]['title'];
                }
            }
        }
    }else{
        if(isset($object)){
            if(!empty($direct_object_id)){
                $current_object_id = untrailingslashit($direct_object_id);
                $current_object_mongo_id = new MongoId($current_object_id);
                $object = $mp->arrayed($objs->find(array("_id"=>$current_object_mongo_id)));
                $current_slug_id = $object[0]['slug_id'];
                $current_object_title = $object[0]['title'];
            }else{
                $current_object_id = 'home';
                $current_slug_id = 'home';
                $current_object_title = $mp_options['site_name'];
            }
        }
    }
    /* SET-UP PAGE OPTIONS */
    $default_meta_charset = 'utf-8';
    $default_meta_description = $current_object_title;
    $default_meta_author = $mp_options['root_url'];
    $default_options = array(
        'meta_charset'      => apply_filters('mp_meta_charset',$default_meta_charset,$perma),
        'meta_description'  => apply_filters('mp_meta_description',$default_meta_description,$perma),
        'meta_author'       => apply_filters('mp_meta_author',$default_meta_author,$perma)
    );
    if(is_array($options)){
        $settings = array_merge($default_options,$options);
    }else{
        $settings = $default_options;
    };
    /* PRINT HEADER STYLES */
    $reset_url = $mp_options['root_url'].'mp-includes/css/reset.css';
    $theme_style = $mp_options['theme'].'/css/style.css';
    $stylesheet_url = $mp_options['root_url'].'mp-content/themes/'.$theme_style;
    mp_register_style('reset', $reset_url, false, 1, 'screen');
    mp_register_style('style', $stylesheet_url, array('reset'), 1, 'screen');
	mp_register_style('dprint', $mp_options['root_url'].'mp-includes/css/dprint.css', array('reset'), 1, 'print');
    mp_register_style('print', $mp_options['root_url'].'mp-content/themes/'.$mp_options['theme'].'/css/print.css', array('reset'), 1, 'print');
    $default_styles = array('reset','style','dprint','print');
    if(is_array($mp_styles_theme)){
        $styles_to_load = array_merge($mp_styles_theme,$default_styles);
    }else{
        $styles_to_load = $default_styles;
    }
    $filtered_styles = apply_filters('mp_theme_css_to_load_header',$styles_to_load);
    mp_print_styles($filtered_styles);
    /* PRINT HEADER SCRIPTS */
    mp_register_script('languages', $mp_options['root_url'].'mp-includes/js/languages.php', false, 1);
    mp_register_script('jquery', $mp_options['root_url'].'mp-includes/js/jquery-1.6.4.min.js', false, '1.6.4');
	mp_register_script('functions', $mp_options['root_url'].'mp-includes/js/functions.js', array('jquery'));
    $default_scripts = array('languages','jquery', 'functions');
    if(is_array($mp_scripts_theme)){
        $scripts_to_load = array_merge($mp_scripts_theme,$default_scripts);
    }else{
        $scripts_to_load = $default_scripts;
    }
    $filtered_scripts = apply_filters('mp_theme_js_to_load_header',$scripts_to_load);
    mp_print_scripts($filtered_scripts);
    ?>
    <meta charset="<?php echo $settings['meta_charset']; ?>">

    <meta name="description" content="<?php echo $settings['meta_description']; ?>">
    <meta name="author" content="<?php echo $settings['meta_author']; ?>">
    <meta id="viewport" name="viewport" content="initial-scale=1.0, user-scalable=1, minimum-scale=1.0, maximum-scale=2.0" />
	<meta name="apple-mobile-web-app-capable" content="yes" />

    <!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
    <link rel="shortcut icon" href="<?php echo $mp_options['root_url']; ?>favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo $mp_options['root_url']; ?>apple-touch-icon.png">
    <script src="<?php echo $mp_options['root_url']; ?>mp-includes/js/body.css.js"></script>
    <?php
    if((strstr($_SERVER['HTTP_USER_AGENT'],'iPad'))||(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone'))){
        echo '<script src="'.$mp_options['root_url'].'mp-includes/js/ios.js"></script>';
    }
    ?>
    <script>
	<?php if((strstr($_SERVER['HTTP_USER_AGENT'],'iPad'))||(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone'))){ ?>
	var is_ios = true;
	<?php }else{ ?>
	var is_ios = false;
	<?php } ?>
    var mp_root = '<?php echo $mp_options['root_url']; ?>';
    var mp_admin_url = '<?php echo $mp_options['admin_url']; ?>';
    var mp_root_url = '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$mp_options['root_url']; ?>';
	var mp_media_url = '<?php echo $mp_options['full_url'].$mp_options['media_slug'] ?>';
    var mp_theme_url = '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$mp_options['root_url']; ?>mp-content/themes/<?php echo $mp_options['theme']; ?>/';
    </script>
    <!--[if lt IE 9]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script><![endif]-->
    <?php
    do_action('mp_header');
}

function mp_get_simple_header($title){
    global $mp_scripts_theme, $mp_styles_theme;
    $default_styles = array(); $default_scripts = array();
    $mp = mongopress_load_mp();
    $mp_options = $mp->options();
    ?>
<!doctype html>
<html class="<?php echo mp_html_class(); ?>" lang="<?php echo mp_html_lang(); ?>">
<head>
<title><?php echo mp_page_title($title); ?></title>
<script>
        var mp_root = '<?php echo $mp_options['root_url']; ?>';
        var mp_root_url = '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$mp_options['root_url']; ?>';
        var mp_theme_url = '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$mp_options['root_url']; ?>mp-content/themes/<?php echo $mp_options['theme']; ?>/';
</script>
<?php
        if(is_array($mp_styles_theme)){
            $styles_to_load = array_merge($mp_styles_theme,$default_styles);
        }else{
            $styles_to_load = $default_styles;
        }
        $filtered_styles = apply_filters('mp_theme_css_to_load_header',$styles_to_load);
        mp_print_styles($filtered_styles);
        if(is_array($mp_scripts_theme)){
            $scripts_to_load = array_merge($mp_scripts_theme,$default_scripts);
        }else{
            $scripts_to_load = $default_scripts;
        }
        $filtered_scripts = apply_filters('mp_theme_js_to_load_header',$scripts_to_load);
        mp_print_scripts($filtered_scripts);
        ?>
</head>
<?php flush(); ?>
<body class="<?php echo mp_body_class(); ?>">
<?php
}

function mp_get_header($options=false,$hide_mongoid=true){
    ?>
    <!doctype html>
    <html class="<?php echo mp_html_class(); ?>" lang="<?php echo mp_html_lang(); ?>">
    <head>
      <?php mp_head($options,$hide_mongoid); ?>
      <title><?php echo mp_page_title(); ?></title>
    </head>
    <body class="<?php echo mp_body_class(); ?>">
    <?php
}

function mp_debug_theme(){
	global $mp_objects;
	$mp = mongopress_load_mp();
	echo '<br /><p style="font-weight:bold;">'.__('Currently available hooks:').'</p><br />';
	echo list_hooked_functions();
	echo '<br /><p style="font-weight:bold;">'.__('The current objects presently displayed on screen include:').'</p><br />';
	$mp->dump($mp_objects,false);
}

function list_hooked_functions($tag=false){
	global $mp_filter;
	if ($tag) {
		$hook[$tag]=$mp_filter[$tag];
		if (!is_array($hook[$tag])) {
			trigger_error("Nothing found for '$tag' hook", E_USER_WARNING);
			return;
		}
	} else {
		$hook=$mp_filter;
		ksort($hook);
	}
	echo '<pre>';
	foreach($hook as $tag => $priority){
		echo "<br />&gt;&gt;&gt;&gt;&gt;\t<strong>$tag</strong><br />";
		ksort($priority);
		foreach($priority as $priority => $function){
			echo $priority;
			foreach($function as $name => $properties) echo "\t$name<br />";
		}
	}
	echo '</pre>';
	return;
}

function mp_get_footer(){
    global $mp_scripts_theme, $mp_scripts_theme, $mp_styles_theme;
    $mp = mongopress_load_mp();
    $mp_perma = mongopress_load_perma();
    $default_options = $mp->options();
    if($default_options['debug']){
        add_action('mp_footer','mp_debug_theme');
    }
    /* PRINT FOOTER STYLES */
    $filtered_styles = apply_filters('mp_theme_css_to_load_footer',$mp_styles_theme);
    mp_print_styles($filtered_styles);
    /* PRINT FOOTER SCRIPTS */
    mp_register_script('ready', $default_options['root_url'].'mp-includes/js/ready.js', array('jquery'), 1);
    $default_scripts = array('ready');
    if(is_array($mp_scripts_theme)){
        $scripts_to_load = array_merge($mp_scripts_theme,$default_scripts);
    }else{
        $scripts_to_load = $default_scripts;
    }
    $filtered_scripts = apply_filters('mp_theme_js_to_load_footer',$scripts_to_load);
    mp_print_scripts($filtered_scripts);
    do_action('mp_footer');
    echo '</body>';
    echo '</html>';
}

function mp_get_content($objects=false,$add_lis=false){
    global $mp_objects, $current_object_id, $current_slug_id, $current_query, $direct_object_id;
	
    $direct_object_id = untrailingslashit($direct_object_id);


    $class = false;

    $default_options = array(
        'type'          => false,
        'style'         => false,
        'order_by'      => false,
        'limit'         => false,
        'order'         => false,
        'can_query'     => false,
        'near'          => false
    );

    $error = array();
    if(is_array($objects)){
        $object = array_merge($default_options,$objects);
        extract($object);
        if(empty($style)){ $error_message = __('Expecting Style for Getting Content'); }
        if(($style!='single')&&($style!='array')){
            if(empty($type)){ $error_message = __('Expecting Type for Getting Content'); }
        }
        if(isset($error_message)){
            $error['message'] = $error_message;
            $error['success'] = false;
        }
    }else{
        $error['message'] = __('Expecting an Array to Get Content');
        $error['success'] = false;
    } 

	if($error){ return $error; }else{
        $mp = mongopress_load_mp();
        $permas = mongopress_load_perma();
        $mp_options = $mp->options();
        if(empty($limit)){ $limit = $mp_options['objects_per_page']; }
        $perma = $permas->current();
        $m = mongopress_load_m();
        $db = $m->$mp_options['db_name'];
        $m->close();

        /* ADD MAP REDUCE */
        //-> NOT WORKING YET TODO: FIX THIS ASAP -> $objs = mp_map_reduce_three($mp_options['obj_col'],'type',$type);
        /* END OF MAP REDUCE */
        $objs = $db->$mp_options['obj_col'];
        $slugs = $db->$mp_options['slug_col'];
		/* TODO: REALLY REALLY NEED TO CLEAN UP THIS WHOLE FUNCTION */
		/* AM INTERCEPTING EARLY FOR STYLE ARRAY TYPE EMPTY */
		if(($style=='array')&&(empty($type))){
			/* SEND BACK ALL OBJECTS */
			if($order_by){
                if($order!='desc'){ $order_value=1; }else{ $order_value=-1; }
                $sort_clause = array($order_by=>$order_value);
            }else{ $sort_clause = array(); }
			$these_objects = $mp->arrayed($objs->find()->sort($sort_clause)->limit($limit));
			$all_objects = apply_filters('mp_get_content_array',$these_objects);
			return $all_objects; exit;
		}elseif($order_by=='geoNear'){
            $geo_near_query = array('geoNear'=>$mp_options['obj_col'],'near'=>$near,'num'=>$limit,'query'=>array("type"=>$type,"points"=>array('$ne'=>array("lng"=>0,"lat"=>0))));
            $geo_results = $db->command($geo_near_query);
            if(is_array($geo_results['results'])){
                foreach($geo_results['results'] as $result){
                    if(is_array($result['obj'])){
                        $temp_geo_results[] = $result['obj'];
                    }
                } $these_objects = $temp_geo_results;
                if($perma){
                    $action='single';
                }else{
                    if(($direct_object_id)&&(empty($type))){
                        $action='single';
                    }else{
                        if($direct_object_id){
                            $action='single';
                        }else{
                            $action='home';
                        }
                    }
                }
            }
        }else{
            if($order_by){
                if($order!='desc'){ $order_value=1; }else{ $order_value=-1; }
                $sort_clause = array($order_by=>$order_value);
            }else{ $sort_clause = array(); }
            if($can_query){
                if($current_object_id!='error'){
                    if($current_object_id!=$mp_options['query_perma_key']){
                        if($current_object_id!=$mp_options['search_perma_key']){
                            $action = 'single';
                        }else{
                            $action = 'search';
                        }
                    }else{
                        $action = 'query';
                    }
                }else{
                    $action = 'error';
                }
            }
            if(($can_query)&&($action!='single')){
                if(($current_object_id==$mp_options['query_perma_key'])||($current_object_id==$mp_options['search_perma_key'])){
                    if(is_array($current_query)){
                        if($current_object_id==$mp_options['query_perma_key']){
                            /* QUERY */
                            $filter = $current_query['filter'];
                            $new_limit = (int)$current_query['limit'];
                            if(empty($new_limit)){ $new_limit=$mp_options['objects_per_page']; }
                            $value = $current_query['value'];
                            $filtered_where_clause = array(
                                $filter => $value
                            );
                            $these_objects = $mp->arrayed($objs->find($filtered_where_clause)->sort($sort_clause)->limit($new_limit));
                        }else{
                            /* SEARCH */
                            $search_term = $current_query[$mp_options['search_perma_key']];
                            $search_limit = $current_query['search_limit'];
                            if(empty($search_limit)){ $search_limit=$mp_options['objects_per_page']; }
                            $slugged_where_clause = array(
                                'slug' => new MongoRegex("'/$search_term/i'")
                            );
                            $these_slugs = $mp->arrayed($slugs->find($slugged_where_clause));
                            if(is_array($these_slugs)){
                                foreach($these_slugs as $key => $value){
                                    $this_slug_id = $mp->get_mongoid_as_string($value['_id']);
                                    $this_obj = $mp->arrayed($objs->find(array("slug_id"=>$this_slug_id)));
                                    $these_objects[$key]=$this_obj[0];
                                }
                                $these_objects = array_splice($these_objects, 0, $search_limit);
                            }
                        }
                    }else{
                        $where_clause = array("type"=>$type);
                        $these_objects = $mp->arrayed($objs->find($where_clause)->sort($sort_clause)->limit($limit));
                    }
                }else{
                    $where_clause = array("type"=>$type);
                    $these_objects = $mp->arrayed($objs->find($where_clause)->sort($sort_clause)->limit($limit));
                }
            }else{
                /* SINGLE PAGES */
                if(isset($action) && $action=='single'){
                    $current_object_mongo_id = new MongoId($current_object_id);
                    $these_objects[0] = $mp->find_one($current_object_mongo_id);
                }else{
                    $where_clause = array("type"=>$type);
                    $these_objects = $mp->arrayed($objs->find($where_clause)->sort($sort_clause)->limit($limit));
                }
            }
            if($current_object_id==$mp_options['query_perma_key']){
                /* QUERY / FILTER */
                $action='query';
            }elseif($current_object_id==$mp_options['search_perma_key']){
                /* SEARCH OBJS */
                $action='search';
            }elseif($current_object_id=='error'){
                /* ERROR */
                $action='error';
            }else{
                if($perma){
                    /* SINGLE OBJ */
                    $action='single';
                }else{
                    if(($direct_object_id)&&(empty($type))){
                        $action='single';
                        $direct_object_mongo_id = new MongoId($direct_object_id);
                        $these_objects[0] = $mp->find_one($direct_object_mongo_id);
                        $current_object_id = $direct_object_id;
                    }else{
                        if($direct_object_id){
                            /* HOMEPAGE */
                            $action='single';
                        }else{
                            /* HOMEPAGE */
                            $action='home';
                        }
                    }
                }
            }
        } // END OF NOT GEONEAR

        /* - USEFUL FOR DEBUGGING :-)
        echo '$type = '.$type.' <br />';
        echo '$style = '.$style.' <br />';
        echo '$can_query = '.$can_query.' <br />';
        echo '$current_object_id = '.$current_object_id.' <br />';
        echo '$action = '.$action.' <br />';
        $mp->dump($these_objects,false);
        */

        /* TODO: BUILD THIS AGAIN FROM SCRATCHING KNOWING WHAT YOU KNOW !!! */
        $mp_objects[$type] = $these_objects;
        $content = '';
        $content_array = array();
        if(is_array($these_objects)){
            $this_content = '';
            foreach($these_objects as $key => $value){
                /* PREVENT PHP WARNINGS */
                if(!isset($value['type'])){ $value['type'] = false; }
                if(!isset($value['updated'])){ $value['updated'] = false; }
                if(!isset($value['content'])){ $value['content'] = false; }
                if(!isset($value['title'])){ $value['title'] = false; }
                if(!isset($value['slug_id'])){ $value['slug_id'] = false; }
                if(!isset($value['_id'])){ $value['_id'] = false; }
                if(($value['type']==$type)&&(!empty($type))){
                    if(($direct_object_id)||($mp_options['skip_htaccess'])){
                        $this_slug = '?obj='.$mp->get_mongoid_as_string($value['_id']);
                    }else{
                        $this_slug_id = $value['slug_id'];
                        $this_slug_mongo_id = new MongoId($this_slug_id);
                        $this_slug_array = $slugs->findOne(array("_id"=>$this_slug_mongo_id));
                        $this_slug = $this_slug_array['slug'];
                    }
                    if($style=='article'){
                        $filtered_content = apply_filters('mp_shortcodes', apply_filters('mp_article_content',$mp->mp_sensible_formatting_filter($value['content'])));
                        $published_ago_distance = mingo_meantime($value['updated']);
			$published_date = mingo_meantime(false,false,'date');
			$published_ago = sprintf(__('( Last updated %s ago - %s )'),$published_ago_distance ,  $published_date);
                        $time = '<time datetime="'.$value['updated'].'">'.$published_ago.'</time>';
                        if($perma){
                            if(empty($this_content)){
                                $this_content = '<section class="content"><header class="title"><h3 class="article-title header">'.apply_filters('mp_article_title',$value['title']).'</h3>'.$time.'</header><div class="content">'.$filtered_content.'</div></section>';
                                $this_content = mp_content_block('article',false,false,$this_content);
                            }
                            $content.= $this_content;
                        }else{
                            $just_content = '<section class="content"><header class="title"><h3 class="article-title header">'.apply_filters('mp_article_title',$value['title']).'</h3>'.$time.'</header><div class="content">'.$filtered_content.'</div></section>';
                            $content.= mp_content_block('article',false,false,$just_content);
                        }
                    }elseif($style=='li'){
                        if(empty($perma)){ $perma = '?obj='.$direct_object_id; }
                        if($perma==$this_slug){
                            $just_content = '<a href="'.trailingslashit($mp_options['root_url'].$this_slug).'" class="'.$value['type'].'" '.mp_get_attr_filter('themes.php','a',trailingslashit($mp_options['root_url'].$this_slug),false,$value['type'],false).'>'.$value['title'].'</a>';
                            $content.= mp_content_block('li',false,'current',$just_content);
                        }else{
                            $just_content = '<a href="'.trailingslashit($mp_options['root_url'].$this_slug).'" class="'.$value['type'].'" '.mp_get_attr_filter('themes.php','a',trailingslashit($mp_options['root_url'].$this_slug),false,$value['type'],false).'>'.$value['title'].'</a>';
                            $content.= mp_content_block('li',false,false,$just_content);
                        }
                    }elseif($style=='a'){
                        $this_url = trailingslashit(trim($value['content']));
                        if(($direct_object_id)||($mp_options['skip_htaccess'])){
                            $this_slug = trim($value['content']);
                            $this_slug_array = $mp->arrayed($slugs->find(array("slug"=>$this_slug)));
                            if(is_array($this_slug_array)){
                                $this_slug_id = $this_slug_array[0]['_id'];
                                $this_slug_mongo_id = $mp->get_mongoid_as_string($this_slug_id);
                                $this_object_array = $mp->arrayed($objs->find(array("slug_id"=>$this_slug_mongo_id)));
                                $this_object_id = $mp->get_mongoid_as_string($this_object_array[0]["_id"]);
                                $this_url = '?obj='.$this_object_id;
                                if((empty($perma))&&(empty($direct_object_id))){
                                    if((trim($value['content'])=='/')||(trim($value['content'])=='/')){
                                        $class='current';
                                    }else{ $class=''; }
                                }else{ 
                                    if($direct_object_id){
                                        $this_slug = $value['content'];
                                        $this_slug_id_array = $mp->arrayed($slugs->find(array("slug"=>$this_slug)));
                                        if(!empty($this_slug_id_array[0])){
                                            $this_slug_id = $mp->get_mongoid_as_string($this_slug_id_array[0]["_id"]);
                                            $this_object_array = $mp->arrayed($objs->find(array("slug_id"=>$this_slug_id)));
                                            if(!empty($this_object_array[0])){
                                                $this_object_array_id = $mp->get_mongoid_as_string($this_object_array[0]["_id"]);
                                            }
                                        }
                                        if($this_object_array_id==$direct_object_id){
                                            $class='current';
                                        }else{ $class=''; }
                                    }else{
                                        $class='';
                                    }
                                }
                            }else{
                                if(empty($direct_object_id)){ if(trim($value['content'])=='//'){ $class='current'; }}
                            }
                        }else{
                            if(trim($value['content'])==$perma){
                                $class='current';
                            }else{
                                if((trim($value['content'])=='/')||(trim($value['content'])=='//')){
                                    if(!$perma){
                                        $class='current';
                                    }else{ $class=''; }
                                }else{ $class=''; }
                            }
                        }
                        if((strstr($this_url,'http://'))||(strstr($this_url,'https://'))){
                            if($add_lis){
                                $just_content = '<a href="'.$this_url.'" class="'.$class.' '.$value['type'].'" '.mp_get_attr_filter('themes.php','a',$this_url,false,$class.' '.$value['type'],false).'>'.$value['title'].'</a>';
                                $content.= mp_content_block('li',false,false,$just_content);
                            }else{
                                $just_content = $value['title'];
                                $content.= mp_content_block('a',false,$class,$just_content,$this_url);
                            }
                        }else{
                            if($add_lis){
                                $just_content = '<a href="'.trailingslashit($mp_options['root_url'].$this_url).'" class="'.$class.' '.$value['type'].'" '.mp_get_attr_filter('themes.php','a',$this_url,false,$class.' '.$value['type'],false).'>'.$value['title'].'</a>';
                                $content.= mp_content_block('li',false,false,$just_content);
                            }else{
                                $just_content = $value['title'];
                                $content.= mp_content_block('a',false,$class,$just_content,trailingslashit($mp_options['root_url'].$this_url));
                            }
                        }
                    }elseif($style=='array'){
                        $content_array[]=$value;
                    }elseif($style=='content'){
                        $content.=$value['content'];
                    }else{
                        $content = __('Unsupported Content Style');
                    }
                }else{
                    if(empty($this_content)){
                        if($action=='query'){
                            $this_content = '<header class="title"><h3 class="article-title header">'.__('QUERY RESULTS:').'</h3></header>';
                            $this_content = mp_content_block('article',false,false,$this_content);
                        }elseif($action=='search'){
                            $this_content = '<header class="title"><h3 class="article-title header">'.__('SEARCH RESULTS:').'</h3></header>';
                            $this_content = mp_content_block('article',false,false,$this_content);
                        }else{
                            $this_content = '';
                        }
                        $content = $this_content;
                    }
                    if(($action=='query')||($action=='search')){
                        $filtered_content = apply_filters('mp_article_excerpt',mp_html_excerpt($mp->mp_sensible_formatting_filter($value['content']),255));
                        $just_content = '<section class="query"><header class="title"><h3 class="article-title header">'.$value['title'].'</h3></header><div class="content">'.$filtered_content.'</div></section>';
                        $content.= mp_content_block('article',false,false,$just_content);
                    }else{
			$published_ago_distance = mingo_meantime($value['updated']);
			$published_date = mingo_meantime(false,false,'datetime');
                        $published_ago = sprintf(__('( Last updated %s ago - %s )'),$published_ago_distance ,  $published_date);
                        $time = '<time datetime="'.$value['updated'].'">'.$published_ago.'</time>';
                        $shortcoded_content = apply_filters('mp_shortcodes', $value['content']);
                        if($direct_object_id){
                            $this_mongo_id = $direct_object_id;
                        }else{
                            $this_mongo_id = $mp->get_mongoid_as_string($value['_id']);
                        }
                        if(($direct_object_id==$this_mongo_id)&&(!empty($direct_object_id))){
                            $this_content = '<section class="content"><header class="title"><h3 class="article-title header">'.$value['title'].'</h3>'.$time.'</header><div class="content">'.$shortcoded_content.'</div></section>';
                            $this_content = mp_content_block('article',false,false,$this_content);
                            $content.= $this_content;
                        }elseif(($value['slug_id']==$current_slug_id)&&(!empty($current_slug_id))){
                            if($style=='array'){
                                $this_content = $value[0];
                            }else{
                                $this_content = '<section class="content"><header class="title"><h3 class="article-title header">'.$value['title'].'</h3>'.$time.'</header><div class="content">'.$shortcoded_content.'</div></section>';
                                $this_content = mp_content_block('article',false,false,$this_content);
                                $content.= $this_content;
                            }
                        }else{
                            $content = false;
                        }
                    }
                }
            }
        }
    }
    $this_mongo_id = (isset($this_mongo_id)) ? $this_mongo_id : '';
    //-> THIS NOW GETS DONE IN find_one() FUNCTION ...? $content = apply_filters('mp_get_content',$content,$this_mongo_id);
    if(empty($content)){
		if (!headers_sent()) {
			/* TODO: WAITING FOR BETTER OBJECT METHOD */
      //TODO
      $status = 'HTTP/1.0 404 Not Found';
      $status = apply_filters('mp_header_status_missing_content', $status);
      header($status);
		}
        $content = __('No Object Matching Requested Type') . ': <strong>' . $type . '</strong>';
    }
    $content_array = apply_filters('mp_get_content_array',$content_array);
    if(empty($content_array)){
        return $content;
    }else{
        return $content_array;
    }
}
function mp_content($options=false,$force_lis=false){
    echo mp_get_content($options,$force_lis);
}
