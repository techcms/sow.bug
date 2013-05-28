<?php
			/******************************************************** POST TYPE for portfolio *****************************************************/
			$labels = array(
				'name' => _x('Portfolio', 'post type general name', 'dt'),
				'singular_name' => _x('Portfolio', 'post type singular name', 'dt'),
				'add_new' => _x('Add New', 'dt', 'dt'),
				'add_new_item' => __('Add New Item', 'dt'),
				'edit_item' => __('Edit Item', 'dt'),
				'new_item' => __('New Item', 'dt'),
				'view_item' => __('View Item', 'dt'),
				'search_items' => __('Search Items', 'dt'),
				'not_found' =>  __('No items found', 'dt'),
				'not_found_in_trash' => __('No items found in Trash', 'dt'), 
				'parent_item_colon' => '',
				'menu_name' => 'Portfolio'

			);
			$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true, 
				'query_var' => true,
				'rewrite' => array('slug' => 'dt_portfolio'),
				'capability_type' => 'post',
				'has_archive' => true, 
				'hierarchical' => false,
				'menu_position' => 20,
				'menu_icon' =>get_template_directory_uri(). '/images/admin_ico_portfolio.png',
				'supports' => array('title','editor','thumbnail','comments', 'excerpt', 'author')
			); 
			register_post_type('portfolio',$args);
			
			/****************************************************************** ADD NEW TAXONOMY *******************************************/
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
			
			/* TAXONOMY for portfolio */
			register_taxonomy('portfolio-category',array('portfolio'), array(
				'hierarchical' => true,
				'show_in_nav_menus ' => false,
				'public' => false,
				'show_tagcloud' => false,
				'labels' => $labels,
				'show_ui' => true,
				'rewrite' => false,
			));
			
			/****************************************************** Define the CUSTOM BOX **********************************************************/
			// WP 3.0+
			add_action( 'add_meta_boxes', 'dt_portfolio_box' );
			
			// backwards compatible
			//add_action( 'admin_init', 'dt_portfolio_box', 1 );

			/* Do something with the data entered */
			add_action( 'save_post', 'dt_portfolio_taxonomy_save_postdata' );
			add_action( 'save_post', 'dt_portfolio_list_save' );

			function dt_portfolio_box() {
				// portfolio item meta box add
				add_meta_box(	'portfolio-category',
								__( 'Portfolio options', 'dt' ),
								'dt_portfolio_taxonomy_inner_box',
								'portfolio',
								'side',
								'high'
							);
				// portfolio list meta box for layout
				if ( isset($_GET['post']) ) {
					$post_id = $_GET['post'];
				} elseif ( isset($_POST['post_ID']) ) {
					$post_id = $_POST['post_ID'];
				} else
					return;
				
				$template_file = get_post_meta($post_id,'_wp_page_template',TRUE);
 
				// check for a template type
				if ( ($template_file == 'portfolio.php') || ($template_file == 'portfolio-masonry.php') ) {
					add_meta_box(	'portfolio-list',
									__( 'Portfolio options', 'dt' ),
									'dt_portfolio_list_inner_box',
									'page',
									'side',
									'high'
								);
				}
			}
			
			/* Prints the box content */
			function dt_portfolio_taxonomy_inner_box( $post ) {
				$video = '';
				$value = get_post_meta( $post->ID, 'portfolio_options', true ); 
				$video = isset( $value['video_html'] )?$value['video_html']:'';
				$video_width = isset( $value['video_width'] )?$value['video_width']:'';
				$hide_m = ( isset($value['hide_meta']) && $value['hide_meta'] )?' checked':'';
				$hide_med = ( isset($value['hide_media']) && $value['hide_media'] )?' checked':'';
				// Use nonce for verification
				wp_nonce_field( plugin_basename( __FILE__ ), 'portfolio_nonce' );
				?>
				<label for="video"><?php echo __( 'Video html code', 'dt') ?></label>
				<div>
					<p><textarea id="video" name="video_html" style="width: 261px;"><?php echo $video ?></textarea></p>
					<p><input type="input" name="video_width" value="<?php echo $video_width ?>" style="width: 50px;"/><?php echo __( 'Video frame width', 'dt' ) ?></p>
				</div>
				<p><input type="checkbox" name="hide_meta"<?php echo $hide_m ?>/><?php echo __( 'Hide metablock in details', 'dt' ) ?></p>
				<p><input type="checkbox" name="hide_media"<?php echo $hide_med ?>/><?php echo __( 'Hide mediacontent in details', 'dt' ) ?></p>
				<?php
			}

			/* When the post is saved, saves our custom data */
			function dt_portfolio_taxonomy_save_postdata( $post_id ) {
				// verify if this is an auto save routine. 
				// If it is our form has not been submitted, so we dont want to do anything
			  
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				  return;

				// verify this came from the our screen and with proper authorization,
				// because save_post can be triggered at other times

				if ( !isset( $_POST['portfolio_nonce'] ) || !wp_verify_nonce( $_POST['portfolio_nonce'], plugin_basename( __FILE__ ) ) )
				  return;

			  
				// Check permissions
				if ( !current_user_can( 'edit_post', $post_id ) )
					return;

				// OK, we're authenticated: we need to find and save the data
				
				$mydata = array();
				$mydata['video_html'] = isset( $_POST['video_html'] )?$_POST['video_html']:'';
				if( $mydata['video_html'] && empty( $_POST['video_width'] )){
					preg_match( '/width[\s]*=[\s]*\\\["\'\s]*(\d+)\\\["\'\s]*/', $mydata['video_html'], $w_search_res );
				}
				
				if( isset($w_search_res[1]) && is_numeric($w_search_res[1]) )
					$matched_val = $w_search_res[1];
				else
					$matched_val = '';
						
				$mydata['video_width'] = ( isset( $_POST['video_width'] ) && !empty($_POST['video_width']) )?intval(trim($_POST['video_width'])):$matched_val;
				$mydata['hide_meta'] = isset( $_POST['hide_meta'] )?true:false;
				$mydata['hide_media'] = isset( $_POST['hide_media'] )?true:false;
				
				update_post_meta( $post_id, 'portfolio_options', $mydata );
			}
			
			// portfolio layout metabox
			function dt_portfolio_list_inner_box( $post ){
				echo '<script>var dt_admin = {box: "#portfolio-list"};</script>';
				echo '<script src="' . get_template_directory_uri() . '/js/admin_gallery.js"></script>';
				// Use nonce for verification
				wp_nonce_field( plugin_basename( __FILE__ ), 'portfolio_list_nonce' );
				$terms = get_categories( array( 'type'                     => 'portfolio',
												'hide_empty'               => 1,
												'hierarchical'             => 0,
												'taxonomy'                 => 'portfolio-category',
												'pad_counts'               => false	) );
				$port_terms = get_post_meta( $post->ID, 'show_portf', true );
				$number_portf = (isset($port_terms['number_portf']) && $port_terms['number_portf'])?$port_terms['number_portf']:'';
				if(isset($port_terms['only']) && is_array($port_terms['only']) ) $only = $port_terms['only']; else $only = array();
				if(isset($port_terms['except']) && is_array($port_terms['except']) ) $except = $port_terms['except']; else $except = array();
				if( empty($only) && empty($except) ) $all = ' checked="checked"'; else $all ='';
				echo '<p>' . __( 'Show Category:', 'dt' ) . '<br></p>';
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
				echo '<p>';
				echo '<input type="text" id="dt_portfolio_postsonpage" size="4" name="number_portf" value="'. $number_portf. '"/>';
				echo '<label for="dt_portfolio_postsonpage">'. __( 'Number of posts on this page( if empty - uses standard setting )', LANGUAGE_ZONE ). '</label>';
				echo '</p>';
			}
			
			// portfolio layout metabox save
			function dt_portfolio_list_save( $post_id ) {
				// verify if this is an auto save routine. 
				// If it is our form has not been submitted, so we dont want to do anything
			  
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
					return;

				// verify this came from the our screen and with proper authorization,
				// because save_post can be triggered at other times

				if ( !isset( $_POST['portfolio_list_nonce'] ) || !wp_verify_nonce( $_POST['portfolio_list_nonce'], plugin_basename( __FILE__ ) ) )
					return;

			  
				// Check permissions
				if ( !current_user_can( 'edit_page', $post_id ) )
				return;
								
				$mydata = null;
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
				$mydata['number_portf'] = intval($_POST['number_portf']);
				
				update_post_meta( $post_id, 'show_portf', $mydata );
			}
			
			//add portfolio to autor archive
			function __set_portfolio_for_author( &$query )
			{
				if ( $query->is_author )
					$query->set( 'post_type', array( 'portfolio', 'post') );
				remove_action( 'pre_get_posts', '__set_portfolio_for_author' ); // run once!
			}
			add_action( 'pre_get_posts', '__set_portfolio_for_author' );

			// taxonomy list
			function dt_portf_tax_list( array $options ) {
				global $post;
				
				// for other button
				$tax = get_terms( 'portfolio-category', array('fields' =>'ids') );
				$query = new Wp_Query(
					array(	
						'post_type'		=>'portfolio',
						'tax_query'		=>array(
							array(
								'taxonomy'	=>'portfolio-category',
								'field'		=>'id',
								'terms'		=>$tax,
								'operator'	=>'NOT IN'
							)
						),
						'posts_per_page'	=>1
					)
				);
				$others_flag = $query->found_posts?true:false;
				// end other part
				
				$term_args = array( 	'type'          =>'portfolio',
										'hide_empty'    =>1,
										'hierarchical'  =>0,
										'taxonomy'      =>'portfolio-category',
										'pad_counts'    =>false
								);
				$default = array(	'a_class'		=>'button filter',
									'c_class'		=>'filters',
									'ajax'			=>false,
									'tax'			=>null
								);
				$o = array_merge( $default, $options );
				$tax_key = $o['tax']?key( $o['tax'] ):$o['tax'];
				$cur_cat = $out = $href = $href_plus = '';
								
				if( !$o['ajax'] ){
					// set glue element for href
					$href = get_permalink();
					if ( get_option('permalink_structure') != '' ){
						$glue = '?';
					}else{
						$glue = '&';
					}
					
					if( isset($_GET['portfolio_category']) ){
						$cur_cat = trim( (string) $_GET['portfolio_category'] );
					}
					$href_plus = $href. $glue. 'portfolio_category=';
					$href_other = $href. $glue. 'portfolio_category=none';
				}else{
					$href_plus = '#';
					$href = '#all';
					$href_other = '#none';
				}
				
				if( 'except' == $tax_key ) {
					$term_args['exclude'] = current( $o['tax'] );
				}elseif( 'only' == $tax_key ){
					$term_args['include'] = current( $o['tax'] );
				}

				$terms = get_categories( $term_args );
				if( 1 == count($terms) ) {
					$out .= '<div class="'. esc_attr($o['c_class']). '" style="display: none !important;">';
					$out .= '<a href="'. esc_attr( $href_plus. $terms[0]->slug ). '" class="'. esc_attr($o['a_class']). ' act">';
                    $out .= '</a>';
                    $out .= '</div>';
                    return $out;
                }

				if( $terms ){
					$out .= '<div class="'. esc_attr($o['c_class']). '">';
					$out .= '<a href="'. esc_attr( $href ). '" class="'. esc_attr($o['a_class']). ($cur_cat?'':' act'). '">';
					$out .= '<span class="but-r"><span>'. __( 'View all', 'dt'). '</span></span>';
					$out .= '</a>';
					foreach( $terms as $term ){
						$act = '';
						if( $cur_cat == $term->slug ) $act = ' act';
						$out .= '<a href="'. esc_attr( $href_plus. $term->slug ). '" class="'. esc_attr($o['a_class']). $act. '">';
						$out .= '<span class="but-r"><span>'. $term->name. '</span></span>';
						$out .= '</a>';
					}
					
					if ( $others_flag ) {
						$out .= '<a href="'. esc_attr( $href_other ). '" class="'. esc_attr($o['a_class']). ('none' == $cur_cat?' act':''). '">';
						$out .= '<span class="but-r"><span>'. __( 'Other', 'dt'). '</span></span>';
						$out .= '</a>';
					}
					$out .= '</div>';
				}
				return $out;
			}