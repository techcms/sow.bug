<?php
add_filter('mp_theme_file_perma', 'rss_display_perma', 1,2);
add_action('mp_content_block_content_article_rss','mp_rss',1,5);
add_filter('mp_header_status_missing_content', 'rss_head_status');

function rss_head_status($status) {
        $mp = mongopress_load_mp();
        $these_options = array(
        'action'    => 'get',
        'key'       => 'rss'
        );

        $plugin_options = $mp->plugin_options($these_options);

        $mp_perma = mongopress_load_perma();
        $perma = $mp_perma->current();
        if ($perma == $plugin_options['feed_slug']) {
					return 'HTTP/1.0 200 OK';
        } else return $status;
}


function mp_rss_plugin_dropdown($id='', $class='', $value=''){
    $m = mongopress_load_m();
    $mp = mongopress_load_mp();
    $default_options = $mp->options();
    $db = $m->$default_options['db_name'];
    $objects = $db->command(array("distinct"=>$default_options['obj_col'],"key"=>"type"));
    $object_types = $objects['values'];
    if(!empty($object_types)){
            echo '<select id="type" name="rss_'.$id.'" class="'.$class.'" data-key="rss_object_type" id="rss_"'.$id.'" autocomplete="off">';
            foreach ($object_types as $type) {
								if ($type === $value) {
									echo '<option value="'.$type.'" selected>'.$type.'</option>';
								} else {
									echo '<option value="'.$type.'">'.$type.'</option>';
								}
            }
            echo '</select>';
    } else {
        echo '<input id="type" name="rss_'.$id.'" placeholder="'.__('Define your object type').'" class="'.$class.'" autocomplete="off" />';
    }
}


function mp_rss($content,$type,$id,$class,$href){
    $mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => $id
    );
    $plugin_options = $mp->plugin_options($these_options);
    if(isset($plugin_options['feed_slug'])){ $feed_slug = $plugin_options['feed_slug']; }else{ $feed_slug = false; }
	if(isset($plugin_options['rss_cache_time'])){ $cache_time = $plugin_options['rss_cache_time']; }else{ $cache_time = false; }
	if(isset($plugin_options['rss_object_type'])){ $object_type = $plugin_options['rss_object_type']; }else{ $object_type = false; }
    ob_start();
    ?>
    <form id="plugin-<?php echo $id; ?>" class="mp-form mu-plugins" data-plugin="rss">
		<label for="slug_<?php echo $id; ?>"><?php _e('RSS Slug'); ?></label>
		<span class="input-wrapper radius5">
			<input class="blanked these_values" id="slug_<?php echo $id; ?>" data-key="feed_slug" name="slug_<?php echo $id; ?>" placeholder="<?php _e('Used for location of feed'); ?>" value="<?php echo $feed_slug; ?>" />
		</span>
		<label for="rss_<?php echo $id; ?>"><?php _e('RSS cache time in minute'); ?></label>
		<span class="input-wrapper radius5">
			<input class="blanked these_values" id="object_id_<?php echo $id; ?>" data-key="rss_cache_time" name="object_id_<?php echo $id; ?>" placeholder="<?php _e('Please manually add the relevant cache time in minute'); ?>" value="<?php echo $cache_time; ?>" />
		</span>
		<label for="type"><?php _e('RSS Object Type:'); ?></label>
		<span class="input-wrapper radius5">
			<?php mp_rss_plugin_dropdown($id, 'blanked these_values', $object_type); ?>
		</span>
		<input type="submit" id="submit_<?php echo $id; ?>" class="button" value="<?php _e('Save Plugin Settings'); ?>" />
        <?php mp_nonce_field('plugin-options','mp_nonce'); ?>
    </form>
	<div style="clear:both; display: block;"></div>
    <?php
    $options = ob_get_clean();
    return $options;
}


function rss_display_perma($file, $perma) {
	$mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => 'rss'
    );
    $plugin_options = $mp->plugin_options($these_options);
	if(isset($plugin_options['feed_slug'])){ $feed_slug = $plugin_options['feed_slug']; }else{ $feed_slug = false; }
	if (($perma == $feed_slug) && ($perma)) {
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'feed.php';
		if (file_exists($file)) {
			return $file;
		} else {
			header("HTTP/1.0 404 Not Found");
			$error = 'Sorry, the page you requested was not found';
			mongopress_pretty_page($error,__('MongoPress Plugin Error'),true);
			exit;
		}	
	}
}

function rss_display() {
    $mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => 'rss'
    );

    $plugin_options = $mp->plugin_options($these_options);
	if(isset($plugin_options['rss_cache_time'])){ $cache_time = $plugin_options['rss_cache_time']; }else{ $chache_time = false; }
	if(isset($plugin_options['rss_object_type'])){ $object_type = $plugin_options['rss_object_type']; }else{ $object_type = false; }
	if(isset($plugin_options['feed_slug'])){ $feed_slug = $plugin_options['feed_slug']; }else{ $feed_slug = false; }

    if (!empty($feed_slug)){

		$m = mongopress_load_m();
		$options = $mp->options();
		$db = $m->$options['db_name'];
		$objs = $db->$options['obj_col'];
		$limit = $options['objects_per_page'];
		
		$default_options = array(
			'type'          => $object_type,
			'style'         => 'array',
			'order_by'      => '_id',
			'limit'         => $limit,
			'order'         => false,
			'can_query'     => false,
			'near'          => false
		);

		$data = mp_get_content($default_options);

		//Add caching support
		if(!empty($cache_time)){
			$cache_dir = $options['root_path'] .'mp-cache/rss/';
			if (!file_exists($cache_dir)) {
				mkdir($cache_dir, 0777);
				$cache_time = $cache_time * 60 * 24; // 24 hours
				$cache_file = $cache_dir.'/rss.rss';
				$timedif = @(time() - filemtime($cache_file));
			}
		}else{
			$cache_file = false;
		}

		$rssfeed = '';

		if (@file_exists($cache_file) && $timedif < $cache_time) {
				$rssfeed = file_get_contents($cache_file);
				echo $rssfeed;
		} else {

			$todays_date = date('Y-m-d\TH:i:sP'); //convert timestamp to readable format

			header("Content-Type: application/rss+xml; charset=UTF-8");
			$rssfeed .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
			$rssfeed .= "\n".'<feed xmlns="http://www.w3.org/2005/Atom"
							xmlns:dc="http://purl.org/dc/elements/1.1/"
							xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
							xmlns:georss="http://www.georss.org/georss"
							xmlns:media="http://search.yahoo.com/mrss/">';
			$rssfeed .= "\n"."\n".'<title>'.sprintf(__('%s | RSS Feed'),$options['site_name']).'</title>';
			$rssfeed .= "\n".'<link rel="self" href="'.$options['full_url'].$feed_slug.'" />';
			$rssfeed .= "\n".'<link rel="alternate" type="text/html" href="'.$options['full_url'].'" />';
			$rssfeed .= "\n".'<subtitle>' .$options['site_description'].'</subtitle>';
			$rssfeed .= "\n".'<updated>'.$todays_date.'</updated>';
			$rssfeed .= "\n".'<generator uri="'.$options['full_url'].'">'.$options['site_name'].'</generator>';
			$rssfeed .= "\n".'<id>'.$options['full_url'].'</id>'."\n";

			foreach($data as $key => $value) {
			  if (is_array($value)) {

				$slug = $mp->get_slug($value['slug_id']);
				$title = strip_tags($value['title']);
				$content = strip_tags($value['content']);
				$date = $value['created']; //date timestamp from db
				$date = new DateTime("@$date"); //convert timestamp to readable format
				$date = $date->format('Y-m-d\TH:i:sP');
				$updated = $value['updated'];
				$updated = new DateTime("@$updated");
				$updated = $updated->format('Y-m-d\TH:i:sP');
				$desc = truncateText(mp_filter_remove_shortcodes($content), 250);
				$object_id = $mp->get_mongoid_as_string($value['_id']);

				if(isset($value['points']['lng'])){ $this_lng = $value['points']['lng']; }else{ $this_lng = false; }
				if(isset($value['points']['lat'])){ $this_lat = $value['points']['lat']; }else{ $this_lat = false; }

				$rssfeed .= "\n".'<entry>';
				$rssfeed .= "\n".'<id>'.$options["full_url"].$slug.'</id>';
				$rssfeed .= "\n".'<title type="html">'._mp_specialchars($title).'</title>';
				$rssfeed .= "\n".'<content>'.$desc.'</content>';
				$rssfeed .= "\n".'<link rel="alternate" type="text/html" href="'.$options["full_url"].$slug.'"/>';
				$rssfeed .= "\n".'<published>' . $date . '</published>';
				$rssfeed .= "\n".'<updated>' . $updated . '</updated>';
				$rssfeed .= "\n".'<author>';
					$rssfeed .= "\n".'<name>'.$options['site_name'].'</name>';
					$rssfeed .= "\n".'<uri>'.$options['full_url'].'</uri>';
				$rssfeed .= "\n".'</author>';
				if((!empty($this_lng))&&(!empty($this_lat))){
					$rssfeed .= "\n".'<georss:point>'.$this_lat.' '.$this_lng.'</georss:point>';
					$rssfeed .= "\n".'<geo:lat>'.$this_lat.'</geo:lat>';
					$rssfeed .= "\n".'<geo:long>'.$this_lng.'</geo:long>';
				}
				$rssfeed .= "\n".'</entry>'."\n";

			}
		}
		$rssfeed .= "\n".'</feed>';
		echo $rssfeed;
		//save to file for caching? mp-cache folder??
		if(!empty($cache_time)){
			file_put_contents($cache_file, $rssfeed);
		}
	  }
   }
}

function truncateText($string, $limit, $break=".", $pad="...") {
// return with no change if string is shorter than $limit
if(strlen($string) <= $limit) return $string; 

// is $break present between $limit and the end of the string?
if(false !== ($breakpoint = strpos($string, $break, $limit))) {
 if($breakpoint < strlen($string) - 1) {
  $string = substr($string, 0, $breakpoint) . $pad;
 }
}
return $string;
}
