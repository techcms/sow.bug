<?php	
	global $post;
    if (!$post) $post = $wp_query->post;
	if ( comments_open() ) :
		if ( post_password_required() ) : 
?>
			<div class="comments_c">
				<p class="nopassword">
					<?php echo __( 'This post is password protected. Enter the password to view any comments.', 'dt' ); ?>
				</p>
			</div><!-- #comments_c -->
	<?php
			/* Stop the rest of comments.php from being processed,
			* but don't kill the script entirely -- we still have
			* to fully load the template.
			*/
			return;
		endif;
	?>
	<?php if ( have_comments() ): ?>
			<!-- Comments section -->
			<div class="header h_com" id="comments"><?php comments_number ( '', __('1 Comment:', 'dt'), __('% Comments:', 'dt') ); ?></div>   <!-- Number of comments -->
			<div class="comments_c">
				<?php wp_list_comments('max_depth=5&style=div&callback=single_comments&end-callback=end_callback'); ?>
			</div>
			<?php ob_start(); paginate_comments_links(); $rel = ob_get_clean(); ?>
	<?php endif; ?>
		<div class="share_com" id="reply"> 
		<?php
			ob_start();
			comment_form();
			ob_get_clean(); 
			global $current_user;
			get_currentuserinfo();
			global $post;
            
            // for plugins compatibility
            $commenter = wp_get_current_commenter();
            $user = wp_get_current_user();
	        $user_identity = ! empty( $user->ID ) ? $user->display_name : '';
            //***
			
            if (!$post) $post = $wp_query->post;
			
			$dt_login = $dt_email = $dt_phone = $dt_url = '';
		?>  
            
            <?php do_action( 'comment_form_before' ); ?>
            
			<div id="form_prev_holder">
				<div id="form_holder">
					<div class="header"><?php echo __( 'Leave a comment:', 'dt' ); ?></div>
					<?php if (is_user_logged_in()) { 
							$dt_login = $current_user->user_login;
							$dt_email = $current_user->user_email;
							//$dt_phone = $current_user->telephone;
							$dt_url = $current_user->user_url;
					}
					?>
					<form action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post" class="uniform">
                        
                        <?php do_action( 'comment_form_top' ); ?>
                        
						<?php if (!is_user_logged_in()): ?>
							<?php do_action( 'comment_form_before_fields' ); ?>
							<div class="l">
								<p><?php echo __( 'Your name', 'dt' ) ?></p>
								<div class="i_h">
									<input id="form_name" name="author" type="text" placeholder="" class="validate[required]" value="<?php echo $dt_login ?>"/>
								</div>
								<p><?php echo __( 'E-mail', 'dt' ) ?></p>
								<div class="i_h">
									<input id="form_email" name="email" type="text" placeholder="" class="validate[required, custom[email]]" value="<?php echo $dt_email ?>"/>
								</div>
								<p><?php echo __( 'Website', 'dt' ) ?></p>
								<div class="i_h">
									<input id="form_url" name="url" type="text" placeholder="" value="<?php echo $dt_url ?>"/>
								</div>
							</div>
						<?php else: ?>
							<?php do_action( 'comment_form_logged_in_after', $commenter, $user_identity ); ?>
						<?php endif ?>
						
						<p><?php echo __( 'Your message:', 'dt' ) ?></p>
						<div class="t_h"><textarea name="comment" id="message3" placeholder="" class="validate[required]"></textarea></div>
						
						<?php if( !is_user_logged_in() ): ?>
							<?php do_action( 'comment_form_after_fields' ); ?>
						<?php endif; ?>
						
						<div class="buttons">
							<a href="#" class="button big go_button" title="<?php echo __( 'Submit', 'dt' ); ?>">
								<span class="but-r">
									<span>
										<i class="submit"></i><?php echo __( 'Submit', 'dt' ); ?>
									</span>
								</span>
							</a>
							<a href="#" class="do_clear" title="<?php echo __( 'Submit', 'dt') ?>"><?php echo __( 'Clear form', 'dt' ); ?></a>
						</div>
						<?php comment_id_fields(); ?>
						<?php do_action('comment_form', $post->ID); ?>
					</form> 
				</div><!-- form_holder-->
			</div><!-- form_prev_holder -->
            
            <?php do_action( 'comment_form_after' ); ?>

		</div><!-- share_com -->
<?php else: ?>
    
	<!-- comments closed -->
    <?php do_action( 'comment_form_comments_closed' ); ?>
    
<?php endif; ?>