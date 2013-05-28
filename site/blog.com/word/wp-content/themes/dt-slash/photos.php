<?php 
/* Template Name: Photos */
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
			if( !$paged = get_query_var('paged') ){
				$paged = get_query_var('page');
			}

			global $wp_query;
			global $dt_where_filter_param;
			global $wpdb;
			
			$temp = $wp_query;
			$filter = get_post_meta( $post->ID, 'galery_filter', true );
			$query_str = sprintf( 'SELECT `ID` FROM %s WHERE `post_type`="%s"', $wpdb->posts, 'dt_gallery' );
			if ( is_array( current( $filter ) ) ) {
				$query_str .= ' AND ID';
				$key = key( $filter );
				if ( 'except' == $key ) {
					$query_str .= ' NOT';
				}
				$query_str .= sprintf( ' IN(%s)', implode( ',', current($filter) ) );
			}
			$dt_where_filter_param = $query_str;

			$args = array( 	'post_type' 		=>'attachment', 
							'post_mime_type'	=>'image',
							'post_status' 		=>'inherit',
							'orderby'			=>'menu_order',
							'paged'				=>$paged
			);
			
			if ( isset($filter['posts_per_page']) && $filter['posts_per_page'] ) {
				$args['posts_per_page'] = $filter['posts_per_page'];
			}
			
			add_filter( 'posts_where' , 'dt_posts_parents_where' );
			$wp_query = new Wp_Query( $args );
			remove_filter( 'posts_where' , 'dt_posts_parents_where' );
			?>
			<?php if( have_posts() ): ?>
				<div id="multicol-gal" class="one_level_gal">
					<?php while( have_posts() ): the_post(); ?>
						<?php get_template_part('photos_posts') ?>
					<?php endwhile ?>
				</div>
			<?php endif ?>
			<div id="nav-above" class="navigation portfolio">
				<?php 
				if( function_exists('wp_pagenavi') ) wp_pagenavi();
				else wp_link_pages();
				?>
			</div>
		</div>
		
		<?php $wp_query = $temp; ?>
		<?php endif;// password protectection ?>
	</div>
</div>
<?php get_footer() ?>
