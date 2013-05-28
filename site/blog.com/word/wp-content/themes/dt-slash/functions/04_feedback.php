<?php
    // DELETE
    global $dt_errors;
    $dt_errors['contact_form'] = '';
    $dt_errors['contact_widget'] = '';
    
	function dt_contact_box () {
		if ( isset($_GET['post']) ) {
			$post_id = $_GET['post'];
		} elseif ( isset($_POST['post_ID']) ) {
			$post_id = $_POST['post_ID'];
		} else
			return;
				
		$template_file = get_post_meta($post_id,'_wp_page_template',TRUE);
		if ( 'contact.php' == $template_file ) {
			add_meta_box(
				'contact',
				__( 'Contact options', LANGUAGE_ZONE ),
				'dt_contact_inner_box',
				'page',
				'side',
				'high'
			);
		}
	}
	
	function dt_contact_inner_box ( $post ) {
		$data = get_post_meta( $post->ID, 'contact_options', true );
		$data['target_email'] = isset($data['target_email'])?$data['target_email']:'';
		$data['html_map'] = isset($data['html_map'])?$data['html_map']:'';
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'contact_nonce' );
		?>
		<p>
			<input id="dt_contact_email" type="text" name="target_email" value="<?php echo $data['target_email'] ?>"/>
			<label for="dt_contact_email"><?php echo __('target em@il', LANGUAGE_ZONE) ?></label>
		</p>
		<p>
			<label for="html_map"><?php echo __('html code of the map', LANGUAGE_ZONE) ?></label>
			<textarea id="html_map" name="html_map" style="width: 255px;height: 100px;"><?php echo $data['html_map'] ?></textarea>
		</p>
		<?php
	}
	
	function dt_contact_save_postdata ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;
			
		if ( !isset( $_POST['contact_nonce'] ) || !wp_verify_nonce( $_POST['contact_nonce'], plugin_basename( __FILE__ ) ) )
			return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
		$mydata = array(
			'target_email'	=>(isset($_POST['target_email'])?$_POST['target_email']:''),
			'html_map'		=>(isset($_POST['html_map'])?$_POST['html_map']:''),
		);
		update_post_meta( $post_id, 'contact_options', $mydata );
	}
	
	add_action( 'add_meta_boxes', 'dt_contact_box' );
	add_action( 'save_post', 'dt_contact_save_postdata' );