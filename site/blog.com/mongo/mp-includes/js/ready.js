function mongopress_init(){
    jQuery('form#mp-login-form').live('submit',function(e){
        e.preventDefault();
        var un = jQuery('form#mp-login-form input#mp-login-username').val();
        var pw = jQuery('form#mp-login-form input#mp-login-password').val();
        var nonce = jQuery('form#mp-login-form input[name="mp_nonce"]').val();
        var this_input = jQuery(this).find('input[type="submit"]');
        jQuery(this_input).addClass('loading_dark');
        jQuery.ajax({
            url:mp_root_url+'mp-includes/ajax/login.php',
            data:({ user_name : un, password : pw, nonce: nonce }),
            type: "POST",
            dataType: 'json',
            success: function(result){
                jQuery(this_input).removeClass('loading_dark');
                if(result.success==true){
                    //console.log('here', result);
                    window.location = result.referrer;
                    return false;
                }else{
                    alert(mp_languages.unidentified_object+'\n'+mp_languages.invalid_username_password);
                }
            }
        });
    });
	/* SEEMS THIS GOT FIXED ELSEWHERE SO IS NO LONGER NEEDED ...?
	jQuery('nav#admin-navigation a#mp-login').live('click',function(e){
		e.preventDefault();
		jQuery('form#mp-login-form').toggle('slow');
	}); */
}
jQuery(document).ready(function(){mongopress_init();})

/* DIRTY HACKS FOR REMOVING UGLY YELLOW BOXES IN CHROME */
if ($.browser.webkit) {
	$('input[name="mp[password]"]').attr('autocomplete', 'off');
}
if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
	$(window).load(function(){
		$('input:-webkit-autofill').each(function(){
			var text = $(this).val();
			var name = $(this).attr('name');
			$(this).after(this.outerHTML).remove();
			$('input[name=' + name + ']').val(text);
		});
	});
}
