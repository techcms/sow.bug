<div class="admin-widget-wrapper" data-admin-widget-type="debug-panel">
	<h3 class="admin-widget-title"><?php _e('Debug Panel'); ?></h3>
	<div class="admin-widget">
		<?php
		$m = mongopress_load_m();
		$mp = mongopress_load_mp();
		$options = $mp->options();
		$db = $m->selectDB($options['db_name']);
		$objs = $db->$options['obj_col'];
		$list = $db->listCollections();
		$collections = ''; $collection_count = 0;
		foreach ($list as $collection) {
			$collection_name_array = explode('.',$collection);
			$collection_count++;
			$these_objects = $db->$collection_name_array[1];
			$this_count = $these_objects->count();
			if(is_object($collection)){
				$collections.='<li><a href="#" '.mp_get_attr_filter('collections.php','a','#','','').'>'.$collection_name_array[1].'</a> ( '.$this_count.' )</li>';
			}
		}
		echo '<div class="left-side">';
			echo '<h3>'.__('You currently have '.$collection_count.' Collections:').'</h3>';
			echo '<ul>'.$collections.'</ul>';
		echo '</div>';
		$objects = array(); $object_count = 0;
		$all_objects = $objs->find();
		foreach($all_objects as $obj) {
			$object_count++;
			$objects[$obj['type']][] = $obj;
		}
		$these_object_types = '';
		foreach($objects as $object_type => $this_obj){
			$these_object_types.= '<li><a href="#" '.mp_get_attr_filter('collections.php','a','#','','').'>'.$object_type.'</a> ( '.count($this_obj).' )</li>';
		}
		echo '<div class="right-side">';
			echo '<h3>'.__('You also have '.$object_count.' objects using the following Object Types:').'</h3>';
			echo '<ul>'.$these_object_types.'</ul>';
		echo '</div>';
		?>
	</div>
</div>
