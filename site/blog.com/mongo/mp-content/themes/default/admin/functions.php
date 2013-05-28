<?php
function mp_themed_admin_page($page=false){
	get_template_part('header');
	$mp = mongopress_load_mp();
	$mp_options = $mp->options();
	$logged_in = $mp->is_logged_in();
	$table_options = array(
		'allow_edits'   => false
	);
	$form_options = array(
        'force_css'                 => true,
        'force_js'                  => true,
        'object_type_dropdown'      => true,
        'allow_new_object_types'    => true,
        'allow_custom_fields'       => true
    );
	if($logged_in){
		ob_start();
		if($page=='add'){
			mp_add_object_form($form_options);
		}elseif($page=='media'){
			echo '<div style="width:70%;display:inline-block;vertical-align:top" class="themed-admin-left-side jdash-item">';
				echo '<h3 class="form-title">'.__('Current Media Gallery').'</h3>';
				mp_media_gallery();
			echo '</div>';
			echo '<div class="themed-admin-right-side" style="width:28%; padding-left:2%; display:inline-block; vertical-align:top">';
				echo '<h3 class="form-title">'.__('Add New Media').'</h3>';
				$current_user_info = $mp->get_current_user();
				$user_id = $mp->get_mongoid_as_string($current_user_info["_id"]);
				$gallery_options = array(
					'max'			=> 10,
					'action'		=> 'gallery',
					'user_id'		=> $user_id
				);
				mp_plupload($gallery_options);
			echo '</div>';
		}elseif($page=='options'){
			mp_misc_options_form();
		}else{
			mp_objects_table($table_options);
		}
		$content = ob_get_clean();
	}else{
		$content = '<p style="text-align:center;font-weight:bold;">'.__('Unidentified object in the imperial vortex!').'</p>';
		$content.= '<p style="text-align:center;">'.__('( You do not have permission to view the contents of this page )').'</p>';
	}
	?>

	<div id="site-wrapper" class="radius5">
		<div id="primary-content" class="full">
			<nav id="primary-navigation">
				<a href="<?php echo $mp_options['admin_url']; ?>" <?php if($page=='dashboard'){ echo 'class="current"'; }; ?>><?php _e('Admin Dashboard'); ?></a>
				<a href="<?php echo $mp_options['admin_url']; ?>add/" <?php if($page=='add'){ echo 'class="current"'; }; ?>><?php _e('Add Object'); ?></a>
				<a href="<?php echo $mp_options['admin_url']; ?>media/" <?php if($page=='media'){ echo 'class="current"'; }; ?>><?php _e('Media Gallery'); ?></a>
				<a href="<?php echo $mp_options['admin_url']; ?>options/" <?php if($page=='options'){ echo 'class="current"'; }; ?>><?php _e('Misc Options'); ?></a>
				<a href="<?php echo $mp_options['root_url']; ?>"><?php printf(__('Return to %s'), $mp_options['site_name']); ?></a>
			</nav>
			<?php echo $content; ?>
		</div>
	</div>

	<?php
}