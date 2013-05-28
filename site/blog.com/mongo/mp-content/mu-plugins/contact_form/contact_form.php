<?php
add_filter('mp_get_content','mp_plugin_contact_form',1,2);
add_action('mp_content_block_content_article_contact_form','mp_content_block_content_article_contact_form_options',1,5);
function mp_content_block_content_article_contact_form_options($content,$type,$id,$class,$href){
    $mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => $id
    );
    $plugin_options = $mp->plugin_options($these_options);
    if(isset($plugin_options['filter_by'])){ $this_filter_by = $plugin_options['filter_by']; }else{ $this_filter_by = false; }
    if(isset($plugin_options['use_ajax'])){ $this_use_ajax = $plugin_options['use_ajax']; }else{ $this_use_ajax = 'yes'; }
    if(isset($plugin_options['object_id'])){ $this_object_id = $plugin_options['object_id']; }else{ $this_object_id = false; }
    if(isset($plugin_options['email_to'])){ $this_email_to = $plugin_options['email_to']; }else{ $this_email_to = false; }
    if(isset($plugin_options['min_char'])){ $this_min_char = $plugin_options['min_char']; }else{ $this_min_char = 20; }
    ob_start();
    ?>
    <form id="plugin-<?php echo $id; ?>" class="mp-form mu-plugins" data-plugin="<?php echo $id; ?>">
        <label for="filter_by_<?php echo $id; ?>"><?php _e('Filter By'); ?></label>
        <span class="input-wrapper radius5">
            <input class="blanked these_values" id="filter_by_<?php echo $id; ?>" data-key="filter_by" name="filter_by_<?php echo $id; ?>" placeholder="<?php _e('This is the object type used to filter the drop-down below'); ?>" value="<?php echo $this_filter_by; ?>" />
        </span>
        <label for="use_ajax_<?php echo $id; ?>"><?php _e('Use AJAX'); ?></label>
        <span class="input-wrapper radius5">
            <select name="use_ajax_<?php echo $id; ?>" class="blanked these_values" data-key="use_ajax" id="use_ajax_<?php echo $id ?>" autocomplete="off">
                <option></option>
                <?php
                if(empty($this_use_ajax)){ $this_use_ajax = 'yes'; }
                if($this_use_ajax == 'no'){
                    echo '<option value="no" selected>'.__('NO').'</option>';
                    echo '<option value="yes">'.__('YES').'</option>';
                }else{
                    echo '<option value="yes" selected>'.__('YES').'</option>';
                    echo '<option value="no">'.__('NO').'</option>';
                }
                ?>
            </select>
        </span>
        <label for="email_to_<?php echo $id; ?>"><?php _e('Your Email Address'); ?></label>
        <span class="input-wrapper radius5">
            <input class="blanked these_values" id="email_to_<?php echo $id; ?>" data-key="email_to" name="email_to_<?php echo $id; ?>" placeholder="<?php _e('This is where the contact form will send the messages to'); ?>" value="<?php echo $this_email_to; ?>" />
        </span>
        <label for="min_char_<?php echo $id; ?>"><?php _e('Minimum Number of Characters for Message'); ?></label>
        <span class="input-wrapper radius5">
            <input class="blanked these_values" id="min_char_<?php echo $id; ?>" data-key="min_char" name="min_char_<?php echo $id; ?>" placeholder="<?php _e('This offers an additional level of authentication'); ?>" value="<?php echo $this_min_char; ?>" />
        </span>
        <label for="object_id_<?php echo $id; ?>"><?php _e('Where to display the contact form?'); ?></label>
        <?php mp_objects_dropdown('object_id_'.$id,false,'these_values','data-key="object_id"',$this_object_id,$this_filter_by); ?>
        <input type="submit" id="submit_<?php echo $id; ?>" class="button" value="<?php _e('Save Plugin Settings'); ?>" />
        <?php mp_nonce_field('plugin-options','mp_nonce'); ?>
    </form>
	<div style="clear:both; display: block;"></div>
    <?php
    $options = ob_get_clean();
    return $options;
}
function mp_plugin_contact_form($content,$mongo_id){
    $mp = mongopress_load_mp();
    $options = $mp->options();
    $these_options = array(
        'action'    => 'get',
        'key'       => 'contact_form'
    );
    $plugin_options = $mp->plugin_options($these_options);
    $this_object_id = $plugin_options['object_id'];
    if(($this_object_id==$mongo_id)&&(!empty($this_object_id))){
        $error = false;
        if(isset($plugin_options['email_to'])){ $this_email_to = $plugin_options['email_to']; }else{ $this_email_to = false; }
        if(isset($plugin_options['use_ajax'])){ $this_use_ajax = $plugin_options['use_ajax']; }else{ $this_use_ajax = false; }
        $nonce = mp_create_nonce('contact_form');
        if(isset($plugin_options['min_char'])){ $this_min_char = (int)$plugin_options['min_char']; if(empty($this_min_char)){ $this_min_char = 20; }}else{ $this_min_char = 20; }
        if($this_use_ajax!='no'){
            mp_enqueue_script_theme('contact_form', $options['root_url'].'mp-content/mu-plugins/contact_form/contact_form.js', array('jquery'), 1);
        }else{
            if('POST' == $_SERVER['REQUEST_METHOD']){
                $files = $_FILES;
                $name = $_POST['name'];
                $name_length = strlen($name);
                $email = $_POST['email'];
                $message = $_POST['message'];
                $message_length = strlen($message);
                $nonce = $_POST['mp_nonce'];
                $email_to = $this_email_to;
                $min_length = 20;
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                    if($name_length<2){
                        $error = __('Please enter a real name!');
                    }else{
                        if($message_length<$this_min_char){
                            $error = __('Please enter a real message!');
                        }else{
                            if(!mp_verify_nonce($nonce,'email')){
                                $error = __('Unidentified Object in The Imperial Vortex!!!');
                            }else{
                                $headers = "From: {$email}" . "\r\n";
                                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                                $emailbody = __('<p>You have received a new message from the enquiries form on your website.</p>');
                                $emailbody.= sprintf(__('<p><strong>Name: </strong> %s</p>'),$name);
                                $emailbody.= sprintf(__('<p><strong>Email Address: </strong> %s</p>'),$email);
                                $emailbody.= sprintf(__('<p><strong>Message: </strong> %s</p>'),$message);
                                if(mail($email_to,__('New Enquiry'),$emailbody,$headers)){
                                    $error = 'success';
                                }else{
                                    $error = __('PHP Mail Function not Available');
                                }
                                /* VALIDATION COMPLETE */
                                if(is_array($files)){
                                    foreach($files as $file){
                                        $error = apply_filters('mp_contact_form_file',$file);
                                    }
                                } /* NOW NEED TO SEND THE EMAIL */
                            }
                        }
                    }
                }else{
                    $error = __('Please use a valid email address!');
                }
            }
        }
        mp_enqueue_style_theme('contact_form', $options['root_url'].'mp-content/mu-plugins/contact_form/contact_form.css');
        ob_start();
        ?>
        <script>
            var contact_form_languages = [];
            contact_form_languages.error_name = '<?php _e('<p>You must enter a name in order to submit this form</p>'); ?>';
            contact_form_languages.error_email = '<?php _e('<p>You must enter an email address in order to submit this form</p>'); ?>';
            contact_form_languages.error_message = '<?php _e('<p>You must enter a message in order to submit this form</p>'); ?>';
            contact_form_languages.success = '<?php _e('<p>Thank you for your message, we will be in touch soon</p>'); ?>';
        </script>
        <form id="contact-form" class="mp-form" method="post" enctype="multipart/form-data">
            <?php if($error){ if($error!='success'){ $note_class='error'; }else{ $note_class=false; $error=__('Message Successfully Sent'); } ?>
                <span class="notifications <?php echo $note_class; ?>" style=""><?php echo $error; ?></span>
            <?php }else{ ?>
                <span class="notifications" style="display:none;"></span>
            <?php } ?>
            <div class="form-wrapper">
                <div class="half-left">
                    <label for="name"><?php _e('Name: <span class="required">*</span>'); ?></label>
                    <span class="input-wrapper radius5"><input type="text" id="name" name="name" value="" class="blanked" placeholder="<?php _e('We need to know what to call you when replying'); ?>" required="required" autofocus="autofocus" autocomplete="off" /></span>
                </div>
                <div class="half-right">
                    <label for="email"><?php _e('Email Address: <span class="required">*</span>'); ?></label>
                    <span class="input-wrapper radius5"><input type="email" id="email" name="email" value="" class="blanked" placeholder="<?php _e('Please provide your full email address'); ?>" required="required" autocomplete="off" /></span>
                </div>
                <?php do_action('mp_contact_form_before_message'); ?>
                <label for="message" class="halfed"><?php _e('Message: <span class="required">*</span>'); ?></label>
                <span class="input-wrapper area halfed radius5"><textarea id="message" name="message" class="blanked" placeholder="<?php _e('Please share what is on your mind or let us know how we can help'); ?>" required="required" data-min-length="<?php echo $this_min_char; ?>"  autocomplete="off"></textarea></span>
                <input type="submit" value="<?php _e('Submit Message'); ?>" id="submit-button" class="button right" />
                <input type="hidden" id="email_to" value="<?php echo $this_email_to; ?>" />
                <?php mp_nonce_field('email','mp_nonce'); ?>
            </div>
        </form>
        <?php
        $contact_form = ob_get_clean();
        $new_content = $content;
        $new_content.= $contact_form;
        return $new_content;
    }else{
        return $content;
    }
}