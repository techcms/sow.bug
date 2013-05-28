jQuery('form#misc-options').live('submit',function(e){
    e.preventDefault();
    var this_key = 'site_options';
    var site_name = jQuery(this).find('input#mp_site_name').val();
    var site_description = jQuery(this).find('input#mp_site_description').val();
    var cookie_ttl = jQuery(this).find('input#mp_cookie_ttl').val();
    var nonce = jQuery(this).find('input[name="mp_nonce"]').val();
    var this_input = jQuery(this).find('input[type="submit"]');
    jQuery(this_input).addClass('loading_dark');
    jQuery.ajax({
        url:mp_root_url+'mp-includes/pjax/add-option.php',
        data:({ key: this_key, value: 'site_name__'+site_name+'-_-'+'site_description__'+site_description+'-_-cookie_ttl__'+cookie_ttl, nonce: nonce }),
        type: "POST",
        dataType: 'json',
        success: function(result){
            jQuery(this_input).removeClass('loading_dark');
            jQuery('form#misc-options span.notification').remove();
            if(result.success){
                jQuery('form#misc-options').prepend('<span class="notification success">'+result.message+'</span>');
            }else{
                jQuery('form#misc-options').prepend('<span class="notification error">'+result.message+'</span>');
            }
        }
    });
});

jQuery('form#user-options').live('submit',function(e){
    e.preventDefault();
    var this_key = 'user_options';
    var email = jQuery(this).find('input#mp_email').val();
    var name = jQuery(this).find('input#mp_name').val();
    var user_id = jQuery(this).find('input#mp_user_id').val();
    var nonce = jQuery(this).find('input[name="mp_nonce"]').val();
    var this_input = jQuery(this).find('input[type="submit"]');
    jQuery(this_input).addClass('loading_dark');
    jQuery.ajax({
        url:mp_root_url+'mp-includes/pjax/add-user-option.php',
        data:({ email: email, name: name, user_id: user_id, nonce: nonce }),
        type: "POST",
        dataType: 'json',
        success: function(result){
            jQuery(this_input).removeClass('loading_dark');
            jQuery('form#user-options span.notification').remove();
            if(result.success){
                jQuery('form#user-options').prepend('<span class="notification success">'+result.message+'</span>');
            }else{
                jQuery('form#user-options').prepend('<span class="notification error">'+result.message+'</span>');
            }
        }
    });
});

jQuery('select#mp-mu-plugins').live('change',function(){
    var this_id = jQuery(this).val();
    jQuery(this).parent().parent().find('article').addClass('hidden');
    if(this_id!='none'){
        jQuery('article#'+this_id).removeClass('hidden');
    }
});
