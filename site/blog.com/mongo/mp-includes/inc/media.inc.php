<?php

function mp_plupload($options = false){
	$default_options = array(
		'id'		=> 'plupload',
		'max'		=> 5,
		'url'		=> false,
		'nonce'		=> false,
		'action'	=> false,
		'user_id'	=> false
	);
	if(is_array($options)){
		$settings = array_merge($default_options,$options);
	}else{
		$settings = $default_options;
	}
	$id = $settings['id'];
	$max_files = $settings['max'];
	$url = $settings['url'];
	$nonce = $settings['nonce'];
	$action = $settings['action'];
	$user_id = $settings['user_id'];
	$mp = mongopress_load_mp(); $options = $mp->options();
	if(empty($url)){ $url = $options['root_url'].'mp-includes/pjax/plupload.php'; }
	if(empty($nonce)){ $nonce = mp_create_nonce('mp_plupload'); }
	/* NEEDED FOR ADMIN */
	mp_enqueue_style_admin('plupload', $options['root_url'].'mp-admin/js/plupload/css/jquery.plupload.queue.css', false);
	mp_enqueue_style_admin('jcrop', $options['root_url'].'mp-admin/css/jcrop.css', false);
	mp_enqueue_script_admin('plupload', $options['root_url'].'mp-admin/js/plupload/plupload.js', false);
	mp_enqueue_script_admin('plupload_html4', $options['root_url'].'mp-admin/js/plupload/plupload.html4.js', array('plupload'));
	mp_enqueue_script_admin('plupload_html5', $options['root_url'].'mp-admin/js/plupload/plupload.html5.js', array('plupload'));
	mp_enqueue_script_admin('jcrop', $options['root_url'].'mp-admin/js/jcrop.js', array('plupload'));
	mp_enqueue_script_admin('plupload_jquery_que', $options['root_url'].'mp-admin/js/plupload/jquery.plupload.queue.js', array('plupload'));
	mp_enqueue_script_admin('plupload_mp', $options['root_url'].'mp-admin/js/plupload/plupload_mp.js', array('plupload_jquery_que'));
	/* NEEDED FOR THEME */
	mp_enqueue_style_theme('plupload', $options['root_url'].'mp-admin/js/plupload/css/jquery.plupload.queue.css', false);
	mp_enqueue_style_theme('jcrop', $options['root_url'].'mp-admin/css/jcrop.css', false);
	mp_enqueue_script_theme('plupload', $options['root_url'].'mp-admin/js/plupload/plupload.js', false);
	mp_enqueue_script_theme('plupload_html4', $options['root_url'].'mp-admin/js/plupload/plupload.html4.js', array('plupload'));
	mp_enqueue_script_theme('plupload_html5', $options['root_url'].'mp-admin/js/plupload/plupload.html5.js', array('plupload'));
	mp_enqueue_script_theme('jcrop', $options['root_url'].'mp-admin/js/jcrop.js', array('plupload'));
	mp_enqueue_script_theme('plupload_jquery_que', $options['root_url'].'mp-admin/js/plupload/jquery.plupload.queue.js', array('plupload'));
	mp_enqueue_script_theme('plupload_mp', $options['root_url'].'mp-admin/js/plupload/plupload_mp.js', array('plupload_jquery_que'));
	$this_function = "mp_plupload_hook_$id";
	$eval_action = "function $this_function(){ ?> <script> mp_plupload('$id', $max_files, '$url', '$nonce', '$action', '$user_id') </script> <?php }";
	eval($eval_action);
	if(function_exists($this_function)){
		add_action('inline_mp_plupload', "$this_function");
		add_action('mp_after_admin_footer', "$this_function");
		add_action('mp_footer', "$this_function");
	}

    if($settings['action']=='avatar'){
		echo '<form action="" method="post" onsubmit="return checkCoords();">';

		if ($_SERVER['REQUEST_METHOD'] == 'POST'){

			$targ_w = $targ_h = 150; // resize upto
			$jpeg_quality = 9;

            $tmp_name = sys_get_temp_dir() .'/'. $_POST['plupload_0_tmpname']; // success assumed.
            if (! copy( $_POST['mp-img-src'], $tmp_name)) {
                $progress['success'] = false;
        	    $progress['message'] = __('Tmp dir problems');
                die();
            }
			
			$this_img_type = $_POST['mp-img-type'];
			if(($this_img_type=='jpg')||($this_img_type=='jpeg')){
				$img_r = imagecreatefromjpeg($tmp_name);
			}elseif($this_img_type=='png'){
				$img_r = imagecreatefrompng($tmp_name);
			}elseif($this_img_type=='gif'){
				$img_r = imagecreatefromgif($tmp_name);
			}else{
				$progress['success'] = false;
        	    $progress['message'] = __('Unsupported Image Format');
                die();
			}

			$edited_file = ImageCreateTrueColor( $targ_w, $targ_h );

            // Preserves transparency.
            imagealphablending( $edited_file, false );
            imagesavealpha( $edited_file, true );

            // TODO - verify dimensions - we need to know cropped correctly.
			imagecopyresampled($edited_file,$img_r, 0, 0,$_POST['x'],$_POST['y'], $targ_w,$targ_h,$_POST['w'],$_POST['h']);

			//header('Content-type: image/jpeg');
			//imagejpeg($dst_r,null,$jpeg_quality);
            
            $result_file =  sys_get_temp_dir() .'/'. $_POST['plupload_0_name'];

			if(($this_img_type=='jpg')||($this_img_type=='jpeg')){
				imagejpeg($edited_file,$result_file, $jpeg_quality);
			}elseif($this_img_type=='png'){
				imagepng($edited_file,$result_file, $jpeg_quality);
			}elseif($this_img_type=='gif'){
				imagegif($edited_file,$result_file, $jpeg_quality);
			}else{
				$progress['success'] = false;
        	    $progress['message'] = __('Unsupported Image Format');
                die();
			}

			imagedestroy($edited_file);

			// $progress = media_add_to_grid($result_file,'avatar_'.$GLOBALS['_MP']['COOKIE']['mp_user_id'].'.png');
            // hard coded to a avatar file name - has conflict potential.
			// $progress = mp_upload_media($result_file, 'avatar', $GLOBALS['_MP']['COOKIE']['mp_user_id']);
			$latest_progress = mp_update_grid_avatar($result_file,$_POST['plupload_0_name'],$GLOBALS['_MP']['COOKIE']['mp_user_id'],$_POST['mp-original-id']);
			if($latest_progress[$_POST['plupload_0_name']]['success']){
				echo '<span class="notification success">'.__('Successfully Edited Avatar').'</span>';
			}

		}
		echo '<input type="hidden" id="ratio" name="ratio" />';
		echo '<input type="hidden" id="x" name="x" />';
		echo '<input type="hidden" id="y" name="y" />';
		echo '<input type="hidden" id="w" name="w" />';
		echo '<input type="hidden" id="h" name="h" />';
		echo '<input type="hidden" id="mp-img-src" name="mp-img-src" />';
		echo '<input type="hidden" id="mp-img-type" name="mp-img-type" />';
		echo '<input type="hidden" id="mp-original-id" name="mp-original-id" />';
	}
	echo '<div id="'.$id.'">'.__('This browser does not support media uploading.').'</div>';
	do_action('inline_mp_plupload');
	if($settings['action']=='avatar'){
		echo '</form>';
	}
}

function mp_update_grid_avatar($tmpfile,$filename,$user_id,$original_id){
	/* GET CORE */
	$mp = mongopress_load_mp();
	$m = mongopress_load_m();
	$mp_options = $mp->options();
	$db = $m->$mp_options['db_name'];

    $data = getimagesize($tmpfile);

	$media_meta = array(
		'filename'  => $filename,
		'actual'    => $filename,
		'type'      => $data['mime'],
		'downloads' => 0,
		'views'     => 0
	);

    $file['type'] = $data['mime'];

	if(
		($file['type']!='image/png') && ($file['type']!='image/gif')
		&& ($file['type']!='application/download') && ($file['type']!='application/x-zip')
        && ($file['type']!='application/octet-stream') && ($file['type']!='application/zip')
        && ($file['type']!='image/jpg') && ($file['type']!='image/jpeg'))
    {
    	$progress['success'] = false;
	    $progress['message'] = __('Unsupported Media Type');
        return $progress;
    }


	$grid = $db->getGridFS();
    /*
	$image = $grid->findOne(
            array("filename" => $filename)
	);
    */
	$original_mongo_id = new MongoId($original_id);
	$was_deleted = $grid->delete($original_mongo_id);
	$mp_media_object_id = $grid->put($tmpfile, $media_meta);

	$these_options = array(
		'id'		=> $user_id,
		'avatar'	=> $mp->get_mongoid_as_string($mp_media_object_id)
	);
	/* RUN FIRST ROUND OF CHECKS */
	$avatar_progress = $mp->user_options($these_options);
	if($avatar_progress['success']){
		$progress[$filename]['success'] = true;
		$progress[$filename]['message'] = __('Successfully Added Avatar');
	}else{
		$progress[$filename]['success'] = false;
		$progress[$filename]['message'] = __('Unable to upload avatar');
		if(isset($avatar_progress['message'])){
			if(!empty($avatar_progress['message'])){
				$progress[$filename]['message'] = $avatar_progress['message'];
			}
		}
	}
	return $progress;
}

function media_add_to_grid($tmpfile,$filename){
	/* GET CORE */
	$mp = mongopress_load_mp();
	$m = mongopress_load_m();
	$mp_options = $mp->options();
	$db = $m->$mp_options['db_name'];

        
    $data = getimagesize($tmpfile);
    
	$media_meta = array(
		'filename'  => $filename,
		'actual'    => $filename,
		'type'      => $data['mime'],
		'downloads' => 0,
		'views'     => 0
	);

    $file['type'] = $data['mime'];
 
	if( ! ($file['type']=='image/png') ||($file['type']=='image/gif')||
            ($file['type']=='application/download')||($file['type']=='application/x-zip')
            ||($file['type']=='application/octet-stream')||($file['type']=='application/zip')
            ||($file['type']=='image/jpg')||($file['type']=='image/jpeg')) 
    {
    	$progress['success'] = false;
	    $progress['message'] = __('Unsupported Media Type');
        return $progress;    
    }


	$grid = $db->getGridFS();
    /*
	$image = $grid->findOne(
            array("filename" => $filename)
	);
    */ 
	$mp_media_object_id = $grid->put($tmpfile, $media_meta);		

    $id = (string)$mp_media_object_id;
    if ($id) { // blindly dump into gridfs.
		$progress['success'] = true;
		$progress['message'] = __('Successfully Uploaded Media');
	} else {
		$progress['success'] = false;
		$progress['message'] = __('Unknown Error Uploading Media');
	}

	return $progress;
}




function mp_upload_media($file, $action, $user_id){
	/* GET CORE */
	$mp = mongopress_load_mp();
	$m = mongopress_load_m();
	$mp_options = $mp->options();
	$db = $m->$mp_options['db_name'];
        
	/* HARDCODE SOME STUFF - TODO: UN-HARDCODE */
	$files = $_FILES;
	$limit_size = 1000000;
	$mb_limit = $limit_size/1000000;
	$title = false; // for now

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
	//foreach($files as $file) {
		$mp->dump($file);
		if($file["size"] > 1) {
			if(($file['type']=='image/png')||($file['type']=='image/gif')||($file['type']=='application/download')||($file['type']=='application/x-zip')||($file['type']=='application/octet-stream')||($file['type']=='application/zip')||($file['type']=='image/jpg')||($file['type']=='image/jpeg')){
				$grid = $db->getGridFS();
				$image = $grid->findOne(
					array("filename" => $filename)
				);
				$display_error = false;
				if(isset($image->file['length'])){
					if(($image->file['length']==$file['size'])||($image->file['actual']==$actual_name)){
						$display_error = true;
					}
				} if($display_error){
					$progress[$filename]['success'] = false;
					$progress[$filename]['message'] = __('This File Already Exists');
				}else{
					$mp_media_object_id = $grid->put($media_object['tmp_name'],$media_meta);
					if(!empty($mp_media_object_id)){
						if($action=='avatar'){
							$these_options = array(
								'id'		=> $user_id,
								'avatar'	=> $mp->get_mongoid_as_string($mp_media_object_id)
							);
							/* RUN FIRST ROUND OF CHECKS */
							$avatar_progress = $mp->user_options($these_options);
							if($avatar_progress['success']){
								$progress[$filename]['success'] = true;
								$progress[$filename]['message'] = __('Successfully Added Avatar');
							}else{
								$progress[$filename]['success'] = false;
								$progress[$filename]['message'] = __('Unable to upload avatar');
								if(isset($avatar_progress['message'])){
									if(!empty($avatar_progress['message'])){
										$progress[$filename]['message'] = $avatar_progress['message'];
									}
								}
							}
						}else{
							$progress[$filename]['success'] = true;
							$progress[$filename]['message'] = __('Successfully Uploaded Media');
						}
					}else{
						$progress[$filename]['success'] = false;
						$progress[$filename]['message'] = __('Unknown Error Uploading Media');
					}
				}
			}else{
				$progress[$filename]['success'] = false;
				$progress[$filename]['message'] = __('Unsupported Media Type');
			}
		}
	//}
	return $progress;
}
