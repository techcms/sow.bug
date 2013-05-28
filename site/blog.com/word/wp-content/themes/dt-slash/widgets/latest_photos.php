<?php

/* Register the widget */
function dt_latest_photo_register() {
	register_widget( 'DT_latest_photo_Widget' );
}

/* Begin Widget Class */
class DT_latest_photo_Widget extends WP_Widget {

	/* Widget setup  */
	function DT_latest_photo_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'description' => __('A widget with photos from your albums', 'dt') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 250, 'id_base' => 'dt-latest-photo-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'dt-latest-photo-widget', __(THEME_TITLE.' Photos', 'dt'), $widget_ops, $control_ops );
	}

	/* Display the widget  */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$show = $instance['show'];
		$order = $instance['order'];
		
		global $wpdb;
		global $dt_where_filter_param;
		$dt_where_filter_param = sprintf( 'SELECT ID FROM %s WHERE post_type="%s" AND post_status="publish"', $wpdb->posts, 'dt_gallery' );
		
		$args = array(
					'numberposts'		=>$show,
					'posts_per_page'	=>$show,
					'post_type'			=>'attachment',
					'post_mime_type'	=>'image',
					'post_status' 		=>'inherit'
				);
		if ( 'rand' == $order ) {
			$args['orderby'] = 'rand';
		}
		
		add_filter( 'posts_where' , 'dt_posts_parents_where' );
		$p_query = new Wp_Query( $args );
		remove_filter( 'posts_where' , 'dt_posts_parents_where' );
		
		echo $before_widget ;

		// start
		echo ''
              .$before_title.$instance['title'].$after_title;
			
		echo '<div class="flickr">';
		
		if ( !empty($p_query->posts) ) {
			foreach ( $p_query->posts as $photo ) {
				$photo_t_src = dt_image_href( array(
								'image_id'	=>$photo->ID,
								'width'		=>50,
								'height'	=>50
							));
				
				$photo_b_src = current(wp_get_attachment_image_src($photo->ID, 'full'));
				$alt = get_post_meta($photo->ID,'_wp_attachment_image_alt', true );
				printf( '<a href="%s" class="%s"><img width="%d" height="%d" src="%s" alt="%s"/><i></i></a><div class="highslide-caption">%s</div>',
					$photo_b_src,
					'alignleft-f dt_highslide_image',
					50, 50,
					$photo_t_src,
					esc_attr($alt),
					$photo->post_excerpt
				);
			}
		}
		
		echo '</div>';
	
		echo $after_widget;
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['show'] = $new_instance['show'];
		$instance['order'] = $new_instance['order'];
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
			'title' => '',
			'order' => 'rand',
			'show' => 3
		);
			
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'dt'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:85%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e('Show:', 'dt'); ?></label><br />
			<label>
   			<input id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" value="rand" type="radio" <?php if ('rand' == $instance['order']) echo ' checked="checked"'; ?> /> Random photos
			</label><br />
			<label>
   			<input id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" value="latest" type="radio" <?php if ('latest' == $instance['order']) echo ' checked="checked"'; ?> /> Latest photos
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'show' ); ?>"><?php _e('How many:', 'dt'); ?></label><br />
   		<select id="<?php echo $this->get_field_id( 'show' ); ?>" name="<?php echo $this->get_field_name( 'show' ); ?>">
   		   <?php
   		      for ($i=1; $i<=12; $i++)
   		      {
   		         if ($i % 3 != 0) continue;
   		         echo '<option value="'.$i.'"'.( $instance['show'] == $i ? ' selected="selected"' : '' ).'>'.$i.'</option>';
   		      }
   		   ?>
   		</select>
	   </p>
		
		<div style="clear: both;"></div>
	<?php
	}
}

/* Load the widget */
add_action( 'widgets_init', 'dt_latest_photo_register' );

?>
