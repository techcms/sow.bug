<?php
require_once(dirname(dirname(__FILE__)).'/mp-includes/includes.php');

/* use to allow refresh of content? */
/*
if (isset($_GET['import'])) $import = $_GET['import']; else $import = false;
if (!$import){
	mongopress_pretty_page(__('Ming the Merciless has terminated this action - What is your problem Earthling?'),__('MongoPress Error'));
	exit;
}
*/

try{

    $mp = mongopress_load_mp(); $options = $mp->options();
 
    $got_content_folder = false;
	$content_folder = $_MP['THEME_ROOT'].'/'.$options['theme'].'/content/';
	$allow_install = false;

    if($folders = opendir($content_folder)){
		$got_content_folder = true;
		$m = mongopress_load_m();
		$db = $m->$options['db_name'];
		$objects = $db->$options['obj_col'];
		$object_count = $objects->find()->count();
		if($object_count>0){
			$title = __('Content Already Added');
		} else {
			$title = __('Import Content from Theme');
			$allow_install = true;
		}
	} else $title = __('No Content Templates');
	
	if ($got_content_folder && $allow_install) {
		$import_button = '<form id="mp-install" class="mp-form"><br style="clear:both; display:block;"><p><input type="button" id="mp-import-html" class="button" value="IMPORT CONTENT" /></p><br style="clear:both; display:block; ">'.mp_nonce_field('mp-install','mp_nonce',true,false).'</form>';
		$obj_import_error_str = __('OBJECT IMPORT ERROR: ');
		$obj_import_success_str = __('OBJECTS IMPORTED');
		$obj_import_file_error_str = __('ERROR FINDING IMPORT FILES');

$js =<<<EOJS
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
<script>
	var mp_root_url = '{$_MP['HOME']}';
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
						if (result.success!=true) {
							alert('$obj_import_error_str'+result.message);
						} else {
							alert('$obj_import_success_str');
							window.location = mp_root_url;
						}
			},
			failure: function(){
				alert('$obj_import_file_error_str');
			}
		});
});
</script>
EOJS;

		$html = $import_button . $js;
		mongopress_simple_page($title, $html);
	}
} catch (Exception $e) {
    $error = sprintf(__('Error: %s'),getClass($e) .' '.$e->getMessage());
    mongopress_pretty_page($error,__('MongoDB Error'),true);
}
