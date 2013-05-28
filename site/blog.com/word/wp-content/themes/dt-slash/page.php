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
					<?php
					if( !post_password_required() ):
						if( have_posts() ): ?>
							<?php while( have_posts() ): the_post(); ?>
								<?php get_template_part('content', 'page') ?>
							<?php endwhile ?>
						<?php
						endif;
					else:
						echo get_the_password_form();
					endif; ?>
				</div><!-- .article b end -->
				<div class="article_b"></div>
			</div>
		</div>
	</div>
</div>
<?php get_footer() ?>