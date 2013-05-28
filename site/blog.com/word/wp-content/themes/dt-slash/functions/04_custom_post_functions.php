<?php
	add_action( 'add_meta_boxes', 'thumb_hide_box' );

	/* Do something with the data entered */
	add_action( 'save_post', 'thumb_hide_save_postdata' );
	add_action( 'save_post', 'blog_posts_pp_save_postdata' );
			
	/* Adds a box to the main column on the Post and Page edit screens */
	function thumb_hide_box() {
		if ( isset($_GET['post']) ) {
			$post_id = $_GET['post'];
		} elseif ( isset($_POST['post_ID']) ) {
			$post_id = $_POST['post_ID'];
		} else
			return;

		$template_file = get_post_meta( $post_id, '_wp_page_template', TRUE );
		$post_format = get_post_format( $post_id );
		
		if ( 'status' != $post_format ) {
			add_meta_box( 
				'hide_post_thumb',
				__( 'Hide featured image', 'dt' ),
				'thumb_hide_inner_box',
				'post',
				'side'
			);
		}

		if ( $template_file == 'blog-masonry.php' ) {
			add_meta_box( 
				'blog_posts_pp',
				__( 'Page options', 'dt' ),
				'blog_posts_pp_inner_box',
				'page',
				'side'
			);
		}
	}

	/* Prints the box content */
	function thumb_hide_inner_box( $post ) {
		$hide = get_post_meta( $post->ID, 'hide_f', true );
		$hide_in_gal = get_post_meta( $post->ID, 'hide_in_gal', true );
		wp_nonce_field( plugin_basename( __FILE__ ), 'hide' );
		?>
		<p>
			<input id="hide_f" type="checkbox" name="hide_f" <?php echo $hide ? ' checked="true"' : ''?>/>
			<label for="hide_f">
				<?php _e( 'Hide featured image in post details', LANGUAGE_ZONE ) ?>
			</label>
		</p>
		<?php if ( 'gallery' == get_post_format($post->ID) ): ?>
		<p>
			<input id="hide_in_gal" type="checkbox" name="hide_in_gal" <?php echo $hide_in_gal ? ' checked="true"' : ''?>/>
			<label for="hide_in_gal">
				<?php _e( 'Exclude featured image from gallery', LANGUAGE_ZONE ) ?>
			</label>
		</p>
		<?php endif;
	}

	/* When the post is saved, saves our custom data */
	function thumb_hide_save_postdata( $post_id ) {
		// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
			  
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times

		if ( !isset( $_POST['hide'] ) || !wp_verify_nonce( $_POST['hide'], plugin_basename( __FILE__ ) ) )
			return;

		$mydata = isset( $_POST['hide_f'] );  
		update_post_meta( $post_id, 'hide_f', $mydata );
		$mydata = isset( $_POST['hide_in_gal'] );
		update_post_meta( $post_id, 'hide_in_gal', $mydata );
	}
	
	// Posts Per Page for Blog-Masonry
	function blog_posts_pp_inner_box( $post ) {
		$data = get_post_meta( $post->ID, 'blog_posts_pp', true );
		$data['posts_per_page'] = isset($data['posts_per_page'])?$data['posts_per_page']:null;
		wp_nonce_field( plugin_basename( __FILE__ ), 'dt_blog_posts_pp' );
		?>
		<input type="text" id="dt_blog_posts_pp_pp" size="4" name="number_portf" value="<?php echo $data['posts_per_page'] ?>"/>
		<label for="dt_blog_posts_pp_pp">
			<?php _e( 'Number of posts on this page( if empty - uses standard setting )', LANGUAGE_ZONE ) ?>
		</label>
		<?php
	}
	
	function blog_posts_pp_save_postdata( $post_id ) {  
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;

		if ( !isset( $_POST['dt_blog_posts_pp'] ) || !wp_verify_nonce( $_POST['dt_blog_posts_pp'], plugin_basename( __FILE__ ) ) )
			return;

		$mydata = array(
			'posts_per_page'	=>( isset($_POST['number_portf'])?intval($_POST['number_portf']):null )
		);
		
		update_post_meta( $post_id, 'blog_posts_pp', $mydata );
	}