<h1 class="entry-title _cf"><?php the_title() ?></h1> <!-- Post title -->
	<?php the_content(); wp_link_pages(); ?>
	<?php if( current_user_can('edit_posts')): // edit link?>
			<a href="<?php echo get_edit_post_link($post->ID) ?>" class="button">
				<span class="but-r"><span><i class="detail"></i><?php echo __( 'Edit', 'dt' ) ?></span></span>
			</a>
	<?php endif ?>
	<?php comments_template(); ?>