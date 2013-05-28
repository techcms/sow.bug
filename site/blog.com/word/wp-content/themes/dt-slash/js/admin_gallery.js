jQuery(function () {	
	jQuery(".showhide", jQuery(dt_admin.box)).each(function () {
		var ee = this;
		jQuery("input[type=radio]", ee).change(function () {
			jQuery(".list", jQuery(dt_admin.box)).hide();
			if ( jQuery(this).attr("checked") )
				jQuery(".list", ee).show();
			else
				jQuery(".list", ee).hide();
		});
		jQuery("input[type=radio]:checked", ee).change();
	});
});