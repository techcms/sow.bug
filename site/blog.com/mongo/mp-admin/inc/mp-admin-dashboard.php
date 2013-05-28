<?php

/* COLLECT INFO */
$db_info = mongopress_get_db_info();
$versions = mongopress_get_versions();

/* CREATE TABLE OPTION ARRAYS */
$db_table_options = array(
	'id'		=> 'database_info',
	'title'		=> __('Database Collections'),
	'data'	=> array(
		'titles'	=> array(
			'Description'	=> 'hidden w80',
			'Count'			=> 'hidden w20 center'
		),
		'rows'		=> array(
			'key1'		=> array(
				'Total Objects', $db_info['total_objs']
			),
			'key2'		=> array(
				'Total Object Types', $db_info['total_types']
			),
			'key3'		=> array(
				'Total Users', $db_info['total_users']
			),
			'key4'		=> array(
				'Total Media', $db_info['total_media']
			)
		)
	)
);
$sys_table_options = array(
	'id'		=> 'system_info',
	'title'		=> __('System Settings'),
	'data'	=> array(
		'titles'	=> array(
			'Description'	=> 'hidden w80',
			'Count'			=> 'hidden w20 center'
		),
		'rows'		=> array(
			'key1'		=> array(
				'MongoPress', $versions['mongopress']
			),
			'key2'		=> array(
				'PHP', $versions['current']['php']
			),
			'key3'		=> array(
				'MongoDB', $versions['current']['mongodb']
			),
			'key4'		=> array(
				'MongoDB PHP Drivers', $versions['current']['phpd']
			)
		)
	)
);

/* TIME TO USE THE TABLE ARRAYS */
?>

<div class="admin-widget-wrapper" data-admin-widget-type="server-console" id="admin-widget-server-console">
	<h3 class="admin-widget-title"><?php _e('Server Console'); ?></h3>
	<div class="admin-widget">
		<div class="left-side">
			<?php mp_table($db_table_options); ?>
		</div>
		<div class="right-side">
			<?php mp_table($sys_table_options); ?>
		</div>
		<div style="display: block; clear: both"></div>
	</div>
</div>
<?php /**/ ?>
<div class="admin-widget-wrapper half" data-admin-widget-type="add-new-object">
	<h3 class="admin-widget-title"><?php _e('Add New Object'); ?></h3>
	<div class="admin-widget">
		<?php
		$form_options = array(
			'force_css'                 => true,
			'force_js'                  => true,
			'object_type_dropdown'      => true,
			'allow_new_object_types'    => true,
			'allow_custom_fields'       => true
		);
		mp_add_object_form($form_options);
		?>
	</div>
</div>

<div class="admin-widget-wrapper half right" data-admin-widget-type="mongopress-news">
	<h3 class="admin-widget-title"><?php _e('MongoPress News'); ?></h3>
	<div class="admin-widget fetch-feed" data-url="http://labs.laulima.com/projects/mongopress/feed/" data-nonce="<?php echo mp_create_nonce('fetch_feed','public'); ?>" data-limit="5"><?php _e('FETCHING FEED VIA AJAX - PLEASE BE PATIENT'); ?><br /><span class="loading"></span></div>
</div> 