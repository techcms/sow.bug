<?php
	
	
	
   define('THEME_TITLE',              'DT :');
   define('LANGUAGE_ZONE',            'dt');
   define('SHORT_NAME',               'slash');
   
	define('TMP_DIR',                  dirname(__FILE__)."/../../../uploads/dt_cache/" );
	define('TMP_DIR2',                 dirname(__FILE__)."/../../../" );
	
   define('DEMO',                     false);
   define('SHOW_EXCERPT',             false);
   
   define('LARGE_SIZE',               get_option('large_size_w'));
   define('EX_CATS_SM',					'');
   
   define('CLASS_FULL_WIDTH',			' full_width');
   define('CLASS_FULL_WIDTH_COLS',		' full_width_cols');
   define('CLASS_COLS',					' clear_cols');
   
	function dt_get_home_slider_defaults() {
		static $defaults = array(	'dt_static_desc'		=>false,
									'dt_hide_over_mask'		=>false,
									'dt_pres_def_s_prop'	=>false,
									'dt_timing'				=>5,
									'dt_autoplay'			=>false );
		return $defaults;
	}

