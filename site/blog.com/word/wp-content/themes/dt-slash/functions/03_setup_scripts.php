<?php
function dt_setup_scripts(){
	$post_id = isset($GLOBALS['post']->ID)?$GLOBALS['post']->ID:false;
	$template_file = get_post_meta( $post_id, '_wp_page_template', TRUE );
	$jwplayer_flag = file_exists( get_template_directory().'/js/jwplayer/jwplayer.js' );

	// jQuery
	wp_deregister_script('jquery');
	wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js', array(), '1.7.0');
	wp_enqueue_script('jquery');
			
	// register scripts
	wp_register_script( 'cufon-yui', get_template_directory_uri().'/js/cufon-yui.js', array('jquery') );
	wp_register_script( 'cufon-colors', get_template_directory_uri().'/js/cufon-colors.js', array('jquery', 'cufon-yui') );
	wp_register_script( 'jquery-placeholder', get_template_directory_uri().'/js/plugins/placeholder/jquery.placeholder.js', array('jquery') );
	wp_register_script( 'jquery-validationEngine', get_template_directory_uri().'/js/plugins/validator/jquery.validationEngine.js', array('jquery') );
	wp_register_script( 'jquery-isotope', get_template_directory_uri().'/js/jquery.isotope.min.js', array('jquery') );
	wp_register_script( 'jquery-jplayer', get_template_directory_uri().'/js/jplayer/jquery.jplayer.min.js', array('jquery') );
	wp_register_script( 'jquery-easing', get_template_directory_uri().'/js/jquery.easing.1.3.js', array('jquery'), '1.3' );
	wp_register_script( 'z.trans', get_template_directory_uri().'/js/plugins/validator/z.trans.en.js', array('jquery', 'jquery-validationEngine') );
	wp_register_script( 'jwplayer', get_template_directory_uri().'/js/jwplayer/jwplayer.js' );
	wp_register_script( 'raphael-min', get_template_directory_uri().'/js/raphael-min.js', array('jquery') );
	wp_register_script( 'highslide-full', get_template_directory_uri().'/js/plugins/highslide/highslide-full.js', array('jquery') );
	wp_register_script( 'highslide-config', get_template_directory_uri().'/js/plugins/highslide/highslide.config.js', array('jquery', 'highslide-full') );
	wp_register_script( 'dt_wipe', get_template_directory_uri().'/js/jquery.wipetouch.js' );
	wp_register_script( 'dt_custom', get_template_directory_uri().'/js/custom.js' );
	wp_register_script( 'dt_scripts', get_template_directory_uri().'/js/scripts.js' );
	wp_register_script( 'dt_slider', get_template_directory_uri().'/js/slider.js', array('jquery') );
	wp_register_script( 'dt_shortcodes', get_template_directory_uri().'/js/shortcodes.js' );
	
	// enqueue scripts
			
	// cufon fonts
	wp_enqueue_script( 'cufon-yui' );
	// plase for cufon scripts
	if( of_get_option('fonts_enable_cufon_checkbox', true) ){
		// get font selected in selec
		$font_select = of_get_option( 'fonts_select' );
		// if custom upload checked
		if( of_get_option( 'fonts_enable_custom_checkbox', false ) ){
			// get font from uploder
			$font_upload = of_get_option( 'fonts_custom_uploader' );
			// if font from uploader exists and hafe .js in its path
			if( $font_upload && strpos($font_upload, '.js') ){
				// add upladed font
				wp_enqueue_script( 'cufon-font', dt_unify_url($font_upload) );
			}else{
				// add font from select
				wp_enqueue_script( 'cufon-font', dt_unify_url($font_select) );
			}
		}else{
			// add font from select
			wp_enqueue_script( 'cufon-font', dt_unify_url($font_select) );
		}
	}
	wp_enqueue_script( 'cufon-colors' );
	wp_enqueue_script( 'jquery-easing' );
	wp_enqueue_script( 'highslide-full' );
	wp_enqueue_script( 'highslide-config' );
	
/*	if( is_archive() ) {
		wp_enqueue_script( 'raphael-min' );
		wp_enqueue_script( 'jquery-isotope' );
	}
*/	
	switch( $template_file ) {
		case 'home-video.php':
			if( $jwplayer_flag ) {
				wp_enqueue_script( 'jwplayer' );
			}else {
				wp_enqueue_script( 'jquery-jplayer' );
			}
			break;
		case 'home-light.php':
			wp_enqueue_script( 'raphael-min' );
		case 'home-static.php':
			wp_enqueue_script( 'dt_slider' );
			break;
		case ('blog-masomry.php' || 'photos.php' || 'gallery.php' || 'portfolio-masonry.php'):
			wp_enqueue_script( 'raphael-min' );
		default:
			wp_enqueue_script( 'jquery-placeholder' );
			wp_enqueue_script( 'jquery-validationEngine' );
			wp_enqueue_script( 'z.trans' );
			
	}
	wp_enqueue_script( 'dt_wipe' );
	wp_enqueue_script( 'jquery-isotope' );
	wp_enqueue_script( 'dt_scripts' );
	wp_enqueue_script( 'dt_shortcodes' );
	wp_enqueue_script( 'dt_custom' );	
	
	if ( is_singular() && get_option( 'thread_comments' ) && (false === strpos($template_file, 'home')) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
	
if( !is_admin() ) {
    add_action('wp_enqueue_scripts', 'dt_setup_scripts');
}