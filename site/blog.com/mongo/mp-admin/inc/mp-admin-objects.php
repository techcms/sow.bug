<div class="admin-widget-wrapper twothirds" data-admin-widget-type="current-objects" id="admin-widget-current-objects">
	<h3 class="admin-widget-title"><?php _e('Current Objects'); ?></h3>
	<div class="admin-widget">
		<?php
		$table_options = array(
			'allow_edits'   => true
		);
		mp_objects_table($table_options);
		?>
	</div>
</div>

<div class="admin-widget-wrapper onethird" data-admin-widget-type="add-edit-objects" id="widget-wrapper-add-edit-objects">
	<h3 class="admin-widget-title"><?php _e('Add &amp Edit Objects'); ?></h3>
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