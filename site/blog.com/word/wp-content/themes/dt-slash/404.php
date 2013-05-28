<?php get_header(); ?>
<div id="bg">
	
	<?php get_template_part('mobile-menu') ?>
	<div id="top_bg"></div>
	<div id="holder">
		<?php get_template_part('aside'); ?>
		<div id="content">    
			<div class="article_box p">
				<div class="article_t"></div>
				<div class="article b">
					<h1 class="entry-title _cf">
						<?php _e('Error', LANGUAGE_ZONE); ?>
					</h1>
					<h3>
						<?php _e( '404 &ndash; File not found', LANGUAGE_ZONE); ?>
					</h3> 
				</div><!-- .article b end -->
				<div class="article_b"></div>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>