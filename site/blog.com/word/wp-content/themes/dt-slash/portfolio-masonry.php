<?php 
/* Template Name: Portfolio - masonry*/
?>
<?php get_header() ?>
<div id="bg">
	
	<?php get_template_part('mobile-menu') ?>
	<div id="top_bg"></div>
	<div id="holder">
		<?php get_template_part('aside') ?>
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
			$port_terms = get_post_meta( $post->ID, 'show_portf', true );
			$args = array( 'post_type'	=>'portfolio' );
			
			if( isset($port_terms['number_portf']) && $port_terms['number_portf'] ) {
				$args['posts_per_page'] = $port_terms['number_portf'];
				unset($port_terms['number_portf']);
			}
			
			if( $port_terms ) {
				$args['tax_query'] = array(	
											array(
												'taxonomy'=>'portfolio-category',
												'field'=>'id',
												'terms'=>current( $port_terms ),
												'operator' => ( 'only' == key($port_terms) )?'IN':'NOT IN',
											)
										);
			}
			$temp = $wp_query;
			$wp_query = new Wp_Query( $args );
			if( $wp_query->have_posts() ): 
				echo dt_portf_tax_list(
					array(
						'a_class'	=>'button big filter',
						'c_class'	=>'filter-p filters',
						'tax'		=>$port_terms,
						'ajax'		=>true
					)
				);
			?>
				<div id="multicol" class="portfolio_massonry">
					<?php while( $wp_query->have_posts() ): $wp_query->the_post(); ?>
						<?php get_template_part('content-masonry', 'portfolio'); ?>
					<?php endwhile ?>
				</div>
				<div id="nav-above" class="navigation portfolio _m">
					<?php
					if( function_exists('wp_pagenavi') ) wp_pagenavi();
					else wp_link_pages();
					?>
				</div>
			<?php else:?>
			<?php endif ?>
			<?php $wp_query = $temp; ?>
			<?php endif;// password protectection ?>
		</div>
	</div>
</div>
<?php get_footer() ?>
