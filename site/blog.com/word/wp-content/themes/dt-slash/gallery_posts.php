<?php
// get main thumbnail
$options = array( 	'post_id'	=>$post->ID,
					'width'		=>240,
					'upscale'	=>true,
					'noimage'	=>false
				);
$thumbnail_album = dt_get_thumbnail( $options );

// album order options
$order_opts = get_post_meta($post->ID, '_dt_gallery_order', true);
// get album images
$query = array( 'post_type'			=>'attachment', 
				'post_mime_type'	=>'image',
				'post_status'		=>'inherit',
				'orderby'			=>isset($order_opts['orderby'])?$order_opts['orderby']:'menu_order',
				'order'				=>isset($order_opts['order'])?$order_opts['order']:'ASC',
				'posts_per_page'	=>999,
				'post_parent'		=>$post->ID
			);	
$images = new Wp_Query( $query );
$images = $images->posts;
$pass_class = post_password_required()?' dt-pass-protected':'';
?>
<div class="gallery-box link_gal<?php echo $post->ID; echo $pass_class; ?>">
	<div class="article">
		<div class="img-holder ro">
			<a href="<?php echo $thumbnail_album['b_href'] ?>" title="<?php echo $thumbnail_album['caption'] ?>">
				<img <?php echo $thumbnail_album['size'][3] ?> src="<?php echo $thumbnail_album['t_href'] ?>" alt="<?php echo $thumbnail_album['alt'] ?>"/>
			</a>
		</div>
		<div class="photo-info">
			<div class="photo-info-t">
				<p class="caption"><?php the_title() ?></p>
				<?php the_excerpt() ?>
			</div>
			<div class="photo-info-b"></div>
		</div>
	</div>
	<div class="article_footer_b"></div>
</div>
<?php if( !empty($images) && !post_password_required() ): ?>
<div class="album_holder for_gal<?php echo $post->ID ?>">
	<strong><?php the_title() ?></strong>
	<?php
	foreach( $images as $image ):
		$thumbnail = dt_get_thumbnail( array( 	
			'image_id'	=>false,
			'image_obj'	=>$image,
			'width'		=>$options['width'],
			'upscale'	=>$options['upscale']
		) );
		$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
	?>
		<a href="<?php echo $thumbnail['b_href'] ?>" data-src="<?php echo $thumbnail['t_href'] ?>" data-width="<?php echo $thumbnail['size'][0] ?>" data-height="<?php echo $thumbnail['size'][1] ?>" data-alt="<?php echo esc_attr($alt); ?>"></a>
		<div class="highslide-caption">
			<?php dt_the_attachment_links( $image->ID ); ?>
			<?php echo $image->post_excerpt; ?>
		</div>
	<?php endforeach ?>
</div>
<?php endif ?>