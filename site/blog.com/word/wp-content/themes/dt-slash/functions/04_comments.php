<?php
	global $is_first_post;
	$is_first_post = 1;
	
	function single_comments($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; 
		global $is_first_post;
		$avatar_size = 50;
		?>
        <!-- Single coment -->
        <div id="comment-<?php echo $comment->comment_ID ?>" class="comment_bg level_<?php echo $depth; if ($is_first_post) echo ' first'; ?>"><!-- Level of comment -->
          <div class="comment">
			<?php
			$avatar = get_avatar( $comment, $avatar_size, get_template_directory_uri(). '/images/icon-comments-slash.jpg' );
			if( $avatar ): ?>
				<div class="shad_dark">
				<?php echo $avatar; ?>
				</div><!-- Userpic -->
			<?php  endif; ?>
            <div class="head">    
				<h5><?php echo get_comment_author() . __( ' says:', LANGUAGE_ZONE ); ?></h5>
				<div class="comment_meta">  <!-- Comment meta holder -->
					<a href="#" class="ico_link date"><?php printf( __('%s at %s', LANGUAGE_ZONE), get_comment_date(), get_comment_time()); ?></a>   <!-- Comment date -->
					<a href="#" class="ico_link comments"><?php echo __( 'Reply', LANGUAGE_ZONE) ?></a>   <!-- Reply link -->
				</div>
            </div>
            <div class="comment-t">
				<?php comment_text() ?>
				<?php edit_comment_link( __( 'Edit', LANGUAGE_ZONE ), '<span class="edit-link">', '</span>' ); ?>
            </div>           
          </div>
        </div>
		<?php
		$is_first_post = 0;
	}
	
	function end_callback(){}