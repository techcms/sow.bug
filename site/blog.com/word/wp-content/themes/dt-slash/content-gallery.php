<?php
global $dt_post_first;
$gallery = dt_gallary_pt();
?>
<div class="item-blog<?php echo $dt_post_first?' first':'' ?>">
	<?php if( !post_password_required() ): ?>
	<?php if( comments_open() ): // comments ?>
		<a href="<?php comments_link() ?>" class="ico_link comments-a grey"><?php echo get_comments_number($post->ID) ?></a>
	<?php endif ?>
	<h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
	<div class="hsgallery">
		<?php echo $gallery['main_img'] ?>
		<div style="display: none;">
			<?php echo $gallery['img_list']?>
		</div>
	</div>
	<?php the_excerpt(); ?>
	<?php if( current_user_can('edit_posts')): // edit link?>
		<a href="<?php echo get_edit_post_link($post->ID) ?>" class="button">
			<span class="but-r"><span><i class="detail"></i><?php echo __( 'Edit', 'dt' ) ?></span></span>
			</a>
	<?php endif ?>
	<a href="<?php the_permalink() ?>" class="button"><span class="but-r"><span><i class="detail"></i><?php _e( 'Details', LANGUAGE_ZONE ) ?></span></span></a>
	<div class="meta b">  
			<div class="ico-l d">   
				<span class="ico_link date"></span> 
				<div class="info-block">
					<span class="grey"><?php _e( 'Published on:', LANGUAGE_ZONE ) ?></span><br/>
					<a href="<?php the_permalink() ?>"><?php the_time(get_option('date_format'). ' '. get_option('time_format') ) ?></a>
				</div>
			</div>           
			
			<div class="ico-l">
				<span class="ico_link author"></span>
				<div class="info-block">
					<span class="grey"><?php echo __( 'Author:', 'dt' ) ?></span><br />                 
					<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ) ?>">
						<?php echo get_the_author() ?>
					</a>
				</div>
			</div>
			
			<?php // category
			 $categories = get_the_category_list( __( ', ', 'dt' ) );
			 if( $categories ):
			?>
				<div class="ico-l">
					<span class="ico_link categories"></span>
					<div class="info-block">
						<span class="grey"><?php echo __( 'Categories:', 'dt' ) ?></span><br />					
						<?php echo $categories ?>
					</div>
				</div>
			<?php endif ?>
			
			<?php //tags
			$tags = get_the_tag_list( '', __( ', ', 'dt' ) );
			if( $tags ):			
			?>
				<div class="ico-l">
					<span class="ico_link tags"></span>
					<div class="info-block">
						<span class="grey"><?php echo __( 'Tags:', 'dt' ) ?></span><br />                 
						<?php echo $tags ?>
					</div>
				</div>
			<?php endif ?>
		</div><!-- meta b end -->
		<?php else: ?>
			<h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
			<?php echo get_the_password_form(); ?>
			<?php if( current_user_can('edit_posts')): // edit link?>
				<a href="<?php echo get_edit_post_link($post->ID) ?>" class="button">
					<span class="but-r"><span><i class="detail"></i><?php echo __( 'Edit', 'dt' ) ?></span></span>
				</a>
			<?php endif ?>
		<?php endif; // password protect ?>
</div>
<?php $dt_post_first = false ?>