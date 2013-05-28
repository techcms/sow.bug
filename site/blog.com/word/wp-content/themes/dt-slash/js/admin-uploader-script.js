jQuery(document).ready(function() {

	jQuery('#upload_image_button').click(function() {
	 formfield = jQuery('#dt_video').attr('name');
	 tb_show('', jQuery(this).attr('href') );
	 return false;
	});

	window.send_to_editor = function(html) {
		var imgurl = jQuery(html.toString()).attr('href');
		jQuery('#dt_video').attr('value', imgurl);
		tb_remove();
	}
	
	jQuery('#remove_image_button').click(function(){
		jQuery(this).parent().find('#dt_video').attr('value', '');
		return false;
	});

});