jQuery('a#mp-import-html, a#import-objects').live('click',function(e){
	e.preventDefault();
	var nonce = jQuery(this).attr('data-nonce');
	var this_input = jQuery(this);
	jQuery(this_input).addClass('loading_dark');
	jQuery.ajax({
		url:mp_root_url+'mp-includes/pjax/import-html.php',
		data:({ nonce: nonce }),
		type: "POST",
		dataType: 'json',
		success: function(result){
			jQuery(this_input).removeClass('loading_dark');
			if (result.success) {
				alert(result.message);
				$(this_input).remove();
				window.location = mp_root_url;
			} else {
				alert(result.message);
			}
		},
		failure: function(){
			alert(mp_languages.error_importing);
		}
	})
});