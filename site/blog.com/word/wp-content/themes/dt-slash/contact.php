<?php
/* Template Name: Contact */
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
					<?php if( have_posts() ): while(have_posts()): the_post(); ?>
					<?php
					$data = get_post_meta( $post->ID, 'contact_options', true );
					$defaults = array( 'html_map' =>'' );
					$data = wp_parse_args( $data, $defaults );
					?>
					<h1 class="entry-title _cf">
						<?php the_title() ?>
					</h1>					
					<div class="c-t"><?php the_content(); ?></div>
					<?php if ( $data['html_map'] ):?>
					<div class="map">
						<?php echo $data['html_map'] ?>
					</div>
					<?php endif ?>
					<div class="share_cont">
						<div id="form_prev_holder">
                            <!--div id="dt_form_responce"-->
                            <?php
                            global $dt_errors;
                            if( isset($dt_errors['contact_form']) ) {
                                echo $dt_errors['contact_form'];
                            }
                            ?>
                            <!--/div-->
							<div id="form_holder">
								<div class="header"><?php _e( 'Have something to say?', LANGUAGE_ZONE ) ?></div>
                                <form id="order_form" class="uniform ajaxing" method="post" name="order_form" >
                                <?php //action="<?php echo $_SERVER['PHP_SELF']; ?" ?>
									<?php wp_nonce_field('dt_contact_form','dt_contact_form_nonce'); ?>
                                    <input type="hidden" name="p_id" value="<?php echo $post->ID ?>" />
									<input type="hidden" name="send_message" value="" />
									<input type="hidden" name="send_contacts" value="form" />
									<div class="l">
										<p><?php _e( 'Your name*', LANGUAGE_ZONE ) ?></p>
										<div class="i_h"><input id="form_name" name="f_name" type="text" placeholder="" class="validate[required]" value="" /></div>
										<p><?php _e ( 'E-mail*', LANGUAGE_ZONE ) ?></p>
										<div class="i_h"><input id="form_email" name="f_email" type="text" placeholder="" class="validate[required, custom[email]]" value="" /></div>
										<p><?php _e( 'Telephone', LANGUAGE_ZONE ) ?></p>
										<div class="i_h"><input id="form_phone" name="f_phone" type="text" placeholder="" class="validate[custom[telephone]]" value="" /></div>
									</div>
									<p><?php _e( 'Your message:*', LANGUAGE_ZONE ); ?></p>
									<div class="t_h"><textarea id="form_message" name="f_comment" placeholder="" class="validate[required]"></textarea></div>
									<?php do_action('dt_contact_form_captcha_place', 'form'); ?>
                                    <div class="buttons">
										<a href="#" class="button big go_button"><span class="but-r"><span><i class="submit"></i><?php _e( 'Submit', LANGUAGE_ZONE ); ?></span></span></a>
										<a href="#" class="do_clear"><?php _e( 'Clear form', LANGUAGE_ZONE ); ?></a>
									</div>
								</form>
							</div>
						</div>
					</div>
				<?php endwhile; endif; ?>
				</div><!-- .article b end -->
				<div class="article_b"></div>
			</div>
		</div>
	</div>
</div>
<?php get_footer() ?>
