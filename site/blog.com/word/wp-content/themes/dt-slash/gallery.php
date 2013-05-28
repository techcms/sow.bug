<?php
/* Template Name: Albums */
?>
<?php get_header() ?>
<div id="bg">
	<?php get_template_part('mobile-menu') ?>
	<div id="top_bg"></div>
	<div id="holder">
		<?php get_template_part('aside'); ?>
		<div id="content">
			<?php if( post_password_required() ): ?>
			
			<div class="article_box p">
				<div class="article_t"></div>
				<div class="article b">
					<?php echo get_the_password_form(); ?>
				</div><!-- .article b end -->
				<div class="article_b"></div>
			</div>
			
			<?php else: ?>
			<?php
			global $paged;
			if( !$paged ) {
				if( !$paged = get_query_var('paged') ) {
					if( !$paged = get_query_var('page') ) {
						$paged = 1;
					}
				}
			}
			
			$filter = get_post_meta( $post->ID, 'galery_filter', true );
			$defaults = array(	'posts_per_page'	=>false	);
			$filter = wp_parse_args( $filter, $defaults );

			$args = array(
				'post_type'	=>'dt_gallery',
				'paged'		=>$paged
			);
			
			if ( $filter['posts_per_page'] ) {
				$args['posts_per_page'] = $filter['posts_per_page'];
			}
			
			unset( $filter['posts_per_page'] );
			
			if( is_array($filter) ) {
				$key = key($filter);
				$ids = current( $filter );
				if( 'only' == $key ) {
					$args['post__in'] = $ids;
				}elseif( 'except' == $key ) {
					$args['post__not_in'] = $ids;
				}
			}

			$temp = $wp_query;
			$wp_query = new Wp_Query( $args );
			?>
			<?php if( $wp_query->have_posts() ): ?>
				<div id="dt-gal-pass-form" style="display: none;"><?php echo get_the_password_form(); ?></div>
				<div id="multicol-gal" class="two_level_gal">
					<?php while( $wp_query->have_posts() ): $wp_query->the_post(); ?>
						<?php get_template_part('gallery_posts') ?>
					<?php endwhile ?>
				</div>
			<?php endif ?>
			
		</div>
		<div id="nav-above" class="navigation portfolio">
			<?php 
			if( function_exists('wp_pagenavi') ) wp_pagenavi();
			else wp_link_pages();
			?>
		</div>
		<?php $wp_query = $temp; ?>
		<?php endif;// password protectection ?>
	</div>
</div>
<?php get_footer() ?>
