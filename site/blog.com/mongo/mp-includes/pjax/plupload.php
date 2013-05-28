<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* GET POSTED INFO */
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; }
if(isset($_POST['action'])){ $action = sanitize_text_field($_POST['action']); }else{ $action = 'gallery'; }
if(isset($_POST['id'])){ $id = sanitize_text_field($_POST['id']); }else{ $id = false; }

/* CHECK NONCE */
mp_json_nonce_check($nonce,'mp_plupload');

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

if($action=='avatar'){
	$user_id = $id;
}

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
				if(($image->file['length']==$file['size'])||($image->file['actual']==$actual_name)){
					$display_error = true;
				}
			} if($display_error){
				$progress[$filename]['success'] = false;
				$progress[$filename]['message'] = __('This File Already Exists');
			}else{
				$mp_media_object_id = $grid->put($media_object['tmp_name'],$media_meta);
				if(!empty($mp_media_object_id)){
					$progress[$filename]['success'] = true;
					$progress[$filename]['original_id'] = $mp_media_object_id;
					$progress[$filename]['message'] = __('Successfully Uploaded Media');
					$progress[$filename]['type'] = $file['type'];
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
}

// RETURN RESULTS
mp_json_send($progress);