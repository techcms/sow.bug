<?php
function dt_like_panel() {
	if( of_get_option('misc_like_panel_checkbox', false) ):
		$act = $fixed = '';
		if( !of_get_option('misc_display_like_panel_checkbox', false) ) {
			
			$act =  ' act"';
		}
?>
	<script src="<?php echo get_template_directory_uri(); ?>/js/like.js"></script>
	<div id="show-l" class="fixed"> 
  		<div class="customize-l<?php echo $act; ?>" id="new_buttons">
  			<a title="close" class="close skin_close" href="#"></a>
  			<div class="social-button">
                <?php echo of_get_option('misc_likes_code_textarea', ''); ?>   
	 		</div>
		<div class="customize-b"></div>
		</div>
	</div>
<?php
	endif;
}

function dt_the_attachment_links( $post_id = null, $comments = null ) {

	if( !of_get_option('misc_like_panel_checkbox', false) ) return false;
	
	global $post;
	if( empty($post_id) && !empty($post) ) {
		$post_id = $post->ID;
		$comments = $post->comment_count;
	}
	
	if( !$comments ){
		$p = get_post( $post_id );
		$comments = $p->comment_count;
	}
?>
<div class="dt-social-buttons dt-window-link">
	<a class="but-like" href="<?php echo get_permalink($post_id); ?>#like" target="_blank"><?php echo __('Like', LANGUAGE_ZONE); ?></a>
	<?php if( comments_open($post_id) ): ?>
	<?php $anchor = $comments?'#comments':'#reply';	?>
	<a class="but-com" href="<?php echo get_permalink($post_id).$anchor; ?>" target="_blank"><?php echo __('Comments', LANGUAGE_ZONE); ?></a>
	<?php endif; ?>
</div>
<?php
}