<?php
/* Template Name: Homepage - slideshow */
?>
<?php get_header() ?>

	<?php get_template_part('aside') ?>
	<div id="top_bg"></div>
<?php
	global $wp_query;
	global $post;
	
	// get homepage data before changing query
	$homepage = $post;
	$homepage_data = get_post_meta( $homepage->ID, 'dt_homepage_options', true );
	$homepage_data = wp_parse_args( $homepage_data, dt_get_home_slider_defaults() );
	
	// preserve default slide proportions
	if ( $homepage_data['dt_pres_def_s_prop'] ) {
		echo '<script type="text/javascript">resize_me = 0;</script>';
	}
	
	$args = array(	'post_type'			=>'main_slider',
					'posts_per_page'	=>999 );

	if( isset($homepage_data['only']) ) {
		$trms = $homepage_data['only'];
		$op = 'IN';
	}elseif( isset($homepage_data['except']) ) {
		$trms = $homepage_data['except'];
		$op = 'NOT IN';
	}
	
	if( isset($trms) ) {
		$args['tax_query'] = array(	array(	'taxonomy'	=>'slider-category',
											'field'		=>'id',
											'terms'		=>$trms,
											'operator' 	=>$op ) );
	}
	
	// begin manipulations
	$temp = $wp_query;
	$wp_query = new Wp_Query( $args );
	$count = 1;
	$pg_last = count($wp_query->posts);
	$pg_preview = $pg_desc_title = $pg_desc = $thumb = '';
	if( $wp_query->have_posts() ){
		while( $wp_query->have_posts() ){
			$wp_query->the_post();
			
			$slider_meta = get_post_meta( $post->ID, 'slider_meta', true );
			$args = array( 'post_id'=>$post->ID, 'no_limits'=>true, 'full_list'=>true );
			
			// big image section
			$thumb_id = get_post_thumbnail_id( $post->ID );
			$img_src = wp_get_attachment_image_src( $thumb_id, 'full' );
			$alt = get_post_meta($thumb_id,'_wp_attachment_image_alt', true );
			
			$img_meta = array(
				'href'	=> $img_src[0],
				'size'	=> array(
					'width'		=> $img_src[1],
					'height'	=> $img_src[2]
				)
			);
			
			//$img_meta = dt_image_href( $args );

			if( !( empty($img_meta['size']['width']) && empty($img_meta['size']['height']) ) ){
				$w = $img_meta['size']['width'];
				$h = $img_meta['size']['height'];
			}else{
				$w = $h = 1000;
			}
			$pg_preview .= sprintf( '<img class="pg_thumb"%1$s src="%2$s" alt="%5$s" width="%3$d" height="%4$d"/>',
				(1 == $count)?' style="display:block;"':'',
				$img_meta['href'],
				$w, $h,
				esc_attr($alt)
			);
			
			// image description section
			if ( !$homepage_data['dt_static_desc'] ) {
				// title
				$pg_desc_title .= sprintf( "<div%s>\n", (1 == $count)?' style="display:block;"':'');
				if( empty( $slider_meta['hide_text'] ) && !empty($post->post_title))
					$pg_desc_title .= sprintf( "<h2>%s</h2>\n", get_the_title( $post->ID) );
				$pg_desc_title .= "</div>\n";
				
				// content
				$pg_desc .= sprintf( "<div%s>\n", (1 == $count)?' style="display:block;"':'');
				if( empty( $slider_meta['hide_text'] ) && !empty($post->post_content) ){
					$pg_desc .= "<p>\n\t". wp_kses_post( $post->post_content ). "\n";
					// detail link
					if( !empty( $slider_meta['link' ]) ){
						$pg_desc .= '<br/><a href="'. esc_url( $slider_meta['link' ] ). '" class="more">'. __('Details', LANGUAGE_ZONE). '</a>'. "\n";
					}
					$pg_desc .= "</p>";
				}
				$pg_desc .= "</div>\n";
			}elseif ( empty($pg_desc_title) && empty($pg_desc) ) {
				// homepage title
				$pg_desc_title .= sprintf( "<div style=\"display:block;\">\n\t<h2>%s</h2>\n</div>\n",
					get_the_title( $homepage->ID )
				);
				// homepage content
				$pg_desc .= sprintf( "<div style=\"display:block;\">\n\t<p>%s</p>\n</div>\n",
					wp_kses_post($homepage->post_content)
				);
			}

			// image thumbnail section
			if(1 == $count){
				$t_class = ' first';
			}elseif($pg_last == $count){
				$t_class = ' last';
			}else
				$t_class = '';

			$args['width'] = 120;
			$args['height'] = 90;
			unset( $args['full_list'] );

			$thumb .= sprintf( "<div class=\"content%s\">\n\t<div>\n\t\t", $t_class );
			$thumb .= sprintf( "<a class=\"slide-h%s\" href=\"#\">\n\t\t\t", (1 == $count)?' act':'' );
			$thumb .= sprintf( "<img src=\"%s\" width=\"120\" height=\"90\" alt=\"%s\" class=\"thumb\" /><i></i>\n\t\t",
				dt_image_href( $args ),
				$alt
			);
			$thumb .= "</a>\n\t\t";
			$thumb .= "</div>\n</div>";
				
			$count++;		
		}
	}
?>
<div id="holder" class="h<?php echo $homepage_data['dt_static_desc']?' static-description':''; ?>">

	<div class="pg_content">
	<?php get_template_part('mobile-menu') ?> 
		<div id="pg_preview">
			<?php echo $pg_preview ?>
		</div>

		<div id="pg_desc1" class="pg_description">
			<?php echo $pg_desc_title ?>
		</div>
		
		<div id="pg_desc2" class="pg_description">
			<?php echo $pg_desc ?>
		</div>
	 <?php if ( !$homepage_data['dt_hide_over_mask'] ):?>
		<div id="big-mask"></div>
	  <?php endif ?>
	</div><!-- .pg_content end-->

 
  
  <div id="thumbContainter">
    <div id="thumbScroller">
      <div class="container"> 
        <?php echo $thumb ?>
        <div class="marker"></div>
      </div>
    </div>
  </div>

</div><!-- #hilder .h end-->
<?php $wp_query = $temp;?>
<div class="bot-home"> 
	<div class="bottom-cont">
		<?php
		$links = dt_get_soc_links( 'frontend' );
		if( $links ): ?>
		<div class="foll">
			<div class="head"><?php _e( 'Social Links:', LANGUAGE_ZONE ); ?></div>
			<?php echo $links; ?>
		</div><!-- #foll -->
		<?php endif; ?>
		<span>
			<?php echo of_get_option( 'cc_copy_info_textarea' ); ?>
			<?php if( of_get_option( 'cc_show_credits_checkbox' ) ): ?>
			Created by Dream-Theme &mdash; <a target="_blank" href="http://dream-theme.com/">premium wordpress themes</a>.
			<?php endif; ?>
			<!--Proudly powered by <a href="http://wordpress.org/">WordPress</a>.-->
		</span>
		<div class="navig">
			<a href="#" class="go prev"></a>
			<a href="#" class="go play"></a>
			<a href="#" class="go next"></a>
		</div>
	</div> 
</div>     
<?php wp_footer() ?>
</body>
</html>
