<?php
function dt_setup_styles() {
	$theme  = get_theme( get_current_theme() );
	wp_register_style( 'dt_html5reset', get_template_directory_uri().'/css/html5reset.css', array(), $theme['Version'] );
	wp_register_style( 'dt_style', get_template_directory_uri().'/css/style.css', array(), $theme['Version'] );
	wp_register_style( 'dt_skin', get_template_directory_uri().'/css/skin.css', array(), $theme['Version'] );
	wp_register_style( 'dt_validator', get_template_directory_uri().'/js/plugins/validator/validationEngine.jquery.css', array(), $theme['Version'] );
	wp_register_style( 'dt_highslide', get_template_directory_uri().'/js/plugins/highslide/highslide.css', array(), $theme['Version'] );
	wp_register_style( 'dt_home', get_template_directory_uri().'/css/home.css', array(), $theme['Version'], 'screen' );
	wp_register_style( 'dt_shortcodes', get_template_directory_uri().'/css/shortcodes.css', array(), $theme['Version'], 'screen' );
	wp_register_style( 'dt_wp', get_template_directory_uri().'/css/wp.css', array(), $theme['Version'], 'screen' );
	wp_register_style( 'dt_custom', get_template_directory_uri().'/css/custom.css', array(), $theme['Version'], 'screen' );
	wp_register_style( 'dt_like', get_template_directory_uri().'/css/like.css', array(), $theme['Version'], 'screen' );
	
	
	wp_register_style( 'dt_media', get_template_directory_uri().'/css/media.css', array(), $theme['Version']);
	
	
	// IE7
	wp_register_style( 'dt_old_ie', get_template_directory_uri().'/css/old_ie.css', array(), $theme['Version'], 'all' );
	$GLOBALS['wp_styles']->add_data( 'dt_old_ie', 'conditional', 'lte IE 7' );
	
	wp_register_style( 'dt_hs_ie6', get_template_directory_uri().'/js/plugins/highslide/highslide-ie6.css', array(), $theme['Version'] );
	$GLOBALS['wp_styles']->add_data( 'dt_hs_ie6', 'conditional', 'lte IE 7' );
	// ^IE7
	
	wp_enqueue_style( 'dt_html5reset' );
	wp_enqueue_style( 'dt_style' );
	wp_enqueue_style( 'dt_skin' );
	wp_enqueue_style( 'dt_highslide' );
	wp_enqueue_style( 'dt_hs_ie6' );
	wp_enqueue_style( 'dt_old_ie' );
	wp_enqueue_style( 'dt_shortcodes' );
	wp_enqueue_style( 'dt_wp' );
	wp_enqueue_style( 'dt_custom' );

	if ( ! of_get_option( 'turn_off_responsivness', false ) ) {
		wp_enqueue_style( 'dt_media' );
	}
	
	if( is_page_template('home-static.php') || is_page_template('home-light.php') || is_page_template('home-video.php')) {
		wp_enqueue_style( 'dt_home' );
	}else {
		wp_enqueue_style( 'dt_validator' );
	}
	if( of_get_option('misc_like_panel_checkbox', false) ) {
		wp_enqueue_style( 'dt_like' );
	}
}

add_action( 'wp_enqueue_scripts', 'dt_setup_styles' );