<?php get_header() ?>
<div id="bg">
	<div id="top_bg"></div>
	<?php get_template_part('mobile-menu') ?>
	<div id="holder">
		<?php get_template_part('aside') ?>
		<div id="content">    
			<div class="article_box p">
				<div class="article_t"></div>
				<div class="article b">
					<h1 class="entry-title _cf">
						<?php echo get_bloginfo('name'); ?>
					</h1>
					<?php if( have_posts() ): ?>
						<?php global $dt_post_first; $dt_post_first = true; ?>
						<?php while( have_posts() ): the_post(); ?>
							<?php get_template_part('content', get_post_format()) ?>
						<?php endwhile ?>
						 <div id="nav-above" class="navigation blog">
							<?php 
						     if( function_exists('wp_pagenavi') ) wp_pagenavi( '', '', 'paginator-small');
							 else wp_link_pages();
							?>
						</div>
					<?php else:?>
						<p><?php _e( 'Sorry, but page is empty.', 'dt' ); ?></p>
					<?php endif ?>
				</div><!-- .article b end -->
				<div class="article_b"></div>
			</div>
		</div>
	</div>
</div>
<?php get_footer() ?>