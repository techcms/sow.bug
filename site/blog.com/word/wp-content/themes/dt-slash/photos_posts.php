<?php
$options = array( 	'image_obj'	=>$post,
					'image_id'	=>false,
					'width'		=>240,
					'upscale'	=>true
				);
// get photo thumbnail
$thumbnail = dt_get_thumbnail( $options );
?>
<div class="gallery-box hs-attached">
	<div class="article">
		<div class="img-holder ro">
			<a href="<?php echo $thumbnail['b_href'] ?>">
				<img <?php echo $thumbnail['size'][3] ?> src="<?php echo $thumbnail['t_href'] ?>" alt="<?php echo $thumbnail['alt'] ?>"/>
			</a>
			<div class="highslide-caption">
				<?php dt_the_attachment_links(); ?>
				<?php echo $post->post_excerpt; ?>
			</div>
		</div>
	</div>
	<div class="article_footer_b"></div>
</div>