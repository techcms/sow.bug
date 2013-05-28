<?php
			// get post options data
			$data = get_post_meta( $post->ID, 'portfolio_options', true );
			$video_html = isset( $data['video_html'] )?trim( $data['video_html'] ):'';
			// post content type
			$p_type = 'photo dt_highslide_image';
			$vid_container = '';

			$thumb = dt_get_thumbnail( array(
				'post_id'	=>$post->ID,
				'width'		=>630,
				'height'	=>0,
				'upscale'	=>false
			) );
			
			if ( $video_html && !post_password_required() ) {
				$p_type = 'video dt_highslide_video';
				$thumb['b_href'] = '#';
				$vid_container = <<<HEREDOC
				<div class="highslide-maincontent" data-width="{$data['video_width']}">{$video_html}</div>
HEREDOC;
			}
			
			$pass_class = post_password_required()?' dt-pass-protected':'';
			if( post_password_required() ) {
				$p_type = $pass_class;
				$thumb['b_href'] = 'javascript: void(0);';
			}
?>
<div class="item-gal<?php echo $pass_class; ?>">
	<div class="item-p">
		<a class="alignleft <?php echo $p_type ?>" href="<?php echo $thumb['b_href'] ?>">
			<img <?php echo $thumb['size'][3] ?> src="<?php echo $thumb['t_href'] ?>" alt="<?php echo $thumb['alt'] ?>"/>
		</a>
		<?php echo $vid_container ?>
	</div>
	<h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
    <?php the_excerpt() ?>
	<?php if( current_user_can('edit_posts')): // edit link?>
			<a href="<?php echo get_edit_post_link($post->ID) ?>" class="button">
				<span class="but-r"><span><i class="detail"></i><?php echo __( 'Edit', 'dt' ) ?></span></span>
			</a>
	<?php endif ?>
    <a href="<?php the_permalink() ?>" class="button"><span class="but-r"><span><i class="detail"></i><?php _e( 'Details', LANGUAGE_ZONE ) ?></span></span></a>
</div>