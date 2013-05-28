<?php
	$taxonomy = 'portfolio-category';
	$category_class = '';
	$term_ids = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' =>'ids' ));
	foreach( $term_ids as $term_id){
		$term = get_term( $term_id, $taxonomy );
		$category_class .= ' '. $term->slug;	
	}
	// get post options data
	$data = get_post_meta( $post->ID, 'portfolio_options', true );
	$video_html = isset( $data['video_html'] )?trim( $data['video_html'] ):'';
	// post content type
	$p_type = '';
	$vid_container = '';

	$thumb = dt_get_thumbnail( array(
		'post_id'	=>$post->ID,
		'width'		=>240,
		'upscale'	=>true
	) );
			
	if ( $video_html && !post_password_required() ) {
		$p_type = 'type-video';
		$thumb['b_href'] = '#';
		$vid_container = <<<HEREDOC
		<div class="highslide-maincontent" data-width="{$data['video_width']}">{$video_html}</div>
HEREDOC;
	}
	
	$pass_class = post_password_required()?' dt-pass-protected':'';
?>
<div id="<?php echo $post->ID ?>" class="article_box<?php echo $category_class ?> isotope-item <?php echo esc_attr(get_post_time('U', true, $post->ID)); echo $pass_class; ?>">
	<div class="article_t"></div>
	<div class="article">
		<div class="img-holder n-s ro <?php echo $p_type ?>">
			<a href="<?php the_permalink() ?>" data-img="<?php echo $thumb['b_href'] ?>" title="<?php echo $thumb['caption']; ?>">
				<img <?php echo $thumb['size'][3] ?> src="<?php echo $thumb['t_href'] ?>" alt="<?php echo $thumb['alt'] ?>"/>
			</a>
			<?php echo $vid_container ?>
		</div>
		<h4 class="entry-title _cf"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
		<?php the_excerpt() ?>
		<?php if( current_user_can('edit_posts')): // edit link?>
			<a href="<?php echo get_edit_post_link($post->ID) ?>" class="button">
				<span class="but-r"><span><i class="detail"></i><?php echo __( 'Edit', 'dt' ) ?></span></span>
			</a>
		<?php endif ?>
		<a href="<?php the_permalink() ?>" class="button"><span class="but-r"><span><i class="detail"></i><?php _e( 'Details', LANGUAGE_ZONE ) ?></span></span></a>       	
	</div><!-- .article end -->
	<div class="article_footer_b"></div>
</div>