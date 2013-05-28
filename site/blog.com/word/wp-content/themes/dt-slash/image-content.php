<?php
global $post;
$img = wp_get_attachment_image_src($post->ID, 'full');
if( $img && $img[1] <= 630 ){
	$thumb = array( 'size' => array($img[1], $img[2], image_hwstring($img[1], $img[2])), 't_href' => $img[0] );
}else {
	$thumb = dt_get_thumbnail( array(
		'image_id'	=> $post->ID,
		'width'		=> 630,
		'upascale'	=> false
	) );
}
$alt = get_post_meta($post->ID, '_wp_attachment_image_alt', true);
//<a href="<?php echo $thumb['b_href'] " class="alignleft dt_highslide_image" title="<?php echo $thumb['caption'] ">
?>
<span class="alignleft"><img <?php echo $thumb['size'][3] ?> alt="<?php echo esc_attr($alt); ?>" src="<?php echo $thumb['t_href'] ?>"/></span>  <!-- Featured image -->
<?php the_content(); ?>
<?php comments_template(); ?>