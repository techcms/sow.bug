<div id="dt-mobile-menu">
	<div id="dt-mobile-menu-cont">	
	
	<?php
	$logo = of_get_option('appearance_mobile_logo_uploader', '/images/logo.png');
	
	if( $logo ):
	?>
	
	<a id="logo-mob" href="<?php echo home_url() ?>">
		<img alt="<?php echo esc_attr(get_bloginfo('name')); ?>" src="<?php echo esc_attr(dt_unify_url($logo)); ?>" <?php //echo $size[3] ?>/>
	</a>
	
	<?php endif; ?>
	
	<?php
	dt_menu( array(
		'menu_wraper'   => '<select id="nav-mob">%MENU_ITEMS%</select>',
		'menu_items'  => '<option data-level="%DEPTH%" value="%ITEM_HREF%"%ACT_CLASS%>%ITEM_TITLE%</option>%SUBMENU%',
		'submenu'    => '%ITEM%',
		'location'   => 'primary-menu',
		'act_class'   => ' selected',
		'depth'    => 3
	) );
	?>
	</div>
</div>