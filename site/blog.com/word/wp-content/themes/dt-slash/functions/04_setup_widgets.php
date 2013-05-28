<?php
	 
	// Include widgets/*.php
	include_files_in_dir("/widgets/");
   
	function dt_widgets_init() {

		global $left_block_args;
		register_sidebar( $left_block_args=array(
			'name' => __( 'PRIMARY widget area', 'dt' ),
			'id' => 'primary-widget-area',
			'description' => __( 'Left block', 'dt' ),
			'before_widget' => '<div class="wrap-widget"><div class="widget-t"></div><div class="widget">',
			'after_widget' => '</div><div class="widget-b"></div></div>',
			'before_title' => '<div class="header">',
			'after_title' => '</div>'
		) );
		
		register_sidebar( $left_block_args=array(
			'name' => __( 'HOMEPAGE widget area', 'dt' ),
			'id' => 'homepage-widget-area',
			'description' => __( 'Left block', 'dt' ),
			'before_widget' => '<div class="wrap-widget"><div class="widget-t"></div><div class="widget">',
			'after_widget' => '</div><div class="widget-b"></div></div>',
			'before_title' => '<div class="header">',
			'after_title' => '</div>'
		) );
		
	}
	/** Register sidebars by running megapolis_widgets_init() on the widgets_init hook. */
	add_action( 'widgets_init', 'dt_widgets_init' );