<?php
	// function that get array of cufon fonts
	function dt_get_fonts_in( $dir = 'fonts' ){
		$res = array();
		$dirname = dirname(__FILE__). '/../' .$dir;
		if ($handle = opendir( $dirname ) ) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$f_name = preg_split( '/\.[^.]+$/', $file );
					$res['/' . $dir . '/' .$file] = $f_name[0];
				}
			}
			closedir($handle);
		}
		if( empty($res) ){
			$res['none'] = __( 'no fonts', 'dt-options-fonts_select');
		}
		return $res;
	}
	
	// get images for options framework
	function dt_get_images_in( $dir = '' ){
		$noimage = get_stylesheet_directory_uri(). '/images/noimage_small.jpg';
		$dirname = dirname(__FILE__). '/../' .$dir;
		$res = $full_dir = $thumbs_dir = array();
		$res['none'] = $noimage;
		
		// full dir
		if ( file_exists($dirname. '/full') && $handle = opendir( $dirname. '/full') ) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && $file != 'Thumb.db' && $file != 'Thumbs.db') {
					$f_name = preg_split( '/\.[^.]+$/', $file );
					$full_dir[$f_name[0]] = $file;
				}
			}
			closedir($handle);
		}
		unset($file);
		
		// thumbs dir
		if ( file_exists($dirname. '/thumbs') && $handle = opendir( $dirname. '/thumbs') ) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && $file != 'Thumb.db' && $file != 'Thumbs.db') {
					$f_name = preg_split( '/\.[^.]+$/', $file );
					$thumbs_dir[$f_name[0]] = $file;
				}
			}
			closedir($handle);
		}
		unset($file);
		asort($full_dir);
		
		foreach( $full_dir as $name=>$file ){
			$full_link = '/' . $dir . '/full/' . $file;
			if( array_key_exists( $name, $thumbs_dir ) ){
				$thumb_link = '/' . $dir . '/thumbs/' . $thumbs_dir[$name];
			}else{
				$thumb_link = $noimage;
			}
			$res[$full_link] = $thumb_link;
		}
		
		return $res;
	}

	// function return list of social links
	function dt_get_soc_links( $type = 'frontend'){
		// links list
		$links = array(
			'MYSPACE'		=>'ico_MySpace',
			'GOOGLEPLUS'	=>'ico_google-plus',
			'TUMBLR'		=>'ico_tumblr',
			'RSS'			=>'ico_rss',
			'FACEBOOK'		=>'ico_facebook',
			'TWITTER'		=>'ico_twitter',
			'FORRST'		=>'ico_forrst',
			'VIMEO'			=>'ico_vimeo',
			'DRIBBBLE'		=>'ico_dribbble',
			'FLICKR'		=>'ico_flickr',
			'YOUTUBE'		=>'ico_youTube',
			'LINKEDIN'		=>'ico_linkedin',
			'DELICIOUS'		=>'ico_delicious',
			'DIGG'			=>'ico_digg',
			'BEHANCE'		=>'ico_behance',
			'DEVIAN-ART'	=>'ico_devian-art',
			'PICASA'		=>'ico_picasa',
			'PLIXI'			=>'ico_Plixi',
			'MOBYPICTURE'	=>'ico_MobyPicture',
			'STUBLEUPON'	=>'ico_StubleUpon',
			'DROPBOX'		=>'ico_Dropbox',
			'SKYPE'			=>'ico_Skype'
		);
		
		if( 'frontend' == $type ) {
			$out_links = '';
			foreach( $links as $l_name=>$class ){
				$o_class = str_replace('-', '', strtolower($class));
				if( of_get_option( 'misc_' .$o_class. '_checkbox' ) ){
					$href = of_get_option( "misc_" .$o_class. "_text" );
					$out_links .= <<<HDOCK
					<div class="ico-l">
						<a class="$class" href="$href" target="_blank">
						</a>
						<div class="info-block">
							$l_name
						</div>
					</div>
HDOCK;
				}
			}
			
			return $out_links;
		}else {
			return $links;
		}
	}
	
	// options add custom scripts
	add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');
	function optionsframework_custom_scripts() { ?>

	<script type="text/javascript">
	jQuery(document).ready(function() {
		// appearance
		
		// fonts - enable_cufon
		jQuery('#fonts_enable_cufon_checkbox').click(function() {
			jQuery(	'#section-fonts_select').fadeToggle(400);
			jQuery(	'#section-fonts_enable_custom_checkbox').fadeToggle(400);
		});
		
		if (jQuery('#fonts_enable_cufon_checkbox:checked').val() !== undefined) {
			jQuery(	'#section-fonts_select').show();
			jQuery(	'#section-fonts_enable_custom_checkbox').show();
		}else{
			jQuery(	'#section-fonts_select').hide();
			jQuery(	'#section-fonts_enable_custom_checkbox').hide();
		}
		
		// fonts - custom_upload
		jQuery('#fonts_enable_custom_checkbox').click(function() {
			jQuery(	'#section-fonts_custom_uploader').fadeToggle(400);
		});
		
		if (jQuery('#fonts_enable_custom_checkbox:checked').val() !== undefined) {
			jQuery(	'#section-fonts_custom_uploader').show();
		}else{
			jQuery(	'#section-fonts_custom_uploader').hide();
		}
		
		// about - about_textarea
		jQuery('#about_show_about_checkbox').click(function() {
			jQuery(	'#section-about_textarea').fadeToggle(400);
		});
		
		if (jQuery('#about_show_about_checkbox:checked').val() !== undefined) {
			jQuery(	'#section-about_textarea').show();
		}else{
			jQuery(	'#section-about_textarea').hide();
		}
		
		// background
		// lv1 custom bg
		jQuery('#lv1_custom_bg_checkbox').click(function() {
			jQuery(	'#section-lv1_custom_db_uploader').fadeToggle(400);
		});
		
		if (jQuery('#lv1_custom_bg_checkbox:checked').val() !== undefined) {
			jQuery(	'#section-lv1_custom_db_uploader').show();
		}else{
			jQuery(	'#section-lv1_custom_db_uploader').hide();
		}
		// lv2 custom bg
		jQuery('#lv2_custom_bg_checkbox').click(function() {
			jQuery(	'#section-lv2_custom_db_uploader').fadeToggle(400);
		});
		
		if (jQuery('#lv2_custom_bg_checkbox:checked').val() !== undefined) {
			jQuery(	'#section-lv2_custom_db_uploader').show();
		}else{
			jQuery(	'#section-lv2_custom_db_uploader').hide();
		}
		
		// MISC	 
		// soc block
		jQuery('#misc_soc_links .section-checkbox input[type="checkbox"]').click(function() {
			jQuery(this).parents('.section-checkbox').next('#misc_soc_links .section-text').fadeToggle(400);
			console.log(this);
		});
		
		jQuery('#misc_soc_links .section-checkbox input[type="checkbox"]').not(':checked').each(function( i ){
			jQuery(this).parents('.section-checkbox').next('#misc_soc_links .section-text').hide();
			console.log(this);
		});
	});
	</script>
	 
	<?php
	}