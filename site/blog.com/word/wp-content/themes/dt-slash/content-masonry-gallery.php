<?php
$gallery = dt_gallary_pt( 'massonry_gallery' );
?>
<div class="gallery-box">
	<div class="article">
		<?php if( comments_open() ): // comments ?>
			<a href="<?php comments_link() ?>" class="ico_link comments-a"><?php echo get_comments_number($post->ID) ?></a>
		<?php endif ?>
		
			<div class="img-holder">
				<?php echo $gallery['main_img'] ?>
			</div>
			<div class="gal_in_posts" style="display: none;">
				<?php echo $gallery['img_list'] ?>
			</div>
		<div class="photo-info">
			<div class="photo-info-t">
				<p class="caption"><?php the_title() ?></p>
				<?php the_excerpt() ?>
			</div>
			<div class="photo-info-b"></div>
		</div>
	</div><!-- .article end -->
	<div class="article_footer_b"></div>
</div>