<?php
if ( ! isset( $content_width ) ) $content_width = 630;
/* Set up theme defaults and registers support for various WordPress features. */
if ( ! function_exists( 'dt_init' ) ){

	function include_files_in_dir($dir, $no_more=FALSE){
	  $dir_init = $dir;
	  $dir = dirname(__FILE__).$dir;
	  
	  if (!file_exists($dir))
		 throw new Exception("Folder $dir does not exist");
		 
	  $files = array();
		 
	  if ($handle = opendir( $dir )) {
		  while (false !== ($file = @readdir($handle))) {
			  if ( is_dir( $dir.$file ) && !preg_match('/^\./', $file) && !$no_more )
			  {
				 include_files_in_dir($dir_init.$file."/", TRUE);
			  }
			  else
			  {
				 if ( preg_match('/^[^~]{1}.*\.php$/', $file) ) {
					 $files[] = $dir.$file;
				 }
			  }
		  }
		  @closedir($handle);
	  }      
	  
	  sort($files);
	  
	  foreach ($files as $file)
		 include_once $file;
	}

	function dt_init(){
				
		/* menu slot */
		global $dt_post_formats;
		$dt_post_formats = array( 'gallery', 'status' );
		register_nav_menu( 'primary-menu', _x( 'Primary Menu', 'backend', 'dt' ) );
		add_theme_support( 'post-formats', $dt_post_formats );
		
		if ( function_exists( 'add_theme_support' ) ) { 
			/* add theme support images */
			add_theme_support( 'post-thumbnails' );
			/* add automatic feeds support */
			add_theme_support( 'automatic-feed-links' );				
		}
					
		// Include functions/*.php
		include_files_in_dir("/functions/");
		
		// Include plugins/*/*.php
		include_files_in_dir("/plugins/");

		// Include settings/*.php
		//include_files_in_dir("/settings/");
		
		
		if( function_exists( 'load_theme_textdomain' ) ) {
			load_theme_textdomain( LANGUAGE_ZONE , get_template_directory(). '/languages' );
		}
		
		//remove in production , for demo stand only!!!
		if( function_exists( 'optionsframework_dt_presets' ) ){
			optionsframework_dt_presets();
		}
		
		// disable attachments comments if needed
		if( !of_get_option('misc_attachments_enable_comments', false) ) {
			// filter function in /functions/03_filters.php
			add_filter( 'comments_open', 'dt_disable_attachment_comments', 99, 2 );
		}
	}
	add_action( 'after_setup_theme', 'dt_init' );
}

// make permalinks work after theme switch
function dt_rewrite_flush() {
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'dt_rewrite_flush' );

remove_action( 'wp_head', 'feed_links_extra', 3);
?>