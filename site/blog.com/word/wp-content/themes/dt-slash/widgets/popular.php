<?php
	class DT_latest_post extends WP_Widget {
		function DT_latest_post() {
			parent::WP_Widget( 'dt_latest_posts', $name = THEME_TITLE. __( ' Popular Posts', LANGUAGE_ZONE) );
		}

		function form($instance) {
			// widget controls
			if ( $instance ) {
				$title = esc_attr( $instance['title'] );
				$category = $instance['category'];
				$show = esc_attr( $instance['show'] );
			} else {
				$title = '';
				$category = '';
				$show = 5;
			}
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title') ?>"><?php echo __( 'Title:', LANGUAGE_ZONE ) ?></label>
				<input style="width: 100px;" id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>" type="text" value="<?php echo $title ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('category') ?>"><?php echo __( 'Category:', LANGUAGE_ZONE ) ?></label>
				<select style="width: 100px;" id="<?php echo $this->get_field_id('category') ?>" name="<?php echo $this->get_field_name('category') ?>">
					<?php
					foreach ( get_categories() as $_tag ) {
						echo '<option value="'.$_tag->cat_ID.'"'.($_tag->cat_ID==$category ? ' selected="selected"' : '').'>'.$_tag->name.'</option>';
					}
					?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('show') ?>"><?php echo __( 'Amount of posts to show:', LANGUAGE_ZONE ) ?></label>
				<input style="width: 100px;" id="<?php echo $this->get_field_id('show') ?>" name="<?php echo $this->get_field_name('show') ?>" type="text" value="<?php echo $show ?>" />
			</p>
		<?php
		}

		function update($new_instance, $old_instance) {
			// processes widget options to be saved
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['category'] = intval( $new_instance['category'] );
			$instance['show'] = intval( $new_instance['show'] );
			return $instance;
		}

		function widget($args, $instance) {
			// outputs the content of the widget
			extract( $args );
			
			if( $instance ) {
				$title = esc_attr( $instance['title'] );
				$category = $instance['category'];
				$show = $instance['show'];
			} else {
				echo $before_widget;
				echo 'Widget haven\'t been confogured properly';
				echo $after_widget;
			}
			
			$args = array(
			   'category' 		=> $category,
			   'numberposts' 	=> $show
			);
			$i=1;
			
			echo $before_widget;
			
			if( $title )
				echo $before_title . $title . $after_title;
			
			$posts_sl = get_posts($args);
			$last = count($posts_sl);
			
			$comments_text = array(
				_x('no comments', 'popular posts widget', LANGUAGE_ZONE),
				_x('1 comment', 'popular posts widget', LANGUAGE_ZONE),
				_x('% comments', 'popular posts widget', LANGUAGE_ZONE)
			);
			
			foreach ( $posts_sl as $post_item ) {
				$user_info = get_userdata($post_item->post_author);
				echo '<div class="post'.($i++==1 ? ' first':'').'">';
				
				// if post pass protected
				if( !post_password_required($post_item->ID) ) {
					// thumbnail
					if( has_post_thumbnail($post_item->ID) ) {
						$args = array(
							'post_id'	=>$post_item->ID,
							'width'		=>50,
							'height'	=>50,
							'upscale'	=>true
						);
						$t_href = dt_image_href( $args );
						$alt = get_post_meta(get_post_thumbnail_id($post_item->ID),'_wp_attachment_image_alt', true );
						echo '<a class="alignleft not" href="'.get_permalink($post_item->ID).'">';
						echo '<img width="50" height="50" src="'. $t_href. '" alt="'. esc_attr($alt). '">';
						echo '</a>';
					}
					// title
					echo '<a href="'.get_permalink($post_item->ID).'">'.$post_item->post_title.'</a>';
					// comments
					echo '<div class="goto_post">
					<a href="'.get_permalink($post_item->ID).'#comments" class="ico_link comments">';
					
					if( !$post_item->comment_count ) {
						echo $comments_text[0];
					}elseif( 1 == $post_item->comment_count ) {
						echo $comments_text[1];
					}else {
						echo str_replace('%', $post_item->comment_count, $comments_text[2]);
					}
					
					echo '</a></div>';
				}else {
					// title
					echo '<a href="'.get_permalink($post_item->ID).'">'. _x('Protected: ', 'popular posts widget', LANGUAGE_ZONE). $post_item->post_title.'</a>';
				}
				echo '</div>';
			}
			echo $after_widget;
		}
	}
	
	function dt_latest_posts_register() {
		register_widget('DT_latest_post');
	}
	
	add_action( 'widgets_init', 'dt_latest_posts_register' );