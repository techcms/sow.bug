<?php get_header() ?>
<div id="bg">
	<?php get_template_part('mobile-menu') ?>
	<div id="top_bg"></div>
	<div id="holder">
		<?php get_template_part('aside') ?>
		<div id="content">    
			<div class="article_box p">
				<div class="article_t"></div>
				<div class="article b">
					<h1 class="entry-title _cf">
						<?php 
						global $wp_query;
						echo str_replace(
							array('%1', '%2'),
							array(get_search_query(), $wp_query->found_posts),
							__('Search query ( %1 ) found %2 matches', LANGUAGE_ZONE)
						);
						?>
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
						<p><?php _e( 'Sorry, but search result is empty.', LANGUAGE_ZONE ); ?></p>
					<?php endif ?>
				</div><!-- .article b end -->
				<div class="article_b"></div>
			</div>
		</div>
	</div>
</div>
<?php get_footer() ?>