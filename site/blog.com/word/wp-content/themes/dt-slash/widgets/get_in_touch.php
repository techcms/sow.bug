<?php

	function widget_sakura_Feedback($args) {


		// "$args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys." - These are set up by the theme
		extract($args);

		// These are our own options
		$options = get_option('widget_sakura_Feedback');
		$curtag = $options['curtag'];  // Your Twitter account name
		$title = $options['title'];  // Title in sidebar for widget
		$text = $options['text'];  // # of Updates to show
		//$upd = $options['upd'];  // # of Updates to show

        // Output
		echo $before_widget ;
		
		echo ''
              .$before_title.$title.$after_title;

		// start
        
        global $dt_errors;
        if( isset($dt_errors['contact_widget']) ) {
            echo $dt_errors['contact_widget'];
        }
      ?>
      <form class="uniform get_in_touch ajaxing" method="post" <?php /*action="<?php echo $_SERVER['PHP_SELF']; ?>" */?>> 
        <?php wp_nonce_field('dt_contact_widget','dt_contact_form_nonce'); ?>
        <input name="send_message" type="hidden" value="">
        <input name="send_contacts" type="hidden" value="widget">
		<p><?php echo $text; ?></p>
        <div class="i_h"><?php _e( 'E-mail:*', LANGUAGE_ZONE ) ?><div class="r"><input id="email" name="f_email" type="text" value="" class="validate[required, custom[email]" /></div></div> 
        <div class="i_h"><?php _e( 'Name:*', LANGUAGE_ZONE ) ?><div class="l"><input id="your_name" name="f_name" type="text" value="" class="validate[required]" /></div></div>
        <div class="t_h"><textarea id="message" name="f_comment" class="validate[required]"></textarea></div>
        <?php
        // for plugins compatibility
        //echo apply_filters('dt_contact_captha', $dt_errors);
        
        // for plugins compatibility
        //echo apply_filters('dt_contact_captha', $dt_errors);
        do_action('dt_contact_form_captcha_place', 'widget');
                                   
        ?>
        <a href="#" class="go_submit go_button" title="Submit"><span><i></i><?php echo __("Send message", LANGUAGE_ZONE); ?></span></a> 
        <a href="#" class="do_clear"><?php _e( 'Clear', LANGUAGE_ZONE ) ?></a> 
      </form>   
      <?php
		// echo widget closing tag
		echo $after_widget;
	}

	// Settings form
	function widget_sakura_Feedback_control() {

		// Get options
		$options = get_option('widget_sakura_Feedback');
		// options exist? if not set defaults
		if ( !is_array($options) )
			$options = array('curtag'=>'', 'title'=>'Get in touch!', 'text' => '', 'email' => '');

        // form posted?
		if ( isset($_POST['sakura_Twitter70-submit']) && $_POST['sakura_Twitter70-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$options['title'] = strip_tags(stripslashes($_POST['sakura_Twitter40-title']));
			$options['text'] = strip_tags(stripslashes($_POST['sakura_Twitter40-show']));
			$options['email'] = strip_tags(stripslashes($_POST['sakura_Twitter40-email']));
			update_option('widget_sakura_Feedback', $options);
		}

		// Get options for form fields to show
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$text = htmlspecialchars($options['text'], ENT_QUOTES);
		$email = htmlspecialchars($options['email'], ENT_QUOTES);

		// The form fields
		echo '<p>
				<label for="Twitter-title">' . __('Title:', LANGUAGE_ZONE) . '<br />
				<input style="width: 100px;" id="Twitter-title" name="sakura_Twitter40-title" type="text" value="'.$title.'" />
				</label></p>';
		echo '<p>
				<label for="Twitter-show">' . __('Text:', LANGUAGE_ZONE) . '<br />
				<textarea style="width: 200px;" id="Twitter-show" name="sakura_Twitter40-show" rows="7">'.$text.'</textarea>
				</label></p>';
		echo '<p>
				<label for="Twitter-email">' . __('Target Email:', LANGUAGE_ZONE) . '<br />
				<input style="width: 200px;" id="Twitter-email" name="sakura_Twitter40-email" type="text" value="'.$email.'" />
				</label></p>';
		echo '<input type="hidden" id="sakura_Twitter0-submit" name="sakura_Twitter70-submit" value="1" />';
	}


   wp_register_sidebar_widget(9003, (THEME_TITLE.' Feedback'), 'widget_sakura_Feedback');
   wp_register_widget_control(9003, THEME_TITLE.' Feedback', "widget_sakura_Feedback_control");
   
?>
