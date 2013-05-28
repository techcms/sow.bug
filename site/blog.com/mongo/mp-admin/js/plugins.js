jQuery('form.mu-plugins').live('submit',function(e){
    e.preventDefault();
    var nonce = jQuery(this).find('input[name="mp_nonce"]').val();
    var this_input = jQuery(this).find('input[type="submit"]');
    var this_plugin = jQuery(this).attr('data-plugin');
    var this_form = jQuery(this);
    jQuery(this_input).addClass('loading_dark');
    jQuery(this).find('.these_values').each(function(index){
        var this_value = jQuery(this).val();
        var this_key = jQuery(this).attr('data-key');
        jQuery.ajax({
            url:mp_root_url+'mp-includes/pjax/add-plugin-option.php',
            data:({ plugin: this_plugin, key: this_key, value: this_value, nonce: nonce }),
            type: "POST",
            dataType: 'json',
            success: function(result){
                jQuery(this_input).removeClass('loading_dark');
                jQuery(this_form).find('span.notification').remove();
                if(result.success){
                    jQuery(this_form).prepend('<span class="notification success">'+result.message+'</span>');
                }else{
                    jQuery(this_form).prepend('<span class="notification error">'+result.message+'</span>');
                    return false;
                }
            }
        });
    });
});