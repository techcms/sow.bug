<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 * 
 */
	function optionsframework_option_name() {

		// This gets the theme name from the stylesheet (lowercase and without spaces)
		$themename = get_theme_data(STYLESHEETPATH . '/style.css');
		$themename = $themename['Name'];
		$themename = preg_replace("/\W/", "", strtolower($themename) );
		
		$optionsframework_settings = get_option('optionsframework');
		$optionsframework_settings['id'] = $themename;
		update_option('optionsframework', $optionsframework_settings);

	}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */

function optionsframework_options() {
	$fl_arr = array( 	'repeat'=>__( 'repeat', 'dt-options-repeat_select'),
						'repeat-x'=>__( 'repeat-x', 'dt-options-repeat_select'),
						'repeat-y'=>__( 'repeat-y', 'dt-options-repeat_select'),
						'no-repeat'=>__( 'no-repeat', 'dt-options-repeat_select'),
					);
	$v_arr = array( 	'center'=>__( 'center', 'dt-options-repeat_select'),
						'top'=>__( 'top', 'dt-options-repeat_select'),
						'bottom'=>__( 'bottom', 'dt-options-repeat_select'),
					);
	$h_arr = array( 	'center'=>__( 'center', 'dt-options-repeat_select'),
						'left'=>__( 'left', 'dt-options-repeat_select'),
						'right'=>__( 'right', 'dt-options-repeat_select'),
					);
	$options = array();
	
	//******************************APEARANCE*********************************//
	$options[] = array( "name" => __( "Appearance", 'dt-options-name'),
						"type" => "heading");

	//LAYOUT OPTIONS
	// >>>
	$options[] = array(	"name" => __( 'Layout options', 'dt-options-name'),
						"type" => "block_begin");
	
	// pagination option
	$options[] = array( "name" => "",
						"desc" => __( "Show all pages in paginator", 'dt-options-desc' ),
						"id" => "layout_paginator_show_all_checkbox",
						"std" => "1",
						"type" => "checkbox");
	
	// blog layout type
/*	$options[] = array( "name" => '',
						"desc" => __( 'Blog archive layout', 'dt-options-desc' ),
						"id" => "blog_layout_type_select",
						"std" => "standard",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => array(	'standard'	=>"Standard",
											'masonry'	=>"Masonry"
											) );
*/	
	// <<<
	$options[] = array(	"type" => "block_end");
	
	//BRANDING
	// >>>
	$options[] = array(	"name" => __( 'Branding', 'dt-options-name'),
						"type" => "block_begin");
	
	// favicon
	$options[] = array( "name" => '',
						"desc" => __( "Favicon", 'dt-options-desc'),
						"id" => "appearance_favicon_uploader",
						"type" => "upload");
	
	// logo
	$options[] = array( "name" => "",
						"std"	=> '/images/logo.png',
						"desc" => __( "Logo", 'dt-options-desc'),
						"id" => "appearance_logo_uploader",
						"type" => "upload");
	
	// mobile logo
	$options[] = array( "name" => "",
						"std"	=> '/images/logo.png',
						"desc" => __( "Mobile Logo", 'dt-options-desc'),
						"id" => "appearance_mobile_logo_uploader",
						"type" => "upload");
	
	// <<<
	$options[] = array(	"type" => "block_end");

	//MENU
	// >>>
	$options[] = array(	"name" => __( 'Menu', 'dt-options-name'),
						"type" => "block_begin");
	// parent menu clickable
	$options[] = array( "name" => "",
						"desc" => __( "make parent menu items clickable", 'dt-options-desc' ),
						"id" => "menu_parent_menu_clickable_checkbox",
						"std" => "1",
						"type" => "checkbox");
	// <<<
	$options[] = array(	"type" => "block_end");
	
	// sidebar
	$options[] = array(	"name" => _x( 'Sidebar', 'theme options', LANGUAGE_ZONE ), "type" => "block_begin" );

		// show credits
		$options[] = array( "name" => '',
							"desc" => _x( 'Hide in mobile layout ', 'theme options', LANGUAGE_ZONE ),
							"id" => "cc_hide_mobile_checkbox",
							"std" => "1",
							"type" => "checkbox");
	
	$options[] = array(	"type" => "block_end");
	
	//RESPONSIVNESS
	// >>>
	$options[] = array(	"name" => _x( 'Responsivness', 'theme options', LANGUAGE_ZONE ),
						"type" => "block_begin");
	// parent menu clickable
	$options[] = array( "name" => "",
						"desc" => _x( "Turn OFF responsivness", 'theme options', LANGUAGE_ZONE ),
						"id" => "turn_off_responsivness",
						"std" => "0",
						"type" => "checkbox");
	// <<<
	$options[] = array(	"type" => "block_end");
	
	// sidebar
	$options[] = array(	"name" => _x( 'Sidebar', 'theme options', LANGUAGE_ZONE ), "type" => "block_begin" );

		// show credits
		$options[] = array( "name" => '',
							"desc" => _x( 'Hide in mobile layout ', 'theme options', LANGUAGE_ZONE ),
							"id" => "cc_hide_mobile_checkbox",
							"std" => "1",
							"type" => "checkbox");
	
	$options[] = array(	"type" => "block_end");
	
	//FONTS
	// >>>
	$options[] = array(	"name" => __( 'Fonts', 'dt-options-name'),
						"type" => "block_begin");
	// enable uppercase
	$options[] = array( "name" => '',
						"desc" => __( "Use upper case for headings", 'dt-options-desc' ),
						"id" => "fonts_enable_upper_checkbox",
						"std" => "1",
						"type" => "checkbox");
	// enable cufon
	$options[] = array( "name" => '',
						"desc" => __( "Enable Cufon", 'dt-options-desc' ),
						"id" => "fonts_enable_cufon_checkbox",
						"std" => "1",
						"type" => "checkbox");
	// cufon list
	$options[] = array( "name" => '',
						"desc" => __( 'Select cufon from the list', 'dt-options-desc' ),
						"id" => "fonts_select",
						"std" => "/fonts/DejaVu_Serif_Condensed_700.font.js",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => dt_get_fonts_in( 'fonts' ) );
	// upload custom
	$options[] = array( "name" => '',
						"desc" => __( "Upload your own Cufon font ", 'dt-options-desc' ),
						"id" => "fonts_enable_custom_checkbox",
						"std" => "0",
						"type" => "checkbox");
	// uploader
	$options[] = array( "name" => "",
						"desc" => __( "Upload", 'dt-options-desc'),
						"id" => "fonts_custom_uploader",
						"type" => "upload");
	// <<<
	$options[] = array(	"type" => "block_end");

	//COPYRIGHT & CREDITS
	// >>>
	$options[] = array(	"name" => __( 'Copyright', 'dt-options-name' ) ,
						"type" => "block_begin");
	// copuright text
	$options[] = array( "name" => '',
						"desc" => '',
						"id" => "cc_copy_info_textarea",
						"std" => false,
						"type" => "textarea");
	// show credits
	$options[] = array( "name" => '',
						"desc" => __( 'Show Credits', 'dt-options-desc' ),
						"id" => "cc_show_credits_checkbox",
						"std" => "1",
						"type" => "checkbox");
	// <<<
	$options[] = array(	"type" => "block_end");

	//******************************BACKGROUNDS*********************************//					
	$options[] = array( "name" => __( "Backgrounds", 'dt-options-name'),
						"type" => "heading");
	//Background level 1
	// >>>
	$options[] = array(	"name" => __( 'Background level 1', 'dt-options-name'),
						"type" => "block_begin");
	// bg color
	$options[] = array( "name" => '',
						"desc" => __( 'Background color', 'dt-options-desc' ),
						"id" => "lv1_back_color_colorpicker",
						"std" => "#EDEDED",
						"type" => "color");
	// bg image
	$options[] = array( "name" => '',
						"desc" => __( 'Background image', 'dt-options-desc' ),
						"id" => "lv1_back_images",
						"std" => "/backgrounds/lv1/full/22-noise-white.png",
						"type" => "images",
						"options" => dt_get_images_in( 'backgrounds/lv1' )
						);
	// upload bg image
	$options[] = array( "name" => '',
						"desc" => __( 'Upload your own image', 'dt-options-desc' ),
						"id" => "lv1_custom_bg_checkbox",
						"std" => "0",
						"type" => "checkbox");
	// uploader
	$options[] = array( "name" => "",
						"desc" => "",
						"id" => "lv1_custom_db_uploader",
						"type" => "upload");
	// repeat
	$options[] = array( "name" => '',
						"desc" => __( 'Repeat', 'dt-options-desc' ),
						"id" => "lv1_repeat_select",
						"std" => "repeat",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => $fl_arr);
	// vertical
	$options[] = array( "name" => '',
						"desc" => __( 'Vertical position', 'dt-options-desc' ),
						"id" => "lv1_vert_position_select",
						"std" => "center",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => $v_arr);
	// horizontal
	$options[] = array( "name" => '',
						"desc" => __( 'Horizontal position', 'dt-options-desc' ),
						"id" => "lv1_horiz_position_select",
						"std" => "left",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => $h_arr);
	// fix background
	$options[] = array( "name" => '',
						"desc" => __( 'Fix background', 'dt-options-desc' ),
						"id" => "lv1_fix_bg_checkbox",
						"std" => "0",
						"type" => "checkbox");
	// use full width image 
/*	$options[] = array( "name" => '',
						"desc" => __( 'Use full width image ', 'dt-options-desc' ),
						"id" => "lv1_use_fw_img_checkbox",
						"std" => "0",
						"type" => "checkbox");
*/	// <<<
	$options[] = array(	"type" => "block_end");

	//Background level 2
	// >>>
	$options[] = array(	"name" => __( 'Background level 2', 'dt-options-name'),
						"type" => "block_begin");
	// bg image
	$options[] = array( "name" => '',
						"desc" => __( 'Background image', 'dt-options-desc' ),
						"id" => "lv2_back_images",
						"std" => "/backgrounds/lv2/full/01-small-lines-for-white.png",
						"type" => "images",
						"options" => dt_get_images_in( 'backgrounds/lv2' )
						);
	// upload bg					
	$options[] = array( "name" => '',
						"desc" => __( 'Upload your own image', 'dt-options-desc' ),
						"id" => "lv2_custom_bg_checkbox",
						"std" => "0",
						"type" => "checkbox");
	// uploader
	$options[] = array( "name" => "",
						"desc" => "",
						"id" => "lv2_custom_db_uploader",
						"type" => "upload");
	// repeat
	$options[] = array( "name" => '',
						"desc" => __( 'Repeat', 'dt-options-desc' ),
						"id" => "lv2_repeat_select",
						"std" => "repeat",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => $fl_arr);
	// vertical
	$options[] = array( "name" => '',
						"desc" => __( 'Vertical position', 'dt-options-desc' ),
						"id" => "lv2_vert_position_select",
						"std" => "center",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => $v_arr);
	// horizontal
	$options[] = array( "name" => '',
						"desc" => __( 'Horizontal position', 'dt-options-desc' ),
						"id" => "lv2_horiz_position_select",
						"std" => "left",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => $h_arr);
	// fix background
	$options[] = array( "name" => '',
						"desc" => __( 'Fix background', 'dt-options-desc' ),
						"id" => "lv2_fix_bg_checkbox",
						"std" => "0",
						"type" => "checkbox");
	// <<<
	$options[] = array(	"type" => "block_end");
						
	//******************************MISC**************************************//					
	$options[] = array( "name" => __( "Misc", 'dt-options-name'),
						"type" => "heading");
	//ANALITICS CODE
	// >>>
	$options[] = array(	"name" => __( 'Analitics code', 'dt-options-name'),
						"type" => "block_begin");
	// anal code
    $options[] = array( "name" => '',
						"desc" => '',
						"id" => "misc_a_code_textarea",
						"std" => false,
						"type" => "textarea",
						"sanitize"	=>false );
	// <<<
	$options[] = array(	"type" => "block_end");
	
	//SOCIAL LINKS
	// >>>
	$options[] = array(	"name" => __( 'Social links', 'dt-options-name'),
						"type" => "block_begin",
						"id"   => "misc_soc_links");

	$soc_links = dt_get_soc_links('backend');
	foreach( $soc_links as $name=>$class) {
		$options[] = array( "name" => '',
			"desc" => ucfirst(strtolower($name)),
			"id" => "misc_".$class."_checkbox",
			"std" => "0",
			"type" => "checkbox"
		);
		$options[] = array(
			"name" => '',
			"desc" => "link",
			"id" => "misc_".$class."_text",
			"std" => '',
			"type" => "text"
		);
	}
	// <<<
	$options[] = array(	"type" => "block_end");
	
	// LIKE PANEL
	// >>>
	$options[] = array(	"name" => __( 'Social Likes Panel', 'dt-options-name'),
						"type" => "block_begin",
						"id"   => "misc_soc_panel_links");

	$options[] = array( "name" => '',
						"desc" => 'Enable Panel',
						"id" => "misc_like_panel_checkbox",
						"std" => "0",
						"type" => "checkbox"
					);
	
	$options[] = array( "name" => '',
						"desc" => 'Panel is displayed by default',
						"id" => "misc_display_like_panel_checkbox",
						"std" => "0",
						"type" => "checkbox"
					);

	$options[] = array( "name" 		=> '',
						"desc" 		=> 'Insert the "like" buttons code here',
						"id" 		=> "misc_likes_code_textarea",
						"std" 		=> false,
						"type" 		=> "textarea",
						"sanitize"	=>false );
	
	// <<<
	$options[] = array(	"type" => "block_end");
	
	$options[] = array(
		"name" => _x( 'Attachments', 'backend', LANGUAGE_ZONE),
		"type" => "block_begin",
		"id"   => "misc_attachments"
	);
						
		$options[] = array(
			"name"	=> '',
			"desc" 	=> _x( 'Enable comments', 'backend', LANGUAGE_ZONE),
			"id" 	=> "misc_attachments_enable_comments",
			"std" 	=> "0",
			"type" 	=> "checkbox"
		);
	
	$options[] = array(	"type" => "block_end");
	
    if( $captcha_opts = locate_template('plugins/captcha/options.php') ) {
        include $captcha_opts;
    }
    
	return $options;
}