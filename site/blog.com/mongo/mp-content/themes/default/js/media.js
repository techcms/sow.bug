jQuery(document).ready(function(){
   jQuery('ul#mp-media-gallery a.delete-media').live('click',function(e){
        e.preventDefault();
        if(confirm(mp_languages.confirmation_delete)) {
            var this_gallery_item = jQuery(this).parent().parent().parent();
            var this_id = jQuery(this).attr('data-mongo-id');
            var nonce = jQuery('ul#mp-media-gallery').attr('data-nonce-action');
            jQuery.ajax({
                url:mp_root_url+'mp-includes/pjax/delete-media.php',
                data:({ id: this_id, nonce: nonce }),
                type: "POST",
                dataType: 'json',
                success: function(result){
                    if(result.success){
                        jQuery(this_gallery_item).remove();
                    }
                }
            });
        }
    });
    jQuery('ul#mp-media-gallery a.edit-media').live('click',function(e){
        e.preventDefault();
        var this_gallery_item = jQuery(this).parent().parent().parent();
        var this_id = jQuery(this).attr('data-mongo-id');
        var nonce = jQuery('ul#mp-media-gallery').attr('data-nonce-action');
        jQuery.ajax({
            url:mp_root_url+'mp-includes/pjax/edit-media.php',
            data:({ id: this_id, nonce: nonce }),
            type: "POST",
            dataType: 'json',
            success: function(result){
                var this_filename = result.results[0]['file']['filename'];
                var this_actual_name = result.results[0]['file']['actual'];
                if(!this_filename){
                    this_filename = this_actual_name;
                }
                if(!this_actual_name){
                    this_actual_name = this_filename;
                }
                if(result.success){
					data_media_id = this_id;
					data_media_nonce = nonce;
                    $('li#id_'+this_id).find('p.edit-media').remove();
					$('li#id_'+this_id).find('form.mp-form').remove();
					$('li#id_'+this_id).find('form.copy-media').remove();
					$('li#id_'+this_id).find('span.media-meta').append('<p class="edit-media"><form class="mp-form"><label for="filename-'+this_id+'">'+mp_languages.edit_filename+'</label><span class="input-wrapper"><input class="edit-media-input blanked" id="filename-'+this_id+'" value="'+this_filename+'" /></span><input type="submit" class="button save-edited-media" data-media-id="'+data_media_id+'" data-media-nonce="'+data_media_nonce+'" value="'+mp_languages.edit_media+'" /></form></p>');
                }
            }
        });
	});
	jQuery('input.save-edited-media').live('click',function(e){
		e.preventDefault();
		var this_id = jQuery(this).attr('data-media-id');
		var nonce = jQuery(this).attr('data-media-nonce');
		var filename = $(this).prev().find('input').val();
		jQuery.ajax({
			url:mp_root_url+'mp-includes/pjax/edit-media-meta.php',
			data:({ id: this_id, nonce: nonce, filename: filename }),
			type: "POST",
			dataType: 'json',
			success: function(result){
				if(result.success){
					$('span#image_filename_'+this_id).html(filename);
					$('li#id_'+this_id).find('p.edit-media').html('');
					$('li#id_'+this_id).find('form.mp-form').html('<span class="notification success">'+result.message+'</span>');
				}else{
					$('li#id_'+this_id).find('p.edit-media').html('');
					$('li#id_'+this_id).find('form.mp-form').html('<span class="notification error">'+result.message+'</span>');
				}
			}
		});
	});
	jQuery('ul#mp-media-gallery a.copy-media').live('click',function(e){
        e.preventDefault();
		var mongo_id = $(this).attr('data-mongo-id');
		$('li#id_'+mongo_id).find('form.copy-media').remove();
		$('li#id_'+mongo_id).find('p.edit-media').remove();
		$('li#id_'+mongo_id).find('form.mp-form').remove();
		$('li#id_'+mongo_id).find('span.media-meta').append('<form class="copy-media mp-form"><label for="id-'+mongo_id+'">'+mp_languages.copy_media_id+'</label><span class="input-wrapper"><input id="id-'+mongo_id+'" type="text" class="blanked" readonly="readonly" value="'+mongo_id+'" /></span></form>');
	});
});