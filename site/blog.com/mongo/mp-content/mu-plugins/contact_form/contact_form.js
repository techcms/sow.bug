$(document).ready(function(){
    $('form#contact-form').live('submit',function(e){
        e.preventDefault();
        var this_form = $(this);
        var name = $(this).find('input#name').val();
        var email = $(this).find('input#email').val();
        var message = $(this).find('#message').val();
        var email_to = $(this).find('#email_to').val();
        var min_length = $(this).find('#message').attr('data-min-length');
        var nonce = $(this).find('input[name="mp_nonce"]').val();
        $(this).find('#submit-button').addClass('loading_dark');
        $(this).find('span.notifications').removeClass('error').animate({'opacity':0},0).html('');
        if(!name){
            $(this).find('span.notifications').prepend(contact_form_languages.error_name);
        }if(!email){
            $(this).find('span.notifications').prepend(contact_form_languages.error_email);
        }if(!message){
            $(this).find('span.notifications').prepend(contact_form_languages.error_message);
        }
        if((!name)||(!email)||(!message)){
            $(this).find('span.notifications').addClass('error').show(0).animate({'opacity':1});
            $(this_form).find('#submit-button').removeClass('loading_dark');
        }else{
            $.ajax({
                url:mp_root_url+'mp-includes/ajax/send-email.php',
                data:({ nonce: nonce, name: name, email: email, message: message, email_to: email_to, min_length: min_length }),
                type: "POST",
                dataType: 'json',
                success: function(result){
                    if(result.success==true){
                        $(this_form).find('span.notifications').removeClass('error').animate({'opacity':0},750, function(){
                            $(this).prepend(contact_form_languages.success);
                            $(this).show(0).animate({'opacity':1});
                            $(this_form).find('.form-wrapper').hide("normal");
                            $(this_form).find('#submit-button').removeClass('loading_dark');
                        });
                    }else{
                        $(this_form).find('span.notifications').addClass('error').animate({'opacity':0},750, function(){
                            $(this).prepend(result.message);
                            $(this).show(0).animate({'opacity':1});
                            $(this_form).find('#submit-button').removeClass('loading_dark');
                        });
                    }
                }
            });
        }
    });
});