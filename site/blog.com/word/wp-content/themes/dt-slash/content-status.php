<?php global $dt_post_first ?>
<div class="item-blog<?php echo $dt_post_first?' first':'' ?>">
	<?php if( !post_password_required() ): ?>
	<div class="blockquote_bg status">
        <blockquote>
			<span class="quotes-l">
				<span class="quotes-r">
					<?php the_content() ?>
				</span>
			</span>
		</blockquote>
	</div>
	
	<span class="inf">
		<a href="<?php the_permalink() ?>">
			<?php printf( __('%s at %s', 'dt'), get_the_date(), get_the_time() ) ?>
		</a>
		<?php if( comments_open() ): // comments ?>
		<a class="ico comments" href="<?php comments_link() ?>">
			<?php comments_number( __('no comments', LANGUAGE_ZONE), __('1 comment', LANGUAGE_ZONE), __('% comments', LANGUAGE_ZONE) ) ?>
		</a>
			<?php endif // end comments?>
	</span>
	<?php else: ?>
		<?php echo get_the_password_form(); ?>
	<?php endif; // password protect ?>
	<?php if( current_user_can('edit_posts')): // edit link?>
		<a href="<?php echo get_edit_post_link($post->ID) ?>" class="button">
			<span class="but-r"><span><i class="detail"></i><?php echo __( 'Edit', 'dt' ) ?></span></span>
		</a>
	<?php endif ?>
</div>
<?php $dt_post_first = false ?>