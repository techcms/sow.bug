<?php
	// featured_image_caption function!!!
	function the_post_thumbnail_caption( $img_id = null ) {
		if( $img_id ){
			$query = new Wp_Query(array('p' => $img_id, 'post_type' => 'attachment'));
			$thumbnail_image = $query->posts;
			if ($thumbnail_image && isset($thumbnail_image[0])) {
				return $thumbnail_image[0]->post_excerpt;
			}
		}else
			return '';
	}

	// with math algorithm
	function dt_get_image_size_math( array $o ){
		if( !is_array( $o) ) return false;
		if( !isset($o['img_h']) || !isset($o['img_w']) )
			return false;

		$img_h = $o['img_h'];
		$img_w = $o['img_w'];

		$h = isset( $o['height'])?$o['height']:0;
		$w = isset( $o['width'])?$o['width']:0;

		if( isset( $o['upscale'] ) && !$o['upscale']){
			$h = ( $h > 0 && $h > $img_h)?$img_h:$h;
			$w = ( $w > 0 && $w > $img_w)?$img_w:$w;
		}

		if ($w && !$h){
			$k = $img_w / $img_h;
			$h = intval($w / $k);
		}elseif (!$w && $h){
			$k = $img_w / $img_h;
			$w = intval($h * $k);
		}

		$res = array();
		$res[] = $w;
		$res[] = $h;
		$res[3] = 'width="'. $w. '" height="'. $h. '"';
		
		return $res;
	}
	
	//	return image href	//
	function dt_image_href( array $params ){
		// get img id
		if( isset($params['image_id']) ){
			$img_id = $params['image_id'];
		}elseif( isset($params['post_id']) ){
			$img_id = get_post_thumbnail_id( $params['post_id'] );
		}else
			return false;
			
		$o_width = isset( $params['width'] )?intval($params['width']):0;
		$o_height = isset( $params['height'] )?intval($params['height']):0;
		
		// get img src
		$img_meta = wp_get_attachment_image_src($img_id, 'full');
			
		// format image url
		if( $img_meta ){
			$img_src = str_replace( site_url(), "", $img_meta[0] );
		}else{
			$img_src = str_replace( site_url(), '', get_template_directory_uri(). '/images/noimage.jpg' ); // noimage image
		}
		
		if( isset($params['upscale']) && false == $params['upscale']){
			// get img metadata( height, width )
			if( $o_width > $img_meta[1] ){
				$o_width = $img_meta[1];
			}
		}
		
		$args = isset($params['no_limits'])?'&no_limits=true':'';
		
		$url_base = get_template_directory_uri(). '/resize.php?get_image=';
		$w = $o_width?'&w='.$o_width:'';
		$h = $o_height?'&h='.$o_height:'';
	
		$output = $url_base . $img_src . $w . $h. $args;
		
		if ( !isset($params['full_list']) || !$params['full_list'] ) {
			return esc_url($output);
		}else {
			return array( 
				'href'	=>esc_url($output),
				'size'	=>array(
					'width'		=>$img_meta[1],
					'height'	=>$img_meta[2]
				)
			);
		}
	}
	
	// get thumbnail
	// input array()
	// return array()
	function dt_get_thumbnail( array $o ) {
		$img_id = $images = false;
		$result = array();

		// get post id
		if( isset($o['post_id']) ){
			$p_id = $o['post_id'];
			unset($o['post_id']);
		}else{
			global $post;
			$p_id = $post->ID;
		}
		
		// get image_id
		if( isset($o['image_id']) ) {
			$img_id = intval( $o['image_id'] );
		}elseif( has_post_thumbnail($p_id) ) {
			$img_id = get_post_thumbnail_id( $p_id );
		}
		
		// set defaults if not set
		$default = array(	'width' 			=>630,
							'height'			=>0,
							'upscale'			=>false,
							'image_obj'			=>null,
							'full_list'			=>true,
							'noimage'			=>true
						);
		$args = wp_parse_args( $o, $default );
		
		if( !$args['image_obj'] &&
			!( !$img_id && $args['noimage']) ) {
			// get attachments
			$query = array( 	'post_type'			=>'attachment', 
								'post_mime_type'	=>'image',
								'post_status'		=>'inherit',
								'orderby'			=>'menu_order',
								'order'				=>'ASC',
								'numberposts'		=>1
							);
							
			if( $img_id ) {
				$query['p'] = $img_id;
			}else {
				$query['post_parent'] = $p_id;
			}
			
			$images = new Wp_Query( $query );
			$images = $images->posts;
		}elseif( $args['image_obj'] ) {
			$images = is_array( $args['image_obj'] )?$args['image_obj']:array( $args['image_obj'] );
		}
		
		// initialize result array
		if( $images ) {
			$image = array_shift($images);
			$args['image_id'] = $image->ID;
			$img_data = dt_image_href( $args );
				
			$img_info = $img_data['size'];
			$result['size'] = dt_get_image_size_math( array(	'img_h'		=>$img_info['height'],
																'img_w'		=>$img_info['width'],
																'width'		=>$args['width'],
																'height'	=>$args['height'],
																'upscale'	=>$args['upscale']		
															)
													);
			$result['t_href'] = $img_data['href'];
			$result['caption'] = esc_attr( $image->post_excerpt );
			$result['alt'] = esc_attr( get_post_meta($image->ID, '_wp_attachment_image_alt', true) );
			$result['b_href'] = current(wp_get_attachment_image_src($image->ID, 'full'));//dt_image_href( array('image_id'	=>$image->ID) );
		}else{
			$w = $args['width'];
			$h = $args['height']?$args['height']:$args['width'];
			$result['size'] = array(	0	=>$w,
										1	=>$h,
										3	=> 'width="'. $w. '" height="'. $h. '"'
									);
			$result['t_href'] = dt_image_href( array(	'image_id' 	=>false,
														'width'		=>$w,
														'height'	=>$h,
														'upscale'	=>$args['upscale']
													) );
			$result['b_href'] = '#';
			$result['caption'] = '';
			$result['alt'] = 'noimage';
		}
		
		return $result;
	}
	
	// gallery item
	function dt_gallary_item( $img = null, $t_size = 'default', $p_type = 'standart' ){
		$args = array(
			'upscale'	=>false,
//			'image_obj'	=>$img
		);
	
		if( $img ) {
			$args['image_obj'] = $img;
		}else {
			$args['image_id'] = null;
		}
/*		if( !$img )
			return '';
*/

		$type = '';
		switch( $t_size ){
			case 'small': $args['width'] = $args['height'] = 70; break;
			case 'massonry_gallery':
				$args['width'] = 240;
				$p_link = get_permalink( $GLOBALS['post']->ID );
				break;
			default : $args['width'] = 630; $type = ' class="alignleft photo"';
		}

		// link to full image
		$thumb = dt_get_thumbnail( $args );
		
		$thumb['b_href'] = isset($p_link)?$p_link:$thumb['b_href'];

		$out = <<<EOL
			<a title="{$thumb['caption']}" href="{$thumb['b_href']}"{$type}>
				<img {$thumb['size'][3]} src="{$thumb['t_href']}" alt="{$thumb['alt']}">
			</a>
EOL;
		return $out;		
	}

	// draw gallary images
	function dt_gallary_pt($type = 'plain_gallery') {
		global $post;

		$main_img = $img_list = '';
		$args = array(
			'post_parent' 		=> $post->ID, 
			'post_type' 		=> 'attachment', 
			'post_mime_type' 	=> 'image',
			'post_status' 		=> 'inherit',
			'orderby' 			=> 'menu_order',
			'order' 			=> 'ASC',
			'numberposts' 		=> 999
		);
		$images = get_children( $args );
		$arr_flag = count($images);
		if( has_post_thumbnail() ){
			$thumb_id = get_post_thumbnail_id( $post->ID );
			$main_img .= dt_gallary_item( $images[$thumb_id], $type );
			
			if( $exclude = get_post_meta( $post->ID, 'hide_in_gal', true ) ) {
				unset( $images[$thumb_id] );
			}
		}elseif( !empty($images) ){
			$image = current( $images );
			$main_img .= dt_gallary_item( $image, $type );
		}else {
			$main_img .= dt_gallary_item( null, $type );
		}
		
		foreach( $images as $image ){
			$img_list .= dt_gallary_item( $image, 'small' );
		}

		
		return array(
			'main_img'	=>$main_img,
			'img_list'	=>$img_list
		);
	}

	// function return css options 
	function dt_options_css(){
		$fix_pos = of_get_option( 'lv1_fix_bg_checkbox' )?'fixed':'scroll';
	?>
		<style type="text/css">
		/* bg 1 */
		body {
			background-color: <?php echo of_get_option( 'lv1_back_color_colorpicker' ); ?> !important;
			background-image: <?php echo dt_get_bg('lv1'); ?> !important;
			background-repeat: <?php echo of_get_option( 'lv1_repeat_select' ); ?> !important;
			background-position: <?php echo of_get_option('lv1_horiz_position_select') .' '. of_get_option( 'lv1_vert_position_select' ); ?> !important;
			background-attachment: <?php echo $fix_pos; ?> !important;
		}
	<?php $fix_pos = of_get_option( 'lv2_fix_bg_checkbox' )?'fixed':'scroll'; ?>
		/* bg 2 */
		#bg {
			background-image: <?php echo dt_get_bg('lv2'); ?> !important;
			background-repeat: <?php echo of_get_option( 'lv2_repeat_select' ); ?> !important;
			background-position: <?php echo of_get_option('lv2_horiz_position_select') .' '. of_get_option( 'lv2_vert_position_select' ); ?> !important;
			background-attachment: <?php echo $fix_pos; ?> !important;
		}
		<?php if( of_get_option( 'fonts_enable_upper_checkbox' ) ): ?>
		/* to upper */
		a.button.big span, #nav > li > a, .box-i-l .box-i-r span.grey, .widget .header, .search-f .p, .go_up, .foll .head {
			text-transform: uppercase !important;
		}
		<?php endif ?>
		</style>
	<?php
	}
	
	// return background image: $level = f/s/t
	function dt_get_bg( $level='lvl' ){
		if( of_get_option( $level. '_custom_bg_checkbox' ) ){
			$img = dt_unify_url( of_get_option( $level. '_custom_db_uploader' ) );
			if((strpos( $img, '.png') || strpos( $img, '.jpg') || strpos( $img, '.gif')) ){
				$output = $img;
			}
		}else{
			$output = dt_unify_url( of_get_option( $level. '_back_images' ) );
		}
		if( empty($output) || ('none' == $output)){
			return 'none';
		}
		return $output = 'url("' .$output. '")';
	}
	
	function dt_unify_url( $src ) {
		$uri = $src;
		if( !parse_url($src, PHP_URL_SCHEME) ) {
			if(  strpos($src, '/wp-content/') !== false )
				$uri = site_url($src);
			else
				$uri = get_template_directory_uri(). $src;
		}
		
		return $uri;
	}
	
	function dt_uploader_style_script( $id, $params = array() ) {

		$defaults = array(
			'align'			=> '.align',
			'image-size'	=> '.image-size',
			'post_content'	=> '.post_content',
			'url'			=> '.url',
			'submit'		=> '.submit input.button'
		);
		$params = wp_parse_args( $params, $defaults );
		$params = array_map( 'esc_attr', $params );
		$str = implode(', ', $params);
	?>
		<script type="text/javascript">
		jQuery('#<?php echo esc_attr($id); ?>').load( function() {
				var innerDoc = this.contentDocument || this.contentWindow.document;
					
				if( innerDoc ) {
					var css = '<?php echo $str; ?> { display: none; }',
						head = innerDoc.getElementsByTagName('head')[0],
						style = document.createElement('style');

						style.type = 'text/css';
						if(style.styleSheet){
							style.styleSheet.cssText = css;
						}else{
							style.appendChild(document.createTextNode(css));
						}
						head.appendChild(style);
				}
			});

		</script>
	<?php	
	}
	
	if( false ){
		add_editor_style();
		add_custom_image_header();
		add_custom_background();
		ob_start();
		post_class();
		ob_clean;
	}