<?php
// custon columns

// portfolio
add_filter('manage_edit-portfolio_columns', 'dt_columns', 5);
// slider
add_filter('manage_edit-main_slider_columns', 'dt_columns', 5);
// benefits
add_filter('manage_edit-dt_gallery_columns', 'dt_columns', 5);

// for portfolio and slider
function dt_columns($defaults){
    $defaults['dt_thumbs'] = __('Thumbs', 'dt');
    return $defaults;
}

add_action('manage_posts_custom_column', 'dt_custom_columns', 5, 2);

function dt_custom_columns($column_name, $id){
	if($column_name === 'dt_thumbs'){
		$args = array( 
			'post_id' 	=> $id,
			'width' 	=> 100,
			'height' 	=> 100,
			'upscale'	=>true
		);
		
		printf( '<a href="post.php?post=%d&action=edit" title=""><img src="%s" alt=""/></a>',
			$id,
			dt_image_href( $args )
		);
    }
}

// end

function my_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('my-upload', get_template_directory_uri().'/js/admin-uploader-script.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
}

function my_admin_styles() {
	wp_enqueue_style('thickbox');
}



