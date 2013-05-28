<?php
$mp = mongopress_load_mp();
$current_user_info = $mp->get_current_user();
$user_id = $mp->get_mongoid_as_string($current_user_info["_id"]);
$gallery_options = array(
	'max'			=> 10,
	'action'		=> 'gallery',
	'user_id'		=> $user_id
);
?>

<div class="admin-widget-wrapper onethird right" data-admin-widget-type="add-media" id="admin-widget-add-media">
	<h3 class="admin-widget-title"><?php _e('Add Media'); ?></h3>
	<div class="admin-widget">
		<?php mp_plupload($gallery_options); ?>
	</div>
</div>

<div class="admin-widget-wrapper twothirds right" data-admin-widget-type="media-gallery">
	<h3 class="admin-widget-title"><?php _e('Media Gallery'); ?></h3>
	<div class="admin-widget">
		<?php mp_media_gallery(); ?>
	</div>
</div>