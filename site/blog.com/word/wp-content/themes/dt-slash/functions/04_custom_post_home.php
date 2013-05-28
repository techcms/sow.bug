<?php
			/* post type for main slider */
			$labels = array(
				'name' => _x('Homepage Slider', 'post type general name', 'dt'),
				'singular_name' => _x('Slide', 'post type singular name', 'dt'),
				'add_new' => _x('Add New Slide', 'post type new', 'dt'),
				'add_new_item' => __('Add New Slide', 'dt'),
				'edit_item' => __('Edit Slide', 'dt'),
				'new_item' => __('New Slide', 'dt'),
				'view_item' => __('View Slide', 'dt'),
				'search_items' => __('Search for Slides', 'dt'),
				'not_found' =>  __('No Slides Found', 'dt'),
				'not_found_in_trash' => __('No Slides found in Trash', 'dt'), 
				'parent_item_colon' => '',
				'menu_name' => 'Homepage Slider'
			);
			$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true, 
				'show_in_menu' => true, 
				'query_var' => true,
				'rewrite' => false,
				'capability_type' => 'post',
				'has_archive' => false, 
				'hierarchical' => false,
				'menu_position' => 20,
				'menu_icon' =>get_template_directory_uri(). '/images/admin_ico_slides.png',
				'supports' => array( 'thumbnail', 'title', 'editor', 'excerpt' )
			); 
			register_post_type( 'main_slider', $args);
			
			// taxonomy
			$labels = array(
				'name' => _x( 'Categories', 'taxonomy general name', 'dt' ),
				'singular_name' => _x( 'Category', 'taxonomy singular name', 'dt' ),
				'search_items' =>  __( 'Search in Category', 'dt' ),
				'all_items' => __( 'Categories', 'dt' ),
				'parent_item' => __( 'Parent Category', 'dt' ),
				'parent_item_colon' => __( 'Parent Category:', 'dt' ),
				'edit_item' => __( 'Edit Category', 'dt' ), 
				'update_item' => __( 'Update Category', 'dt' ),
				'add_new_item' => __( 'Add New Category', 'dt' ),
				'new_item_name' => __( 'New Category Name', 'dt' ),
				'menu_name' => __( 'Categories', 'dt' ),
			); 	
			
			register_taxonomy('slider-category',array('main_slider'), array(
				'hierarchical' => true,
				'show_in_nav_menus ' => false,
				'public' => false,
				'show_tagcloud' => false,
				'labels' => $labels,
				'show_ui' => true,
				'rewrite' => false,
			));

			/* Define the custom box */

			// WP 3.0+
			add_action( 'add_meta_boxes', 'slider_meta_box' );
			add_action( 'save_post', 'slider_save_postdata' );
			add_action( 'save_post', 'dt_home_slider_save' );
			add_action( 'save_post', 'dt_home_static_save' );
			add_action( 'save_post', 'dt_home_video_save' );

			/* Adds a box to the main column on the Post and Page edit screens */
			function slider_meta_box () {
				add_meta_box ( 
					'Slider link',
					__( 'Slider options', 'dt' ),
					'slider_meta_block',
					'main_slider',
					'side'
				);

				if ( isset($_GET['post']) ) {
					$post_id = $_GET['post'];
				} elseif ( isset($_POST['post_ID']) ) {
					$post_id = $_POST['post_ID'];
				} else
					return;
				
				$page_meta = get_post_meta($post_id,'_wp_page_template',TRUE);
 				if ( 'home-light.php' == $page_meta ) {
					add_meta_box( 
						'slider-options',
						__( 'Options for homepage slider', 'dt' ),
						'dt_home_slider_options',
						'page',
						'side'
					);
				}elseif ( 'home-static.php' == $page_meta ) {
					add_meta_box( 
						'Page options',
						__( 'Options for homepage static', 'dt' ),
						'dt_home_slider_static_options',
						'page',
						'side'
					);
				}elseif ( 'home-video.php' == $page_meta || 'home-video_2.php' == $page_meta ) {
					add_meta_box( 
						'Page options',
						__( 'Options for homepage video', 'dt' ),
						'dt_home_slider_video_options',
						'page',
						'side'
					);
					
					add_action('admin_enqueue_scripts', 'my_admin_scripts');
					add_action('admin_print_styles', 'my_admin_styles');
				}
			}

			/* Prints the box content */
			function slider_meta_block( $post ) {
				// Use nonce for verification
				wp_nonce_field( plugin_basename( __FILE__ ), 'slider_noncename' );

				// serialized array returned and userialized...
				$value = get_post_meta( $post->ID, 'slider_meta', true );
				
				$s_link = isset( $value['link'] )?trim( $value['link'] ):'';
				$s_hide_text = '';
				if( isset( $value['hide_text'] ) )
					$s_hide_text = !empty( $value['hide_text'] )?' checked':'';
				
				echo '<input type="text" id="slider_link" name="slider_link" value="' . $s_link . '" size="43" />';
				echo '<label for="slider_link">'. __('Slider link', 'dt'). '</label>';
				echo '<p>';
				echo '<input type="checkbox" id="slider_hide_text" name="slider_hide_text"' . $s_hide_text . '/>' . __( 'Hide post text in the side box of slide', 'dt' );
				echo '</p>';
			}
			
			// SLIDER METABOX
			function dt_home_slider_options( $post ) {
				$data = get_post_meta( $post->ID, 'dt_homepage_options', true );
				$data = wp_parse_args( $data, dt_get_home_slider_defaults() );
				extract( $data, EXTR_SKIP );
				
				$dt_static_desc = $dt_static_desc?' checked="true"':$dt_static_desc;
				$dt_hide_over_mask = $dt_hide_over_mask?' checked="true"':$dt_hide_over_mask;
				$dt_pres_def_s_prop = $dt_pres_def_s_prop?' checked="true"':$dt_pres_def_s_prop;
				$dt_autoplay = $dt_autoplay?' checked="true"':$dt_autoplay;
				
				// Use nonce for verification
				wp_nonce_field( plugin_basename( __FILE__ ), 'homepage_slider_noncename' );

				// The actual fields for data entry
				?>
				<p>
					<input type="checkbox" id="dt_static_desc" name="dt_static_desc"<?php echo $dt_static_desc ?>/>
					<label for="dt_static_desc"><?php echo __("static description", 'dt' ) ?></label>
				</p>
				<p>
					<input type="checkbox" id="dt_hide_over_mask" name="dt_hide_over_mask"<?php echo $dt_hide_over_mask ?>/>
					<label for="dt_hide_over_mask"><?php echo __("hide overlay mask", 'dt' ) ?></label>
				</p>
				<p>
					<input type="checkbox" id="dt_pres_def_s_prop" name="dt_pres_def_s_prop"<?php echo $dt_pres_def_s_prop ?>/>
					<label for="dt_pres_def_s_prop"><?php echo __("preserve default slide proportions", 'dt' ) ?></label>
				</p>
				<p>
					<input type="text" id="dt_timing" name="dt_timing" value="<?php echo $dt_timing ?>" size="4"/>
					<label for="dt_timing"><?php echo __("slider timeout(in seconds)", 'dt' ) ?></label>
				</p>
				<p>
					<input type="checkbox" id="dt_autoplay" name="dt_autoplay"<?php echo $dt_autoplay ?>/>
					<label for="dt_autoplay"><?php echo __("slider autoplay", 'dt' ) ?></label>
				</p>
				<?php
				echo '<script>var dt_admin = {box: "#slider-options"};</script>';
				echo '<script src="' . get_template_directory_uri() . '/js/admin_gallery.js"></script>';
				$terms = get_categories( array( 'type'                     => 'main_slider',
												'hide_empty'               => 1,
												'hierarchical'             => 0,
												'taxonomy'                 => 'slider-category',
												'pad_counts'               => false	) );
				$port_terms = get_post_meta( $post->ID, 'dt_homepage_options', true );
				$number_portf = (isset($port_terms['number_portf']) && $port_terms['number_portf'])?$port_terms['number_portf']:'';
				if(isset($port_terms['only']) && is_array($port_terms['only']) ) $only = $port_terms['only']; else $only = array();
				if(isset($port_terms['except']) && is_array($port_terms['except']) ) $except = $port_terms['except']; else $except = array();
				if( empty($only) && empty($except) ) $all = ' checked="checked"'; else $all ='';
				echo '<p>' . __( 'Show Ctegory:', 'dt' ) . '<br></p>';
				echo '<div class="showhide"><label><input name="show_type_portf" value="all"' . $all . ' type="radio">' . __( 'All', 'dt' ) . '</label><br></div>';
				echo '<div class="showhide"><label><input name="show_type_portf" value="only"' . (empty($only)?'':' checked="checked"') . ' type="radio">' . __( 'Only...', 'dt' ) . '</label><br>';
					echo '<div style="margin-left: 20px; margin-bottom: 8px; display: none;" class="list">';
						if( $terms ){
							foreach( $terms as $term ){
								echo '<label><input name="show_portf[only][]" value="' . $term->term_id . '" type="checkbox"' . (in_array($term->term_id, $only)?' checked':'') . '>' . $term->name . '</label><br>';
							}
						}
					echo '</div>';
				echo '</div>';
				echo 	'<div class="showhide"><label><input name="show_type_portf" value="except"' . (empty($except)?'':' checked="checked"') . ' type="radio">' . __( 'Except...', 'dt' ) . '</label><br>
						<div style="margin-left: 20px; margin-bottom: 8px; display: none;" class="list">';
						if( $terms ){
							foreach( $terms as $term ){
								echo '<label><input name="show_portf[except][]" value="' . $term->term_id . '" type="checkbox"' . (in_array($term->term_id, $except)?' checked':'') . '>' . $term->name . '</label><br>';
							}
						}
					echo '</div>';
				echo '</div>';
			}
			
			// STATIC METABOX
			function dt_home_slider_static_options( $post ) {
				$data = get_post_meta( $post->ID, 'dt_homepage_options', true );
				
				$dt_hide_desc = ( isset( $data['dt_hide_desc'] ) && $data['dt_hide_desc'] )?' checked="true"':'';
				$dt_hide_over_mask = ( isset( $data['dt_hide_over_mask'] ) && $data['dt_hide_over_mask'] )?' checked="true"':'';
				$dt_pres_def_s_prop = ( isset( $data['dt_pres_def_s_prop'] ) && $data['dt_pres_def_s_prop'] )?' checked="true"':'';
				$dt_link = isset( $data['dt_link'] )?trim( $data['dt_link'] ):'';
				
				// Use nonce for verification
				wp_nonce_field( plugin_basename( __FILE__ ), 'homepage_static_noncename' );

				// The actual fields for data entry
				?>
				<p>
					<input type="checkbox" id="dt_hide_desc" name="dt_hide_desc"<?php echo $dt_hide_desc ?>/>
					<label for="dt_hide_desc"><?php echo __("hide description", 'dt' ) ?></label>
				</p>
				<p>
					<input type="checkbox" id="dt_hide_over_mask" name="dt_hide_over_mask"<?php echo $dt_hide_over_mask ?>/>
					<label for="dt_hide_over_mask"><?php echo __("hide overlay mask", 'dt' ) ?></label>
				</p>
				<p>
					<input type="checkbox" id="dt_pres_def_s_prop" name="dt_pres_def_s_prop"<?php echo $dt_pres_def_s_prop ?>/>
					<label for="dt_pres_def_s_prop"><?php echo __("preserve default slide proportions", 'dt' ) ?></label>
				</p>
				<p>
					<input type="text" id="dt_link" name="dt_link" value="<?php echo $dt_link ?>" size="43" />
					<label for="dt_link"><?php _e('Detail link', 'dt') ?></label>
				</p>
				<?php
			}
			
			// VIDEO METABOX
			function dt_home_slider_video_options( $post ) {
				$data = get_post_meta( $post->ID, 'dt_homepage_options', true );
				
				$dt_video = isset( $data['dt_video'] )?$data['dt_video']:'';
				$dt_hide_desc = ( isset( $data['dt_hide_desc'] ) && $data['dt_hide_desc'] )?' checked="true"':'';
//				$dt_hide_over_mask = ( isset( $data['dt_hide_over_mask'] ) && $data['dt_hide_over_mask'] )?' checked="true"':'';
				$dt_vid_autoplay = ( isset( $data['dt_vid_autoplay'] ) && $data['dt_vid_autoplay'] )?' checked="true"':'';
				$dt_vid_loop = ( isset( $data['dt_vid_loop'] ) && $data['dt_vid_loop'] )?' checked="true"':'';
				//$dt_vid_controls = ( isset( $data['dt_vid_controls'] ) && $data['dt_vid_controls'] )?' checked="true"':'';
				$dt_link = isset( $data['dt_link'] )?trim( $data['dt_link'] ):'';

				$u_href = get_admin_url();
				$u_href .= '/media-upload.php?post_id='. $post->ID;
				$u_href .= '&type=image&amp;TB_iframe=true';
				
				// Use nonce for verification
				wp_nonce_field( plugin_basename( __FILE__ ), 'homepage_video_noncename' );

				// The actual fields for data entry
				?>
				
				<p>
					<input id="dt_video" type="text" name="dt_video" value="<?php echo $dt_video ?>" />
					<?php echo __("video url", 'dt' ) ?>
				</p>
					<a id="upload_image_button" class="upload_button button" href="<?php echo esc_url( $u_href ) ?>">Upload</a>
					<a id="remove_image_button" class="upload_button button" href="#">Remove</a>
					<hr>
				<p>
					<label for="dt_vid_autoplay">
						<input type="checkbox" id="dt_vid_autoplay" name="dt_vid_autoplay"<?php echo $dt_vid_autoplay ?>/>
						<?php echo __("video autoplay", 'dt' ) ?>
					</label>
				</p>
				<p>
					<label for="dt_vid_loop">
						<input type="checkbox" id="dt_vid_loop" name="dt_vid_loop"<?php echo $dt_vid_loop ?>/>
						<?php echo __("video repeat", 'dt' ) ?>
					</label>
				</p>
				<?php
/*				<p>
					<label for="dt_vid_controls">
						<input type="checkbox" id="dt_vid_controls" name="dt_vid_controls"<?php echo $dt_vid_controls ?>/>
						<?php echo __("show video control elements", 'dt' ) ?>
					</label>
				</p>
*/				?>
				<p>
					<label for="dt_hide_desc">
						<input type="checkbox" id="dt_hide_desc" name="dt_hide_desc"<?php echo $dt_hide_desc ?>/>
						<?php echo __("hide description", 'dt' ) ?>
					</label>
				</p>
<?php
/*
				<p>
					<label for="dt_hide_over_mask">
						<input type="checkbox" id="dt_hide_over_mask" name="dt_hide_over_mask"<?php echo $dt_hide_over_mask ?>/>
						<?php echo __("hide overlay mask", 'dt' ) ?>
					</label>
				</p>
*/
?>
				<p>
					<input type="text" id="dt_link" name="dt_link" value="<?php echo $dt_link ?>" size="43" />
					<label for="dt_link"><?php _e('Detail link', 'dt') ?></label>
				</p>
				<?php
			}
			
			/* When the post is saved, saves our custom data */
			function slider_save_postdata( $post_id ) {
				// verify if this is an auto save routine. 
				// If it is our form has not been submitted, so we dont want to do anything
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
					return;

				// verify this came from the our screen and with proper authorization,
				// because save_post can be triggered at other times

				if ( !isset( $_POST['slider_noncename'] ) || !wp_verify_nonce( $_POST['slider_noncename'], plugin_basename( __FILE__ ) ) )
					return;

				// Check permissions
				if ( !current_user_can( 'edit_post', $post_id ) )
					return;

				// OK, we're authenticated: we need to find and save the data
				$mydata = array();
				$mydata['link'] = esc_url_raw( $_POST['slider_link'] );
				$mydata['hide_text'] = isset( $_POST['slider_hide_text'] );

				update_post_meta( $post_id, 'slider_meta', $mydata );
			}

			// SLIDER SAVE FUNK
			function dt_home_slider_save( $post_id ) {
				// verify if this is an auto save routine. 
				// If it is our form has not been submitted, so we dont want to do anything
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
					return;

				// verify this came from the our screen and with proper authorization,
				// because save_post can be triggered at other times

				if ( !isset( $_POST['homepage_slider_noncename'] ) || !wp_verify_nonce( $_POST['homepage_slider_noncename'], plugin_basename( __FILE__ ) ) )
					return;

				if ( !current_user_can( 'edit_post', $post_id ) )
					return;
				
				// OK, we're authenticated: we need to find and save the data
				$mydata = array();
				$mydata['dt_static_desc'] = isset($_POST['dt_static_desc']);
				$mydata['dt_hide_over_mask'] = isset($_POST['dt_hide_over_mask']);
				$mydata['dt_pres_def_s_prop'] = isset($_POST['dt_pres_def_s_prop']);
				$mydata['dt_timing'] = intval($_POST['dt_timing']);
				$mydata['dt_autoplay'] = isset($_POST['dt_autoplay']);
				
				switch( $_POST['show_type_portf'] ) {
					case 'only':
						if( isset($_POST['show_portf']['only']) ) {
							$mydata['only'] = $_POST['show_portf']['only'];
						}
						break;
					case 'except':
						if( isset($_POST['show_portf']['except']) ) {
							$mydata['except'] = $_POST['show_portf']['except'];
						}
						break; 
				}
				
				update_post_meta( $post_id, 'dt_homepage_options', $mydata );
			}
			
			// STATIC SAVE FUNK
			function dt_home_static_save( $post_id ) {
				// verify if this is an auto save routine. 
				// If it is our form has not been submitted, so we dont want to do anything
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
					return;

				// verify this came from the our screen and with proper authorization,
				// because save_post can be triggered at other times

				if ( !isset( $_POST['homepage_static_noncename'] ) || !wp_verify_nonce( $_POST['homepage_static_noncename'], plugin_basename( __FILE__ ) ) )
					return;

				if ( !current_user_can( 'edit_post', $post_id ) )
					return;
				
				// OK, we're authenticated: we need to find and save the data
				$mydata = array();
				$mydata['dt_hide_desc'] = isset($_POST['dt_hide_desc']);
				$mydata['dt_hide_over_mask'] = isset($_POST['dt_hide_over_mask']);
				$mydata['dt_pres_def_s_prop'] = isset($_POST['dt_pres_def_s_prop']);
				$mydata['dt_link'] = esc_url_raw( $_POST['dt_link'] );
				
				update_post_meta( $post_id, 'dt_homepage_options', $mydata );
			}
			
			// VIDEO SAVE FUNK
			function dt_home_video_save( $post_id ) {
				// verify if this is an auto save routine. 
				// If it is our form has not been submitted, so we dont want to do anything
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
					return;

				// verify this came from the our screen and with proper authorization,
				// because save_post can be triggered at other times

				if ( !isset( $_POST['homepage_video_noncename'] ) || !wp_verify_nonce( $_POST['homepage_video_noncename'], plugin_basename( __FILE__ ) ) )
					return;

				if ( !current_user_can( 'edit_post', $post_id ) )
					return;
				
				// OK, we're authenticated: we need to find and save the data
				$mydata = array();
				$mydata['dt_video'] = isset($_POST['dt_video'])?$_POST['dt_video']:null;
				$mydata['dt_hide_desc'] = isset($_POST['dt_hide_desc']);
//				$mydata['dt_hide_over_mask'] = isset($_POST['dt_hide_over_mask']);
				$mydata['dt_vid_autoplay'] = isset($_POST['dt_vid_autoplay']);
				$mydata['dt_vid_loop'] = isset($_POST['dt_vid_loop']);
				//$mydata['dt_vid_controls'] = isset($_POST['dt_vid_controls']);
				$mydata['dt_link'] = esc_url_raw( $_POST['dt_link'] );
				
				update_post_meta( $post_id, 'dt_homepage_options', $mydata );
			}
			
			