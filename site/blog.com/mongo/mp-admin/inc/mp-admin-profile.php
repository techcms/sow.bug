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

<div class="admin-widget-wrapper twothirds" data-admin-widget-type="user-profile">
	<h3 class="admin-widget-title"><?php _e('Your Profile'); ?></h3>
	<div class="admin-widget">
		<?php mp_user_options_form(); ?>
	</div>
</div>

<div class="admin-widget-wrapper onethird" data-admin-widget-type="your-avatar" data-avatar-nonce="<?php echo mp_create_nonce('public-avatar','public'); ?>" data-user-id="<?php echo $user_id; ?>" id="admin-widget-your-avatar">
	<h3 class="admin-widget-title"><?php _e('Your Avatar'); ?></h3>
	<div class="admin-widget">
		<p class="notice"><?php _e('Please note that the avatar uploading and cropping functionality is currently in very early beta development and that its improvement will be on the top of our list for the next major update.'); ?></p>
		<p class="notice"><?php _e('At the moment, this system only works with files that have no spaces in their names and are all in lowercase.'); ?></p>
		<?php mp_plupload($plupload_options); ?>
	</div>
</div>