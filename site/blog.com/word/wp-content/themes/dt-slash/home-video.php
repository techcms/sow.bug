<?php 
/* Template Name: Homepage - video */
?>
<?php get_header() ?>
	<?php $jwplayer_flag = file_exists( get_template_directory().'/js/jwplayer/jwplayer.js' ) ?>
	<?php get_template_part('aside') ?>
	<div id="top_bg"></div>
<?php
	global $post;
	$homepage_data = get_post_meta( $post->ID, 'dt_homepage_options', true );
	if ( $homepage_data ) {
		$hide_desc = $homepage_data['dt_hide_desc'];
		$hide_masc = $homepage_data['dt_hide_over_mask'];
		$vid_repeat = $homepage_data['dt_vid_loop'];
		$vid_auto = $homepage_data['dt_vid_autoplay'];
//		$vid_control = $homepage_data['dt_vid_controls'];
		$video = $homepage_data['dt_video'];
		$link = $homepage_data['dt_link'];
	} else {
		$video = $hide_desc = $hide_masc = $vid_repeat = $vid_auto = $vid_control = $link = false;
	}
	
	if ( has_post_thumbnail() ) {
		$poster = current(wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full'));
	} else
		$poster = '';
?>
<script type="text/javascript">
		$(document).ready( function(){
			$(window).resize(function(){
					window.w_height = $(window).height();
					window.w_width = $(window).width();
					window.deviceAgent = navigator.userAgent.toLowerCase();
					window.agentID = deviceAgent.match(/(iphone|ipod|ipad)/);
					
					if(agentID){
						$('.jp-controls').css({
							width:w_width -80
						});
						$('.jp-progress').css({
							width: w_width - $('.jp-play').width() - $('.jp-pause').width() - $('.jp-stop').width() - 160
						})
					}
					else{
						$('.jp-controls').css({
							width:w_width -80
						});
						$('.jp-progress').css({
							width: w_width - $('.jp-play').width() - $('.jp-pause').width() - $('.jp-stop').width() - $('.jp-mute').width() - $('.jp-unmute').width() - $('.jp-volume-bar').width() - $('.jp-volume-max').width() - 180
						})
					}
				
			}).trigger('resize');
		<?php if ( $jwplayer_flag ): ?>
			window.ua = navigator.userAgent.toLowerCase();
			window.isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
			window.deviceAgent = navigator.userAgent.toLowerCase();
			window.agentID = deviceAgent.match(/(ipad)/);
			function isiPhone(){
					return (
						(navigator.platform.indexOf("iPhone") != -1) ||
						(navigator.platform.indexOf("iPod") != -1)
					);
				}
			if(agentID || isAndroid){
				
				$('.video').removeClass('jw-video');
				$('#big-mask').css('display', 'none');
				jwplayer("JPlayer").setup({
					flashplayer: "<?php echo get_template_directory_uri(); ?>/js/jwplayer/jwplayer.flash.swf",
					file: "<?php echo $video; ?>",
					'image': "<?php echo $poster ?>",
					autostart: <?php echo $vid_auto?'true':'false' ?>,
					bufferlength: 5,
					repeat: "<?php echo $vid_repeat?'always':'none'; ?>",
					height: w_height,
					width: w_width
				});
			}
			else if(isiPhone()){
				$('.video').addClass('jw-video');
				$('#big-mask').css('display', 'none');
				jwplayer("JPlayer").setup({
					flashplayer: "<?php echo get_template_directory_uri(); ?>/js/jwplayer/jwplayer.flash.swf",
					file: "<?php echo $video; ?>",
					'image': "<?php echo $poster ?>",
					autostart: <?php echo $vid_auto?'true':'false' ?>,
					bufferlength: 5,
					repeat: "<?php echo $vid_repeat?'always':'none'; ?>",
					height: w_height + 130,
					width: w_width,
					stretching: 'fill'
				});
				
				if(ResizeTurnOff){}
				else{	
					$(".video").on("click", function(e) {
						$("video").trigger("play");
					});
					 
					
					$(document).on('touchmove', function(e) { if ($(window).width() < 1000) e.preventDefault(); });
					if ($.browser.SafariMobile){
				
						$(window).on("orientationchange",  function() {
							
							if(window.orientation == 90 || window.orientation == -90) {
							  jwplayer("JPlayer").resize(w_width, w_height+130);
								
							} else {
							  jwplayer("JPlayer").resize(w_width, w_height+130);
											
							}
						
							setTimeout(scrollTo, 0, 0, 1);
							$(window).trigger("resize");
						
						}).trigger("orientationchange");
						
						setInterval( function() {
							$(window).trigger("orientationchange");						
							window.onresize = function(){
							  jwplayer("JPlayer").resize(w_width, w_height+130);
							};
						}, 1000);
						
					}			
				}
			}
			else{				
				$('.video').removeClass('jw-video');
				if(isiPhone()){
					$('.video').addClass('jp-video-iphone');
				}else{
					$('.video').removeClass('jp-video-iphone');
				}
				jwplayer("JPlayer").setup({
					flashplayer: "<?php echo get_template_directory_uri(); ?>/js/jwplayer/jwplayer.flash.swf",
					file: "<?php echo $video; ?>",
					'image': "<?php echo $poster ?>",
					autostart: <?php echo $vid_auto?'true':'false' ?>,
					bufferlength: 5,
					repeat: "<?php echo $vid_repeat?'always':'none'; ?>",
					controlbar: {position: 'bottom'},
					height: w_height,
					width: w_width,
					stretching: 'fill',
					'controlbar':'bottom',					
					//controls:false,
					primary: "flash",	
					'skin': "<?php echo get_template_directory_uri(); ?>/js/jwplayer-skin/six/six.xml",
					players: [
					   {type: "flash", src: "<?php echo get_template_directory_uri(); ?>/js/jwplayer/jwplayer.flash.swf"}
					]
				});
			}
			window.onresize = function(){
			  jwplayer("JPlayer").resize(w_width, w_height);
			};
			
		<?php else:
//			$video = str_replace( get_site_url(), '', $video );
			preg_match_all( '/.*\.(.*)$/', $video, $mathes );
			switch ( current($mathes[1]) ) {
				case 'flv':
					$video = 'flv: "'. $video. "\",\n";
					break;
				case ('mp4' || 'mpg4'):
					$video = 'm4v: "'. $video. "\",\n";
					break;
				default: $video = '';
			}
			
			$poster = $poster?"poster: \"". $poster. "\",\n":$poster;
			//$poster = str_replace( get_site_url(), '', $poster );
			
			$vid_repeat = $vid_repeat?'ended: function() {$(this).jPlayer("play");},'."\n":'';
			$vid_auto = $vid_auto?'$(this).jPlayer("play");'."\n":'';
		?>

				
				$(window).resize(function(){
						$("#JPlayer").jPlayer( {
							swfPath: "<?php echo get_template_directory_uri() ?>/js/jplayer/",
							cssSelectorAncestor: "#jplayer_controlls",
							size: {
								width: w_width,
								height: w_height
							},
							ready: function() { // The $.jPlayer.event.ready event
								$(this).jPlayer("setMedia", { // Set the media
									<?php echo $video ?>
									<?php echo $poster ?>
									preload: "auto"
								});
								<?php echo $vid_auto ?>// Attempt to auto play the media
							},
							click: function( event ) {
								if( event.jPlayer.status.paused || event.jPlayer.status.waitForPlay ) {
									$(this).jPlayer("play");
								} else {
									$(this).jPlayer("pause");
								}
							},
							<?php echo $vid_repeat ?>
							solution: 'flash, html',
							supplied: 'flv, m4v',
							wmode: "opaque"
						});
				}).trigger('resize')
		
		<?php endif ?>
			
			if(ResizeTurnOff){}
			else{	
				$(document).on('touchmove', function(e) { if ($(window).width() < 1000) e.preventDefault(); });
				$(document).wipetouch({
					preventDefault: false,
					wipeLeft: function(result) {
						hideHeader();
					},
					wipeRight: function(result) {
						hideHeader();
					},
					wipeUp: function(result) {
						hideHeader();
					},
					wipeDown: function(result) { 
						showHeader();
						
					}
				});
			}
		
		});
</script>
<div id="holder" class="h i-m-video">
	<?php get_template_part('mobile-menu') ?> 
	<div class="pg_content video">
	
		<div id="JPlayer"></div>
		<?php if ( !$hide_desc ): ?>
			<div id="pg_desc1" class="pg_description">
				<div style="display:block;">
					<h2>
						<?php the_title() ?>
					</h2>
				</div>
			</div>
			
				<div id="pg_desc2" class="pg_description">
				<?php if( !empty($post->post_content) ): ?>
					<div style="display:block;">
						<p>
							<?php
							echo wp_kses_post( $post->post_content );
							// detail link
							if( !empty($link) ):
							?>
								<br/><a href="<?php echo esc_url($link); ?>" class="more"><?php _e('Details', LANGUAGE_ZONE); ?></a>
							<?php endif ?>
						</p>
					</div>
				<?php endif ?>
				</div>
		<?php endif ?>
		<?php if ( !$jwplayer_flag ) :?>
		<div class="bot-home"> 
			<div class="bottom-cont">
					<div id="jplayer_controlls">
						<div class="jp-gui">
							<div class="jp-video-play">
								<a href="javascript:;" class="jp-video-play-icon" tabindex="1"></a>
							</div>
							<div class="jp-interface">
								<div class="jp-controls-holder">
									<div class="jp-controls">
										<a href="javascript:;" class="jp-play" tabindex="1"></a>
                                        <a tabindex="1" class="jp-pause" href="javascript:;"></a>
										<a href="javascript:;" class="jp-stop" tabindex="1"></a>
                                        <div class="jp-progress">
											<div class="jp-seek-bar">
												<div class="jp-play-bar"></div>
											</div>
										</div>
                                        <a title="mute" tabindex="1" class="jp-mute" href="javascript:;"></a>
                                        <a title="unmute" tabindex="1" class="jp-unmute" href="javascript:;" style="display: none;"></a>
                                        <div class="jp-volume-bar">
											<div class="jp-volume-bar-value"></div>
										</div>
                                      	<a title="max volume" tabindex="1" class="jp-volume-max" href="javascript:;"> </a>
                                        
									</div>
								</div>
							</div>
					</div>
				</div>
			</div> 
		</div>
		<?php endif ?>
<?php wp_footer() ?>
	</div><!-- .pg_content end-->


  
  
</div><!-- #hilder .h end-->	
		
</body>
</html>
