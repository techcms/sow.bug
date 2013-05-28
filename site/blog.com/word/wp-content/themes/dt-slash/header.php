<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<title>
	<?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'dt' ), max( $paged, $page ) );

	?>
</title>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

<?php if ( ! of_get_option( 'turn_off_responsivness', false ) ): ?>

<meta name="viewport" content="width=device-width, initial-scale=0.85, maximum-scale=0.85, user-scalable=no"/>

<?php endif; ?>

<!-- FAVICON -->
<?php
$icon = dt_unify_url(of_get_option( 'appearance_favicon_uploader' ));
echo empty($icon)?'':'<link rel="icon" type="image/png" href="' .$icon. '" />'; 
?>

<?php wp_head() ?>

<?php
global $post;
if( $post ) {
	$template_file = get_post_meta( $post->ID, '_wp_page_template', TRUE );
}else{
	$template_file = '';
}

if( 'home-light.php' == $template_file ):
	$data = get_post_meta( $post->ID, 'dt_homepage_options', true );
?>
<script type="text/javascript">
/* <![CDATA[ */
	homeSlider = {
		animSpeed: 600,
		interval: <?php echo $data['dt_timing']*1000; ?>,
		autostart: <?php echo intval($data['dt_autoplay']); ?>
	};
/* ]]> */
</script>
<?php endif ?>

<?php dt_options_css() ?>
<script type="text/javascript">
/* <![CDATA[ */
	// DO NOT REMOVE!
	// b21add52a799de0d40073fd36f7d1f89
	hs.graphicsDir = '<?php echo get_template_directory_uri() ?>/js/plugins/highslide/graphics/';
	gal_h = '<?php echo get_template_directory_uri() ?>/images/gal-h.png';
	window.moveWidgets = <?php echo of_get_option('cc_hide_mobile_checkbox', true)?'false':'true'; ?>;
	window.ResizeTurnOff = <?php echo of_get_option( 'turn_off_responsivness', false ) ? 'true' : 'false'; ?>;
/* ]]> */
</script>
<?php
// google a code
echo of_get_option('misc_a_code_textarea', '');
?>
</head>

<body <?php body_class() ?>>
	