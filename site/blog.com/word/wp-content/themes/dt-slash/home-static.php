<?php 
/* Template Name: Homepage - static */
?>
<?php get_header() ?>
	<?php get_template_part('aside') ?>
	<div id="top_bg"></div>
<?php
	global $post;

	// get homepage data before changing query
	$homepage_data = get_post_meta( $post->ID, 'dt_homepage_options', true );
	if ( $homepage_data ) {
		$prop_res = $homepage_data['dt_pres_def_s_prop'];
		$hide_desc = $homepage_data['dt_hide_desc'];
		$hide_masc = $homepage_data['dt_hide_over_mask'];
		$link = $homepage_data['dt_link'];
	} else {
		$prop_res = $hide_desc = $hide_masc = $link = false;
	}
	
	// preserve default slide proportions
	if ( $prop_res ) {
		echo '<script type="text/javascript">resize_me = 0;</script>';
	}
	
	// begin manipulations
	$args = array( 'post_id'=>$post->ID, 'no_limits'=>true );
			
	// big image section
	$thumb_id = get_post_thumbnail_id( $post->ID );
	$img_meta = wp_get_attachment_metadata( $thumb_id );
	$alt = get_post_meta($thumb_id,'_wp_attachment_image_alt', true );
	
	if ( $img_meta ) {
		$w = $img_meta['width'];
		$h = $img_meta['height'];
	} else {
		$w = $h = 1000;
	}
	$pg_preview = sprintf( '<img class="pg_thumb"%1$s src="%2$s" alt="%5$s" width="%3$d" height="%4$d"/>',
		' style="display:block;"',
		dt_image_href( $args ),
		$w, $h,
		esc_attr($alt)
	);
?>
<div id="holder" class="h static">
	<div class="pg_content">
	
	<?php get_template_part('mobile-menu') ?>  
		<div id="pg_preview">
			<?php echo $pg_preview ?>
		</div>

		<?php if ( !$hide_desc ): ?>
			<div id="pg_desc1" class="pg_description">
				<div style="display:block;">
					<h2>
						<?php the_title() ?>
					</h2>
				</div>
			</div>
			
			<div id="pg_desc2" class="pg_description">
				<div style="display:block;">
					<p>
						<?php
						echo wp_kses_post( $post->post_content );
						// detail link
						if( !empty($link) ):
						?>
							<br/><a href="<?php echo esc_url($link); ?>" class="more"><?php _e('Details', LANGUAGE_ZONE); ?></a>
						<?php endif ?>
					</p>
				</div>
			</div>
		<?php endif ?> <?php if ( !$hide_masc ):?>
			<div id="big-mask"></div>
		  <?php endif ?>
	</div><!-- .pg_content end-->

 
</div><!-- #hilder .h end-->

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
	</div> 
</div>     
<?php wp_footer() ?>
</body>
</html>
