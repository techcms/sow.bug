<div id="bottom">
  <div class="bottom-cont">
    <a href="#" class="go_up"><?php _e( 'Top!', 'dt' ); ?></a>
	<span>
		<?php echo of_get_option( 'cc_copy_info_textarea' ); ?>
		<?php if( of_get_option( 'cc_show_credits_checkbox' ) ): ?>
		Created by Dream-Theme &mdash; <a target="_blank" href="http://gamehour.org" style="color:#222;">free games</a>.
		<?php endif; ?>
		<!--Proudly powered by <a href="http://wordpress.org/">WordPress</a>.-->
	</span>
	<div class="search-f">
		<?php get_search_form(); ?>
	</div>
	<?php
	$links = dt_get_soc_links( 'frontend' );
		if( $links ):
		?>
			<div class="foll">
			  <div class="head"><?php _e( 'Social Links:', LANGUAGE_ZONE ) ?></div>
			  <?php echo $links ?>
			</div><!-- #foll -->
		<?php
		endif;
	?>
  </div><!-- .bottom-cont -->
</div><!-- #bottom --> 

<div class="image-preloader">
	<img src="<?php echo get_template_directory_uri() ?>/images/ddmenu_bg.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/gal-h.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/bg-alt.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/lupa.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/zoom-gal.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/zoom-video.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/zoom-detal.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/gal-h-b.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/fade.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/zoom.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/go-details.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/gal-detal-bg.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/gal-detal-bot.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/block_info_l_bot.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/block_info_bg_bot.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/block_info_l.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/block_info_bg.png" alt="" />
	
	<img src="<?php echo get_template_directory_uri() ?>/images/article_t_p.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_bg_p.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_b_p.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_t_p-v-i.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_bg_p-v-i.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_b_p-v-i.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_t_p-l-p.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_bg_p-l-p.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_b_p-l-p.png" alt="" />
	
	<img src="<?php echo get_template_directory_uri() ?>/images/article_t.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_bg.png" alt="" />
	<img src="<?php echo get_template_directory_uri() ?>/images/article_b.png" alt="" />
</div>

<?php dt_like_panel(); ?>
<?php wp_footer(); ?>
</body>
</html>