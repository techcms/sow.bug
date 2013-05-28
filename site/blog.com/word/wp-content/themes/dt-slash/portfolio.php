<?php 
/* Template Name: Portfolio - standard */
?>
<?php get_header() ?>
<div id="bg">
	<?php get_template_part('mobile-menu') ?>
	<div id="top_bg"></div>
	<div id="holder">
		<?php get_template_part('aside') ?>
		<div id="content">
			<div class="article_box p">
				<div class="article_t"></div>
				<div class="article">
					<?php
					if( post_password_required() ):
						echo get_the_password_form();
					else:
						if( have_posts() ) while( have_posts() ){ the_post(); } ?>
						<h1 class="entry-title _tf">
							<?php the_title() ?>
						</h1>
						<?php
						$port_terms = get_post_meta( $post->ID, 'show_portf', true );
						global $wp_query;
						$temp = $wp_query;
						
						if( !$paged = get_query_var('paged') )
							$paged = get_query_var('page');
							
						$args = array( 	'post_type'	=>'portfolio',
										'paged'		=>$paged
									);
									
						if( isset($port_terms['number_portf']) && $port_terms['number_portf'] ){
							$args['posts_per_page'] = $port_terms['number_portf'];
							unset($port_terms['number_portf']);
						}
						
						if( isset( $_GET['portfolio_category'] ) && $_GET['portfolio_category'] ) {
							if ( 'none' != $_GET['portfolio_category'] ) {
								$args['tax_query'] = array(	
														array(
															'taxonomy'	=>'portfolio-category',
															'field'		=>'slug',
															'terms'		=>strip_tags( (string) $_GET['portfolio_category'] )
														)
													);
							} else {
								$tax = get_terms( 'portfolio-category', array('fields' =>'ids') );
								$args['tax_query'] = array(	
														array(
															'taxonomy'	=>'portfolio-category',
															'field'		=>'id',
															'terms'		=>$tax,
															'operator'	=>'NOT IN'
														)
													);
							}
						}elseif( $port_terms ) {
							$args['tax_query'] = array(	
													array(
														'taxonomy'	=>'portfolio-category',
														'field'		=>'id',
														'terms'		=>current( $port_terms ),
														'operator' 	=> ( 'only' == key($port_terms) )?'IN':'NOT IN',
													)
												);
						}
						$wp_query = new Wp_Query( $args );
						?>
						<?php if( $wp_query->have_posts() ):
							echo dt_portf_tax_list(
								array(
									'a_class'	=>'button filter',
									'c_class'	=>'portf-f',
									'tax'		=>$port_terms
								)
							);
							global $dt_post_first; $dt_post_first = true;
							while( $wp_query->have_posts() ) {
								$wp_query->the_post();
								get_template_part('content-portfolio');
							}
						?>
							<div id="nav-above" class="navigation gal">
								<?php 
								 if( function_exists('wp_pagenavi') ) wp_pagenavi( '', '', 'paginator-small');
								 else wp_link_pages();
								?>
							</div>
						<?php else: ?>
							<p><?php _e( 'Sorry, but there are no portfolio yet', 'dt' ); ?></p>
						<?php endif ?>
						<?php $wp_query = $temp; $temp = null;?>
					<?php endif;// password protectection ?>
				</div><!-- .article b end -->
				<div class="article_b"></div>
			</div>
		</div><!-- content end -->
		
	</div>
</div>
<?php get_footer() ?>
