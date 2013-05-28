<?php
$mp = mongopress_load_mp();
$current_user_info = $mp->get_current_user();
$user_id = $mp->get_mongoid_as_string($current_user_info["_id"]);
$plupload_options = array(
	'max'			=> 1,
	'action'		=> 'avatar',
	'user_id'		=> $user_id
);
?>

<div class="admin-widget-wrapper" data-admin-widget-type="contributors">
	<h3 class="admin-widget-title"><?php _e('People Who Have Contributed to Core:'); ?></h3>
	<div class="admin-widget" id="admin-widget-contributors">
		<?php echo 'Fetching contributor list via AJAX, please be patient...' ?>
	</div>
</div>