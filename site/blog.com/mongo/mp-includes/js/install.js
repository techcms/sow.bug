jQuery(document).ready(function(){
    jQuery('form#add-user').live('submit',function(e){
        e.preventDefault();
        var this_form = jQuery(this);
        var username = jQuery(this_form).find('input#username').val();
        var email = jQuery(this_form).find('input#email').val();
        var password = jQuery(this_form).find('input#password').val();
        var name = jQuery(this_form).find('input#name').val();
        var nonce = jQuery(this_form).find('input[name="mp_nonce"]').val();
        jQuery.ajax({
            url:mp_root_url+'mp-includes/ajax/add-user.php',
            data:({ username: username, email: email, password: password, name: name, nonce: nonce }),
            type: "POST",
            dataType: 'json',
            success: function(result){
                if(result.success!=true){
                    alert(mp_languages.user_install_error+result.message);
                }else{
                    alert(mp_languages.user_installed);
                    window.location = mp_root_url;
                }
            }
        });
    });
    jQuery('input#mp-import-html').live('click',function(e){
        e.preventDefault();
        var this_form = jQuery(this).parent().parent();
        var nonce = jQuery(this_form).find('input[name="mp_nonce"]').val();
        jQuery.ajax({
            url:mp_root_url+'mp-includes/pjax/import-html.php',
            data:({ nonce: nonce }),
            type: "POST",
            dataType: 'json',
            success: function(result){
                return;
                if(result.success!=true){
                    alert(mp_languages.object_import_error+result.message);
                }else{
                    alert(mp_languages.object_imported);
                    window.location = mp_root_url;
                }
            },
            failure: function(){
                alert(mp_languages.import_file_error);
            }
        });
    });
})
