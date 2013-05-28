<?php
$logo = of_get_option('appearance_logo_uploader', '/images/logo.png');
?>
<div id="aside">
  <div id="aside_c">
  	<div class="wrap-widget menu">
		<div class="widget-t"></div>
		<div class="masthead-widget">
			
			<?php if( $logo ): ?>
			
			<div id="logo">
				<a href="<?php echo home_url() ?>">
					<img alt="<?php echo esc_attr(get_bloginfo('name')); ?>" src="<?php echo esc_attr(dt_unify_url($logo)); ?>" <?php //echo $size[3] ?>/>
				</a>
			</div>
			
			<?php endif; ?>
			
			<?php
			dt_menu( array(
				'menu_wraper' 		=> '<ul id="nav">%MENU_ITEMS%</ul>',
				'menu_items'		=> '<li class="%ITEM_CLASS%"><a href="%ITEM_HREF%" %ESC_ITEM_TITLE%>%ITEM_TITLE%</a>%SUBMENU%</li>',
				'submenu' 			=> '<div style="display: none;"><ul>%ITEM%</ul><i></i></div>'
			) );
			?>
		</div>
		<div class="widget-b"></div>
	</div>
	<div id="widget-container">
	<?php
	if ( 	is_page_template('home-light.php') ||
			is_page_template('home-video.php') ||
			is_page_template('home-static.php') )
	{
		dynamic_sidebar('homepage-widget-area');
	} else {
		dynamic_sidebar('primary-widget-area');
	}
	?>
	</div>
  </div>
</div>