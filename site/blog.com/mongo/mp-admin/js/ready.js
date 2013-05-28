function mp_js_object_count(obj) {
    var count = 0;
    for(var prop in obj) {
    	if(obj.hasOwnProperty(prop))
    		++count;
    }
    return count;
}

function mp_animate_body_opacity(){
	$('body').animate({'opacity':1},750);
}

function initJS(page) {
	var screen_width = $(window).width();
	var is_handheld = true;
	if(screen_width > 465) {
		mp_load_styles_via_js();
		is_handheld = false;
	}
    $('.jdash-column:nth-child(odd)').addClass('odd');
    $('.jdash-column:nth-child(even)').addClass('even');
    init_jwysiwyg();
    initForms();
	init_notifications();
	init_contributors();
    $(window).resize(function() {
        /* RESIZE THINGS */
		if($(window).width() > 465) {
			if(is_handheld){
				mp_load_styles_via_js();
				is_handheld = false;
			}
		}
    });
}

var init_notifications = function(){
	if($('div#mp-notifications').length>0){
		jQuery.ajax({
			url:mp_root_url+'mp-includes/ajax/check-version.php',
			dataType: 'json',
			success: function(result){
				if(result.update_needed){
					$('div#mp-notifications').show("normal");
				}
			}
		});
	}
}

var init_contributors = function(){
	if($('div#admin-widget-contributors').length>0){
		jQuery.ajax({
			url:mp_root_url+'mp-includes/ajax/get-contributors.php',
			dataType: 'html',
			success: function(result){
				if(result){
					$('div#admin-widget-contributors').html(result);
				}
			}
		});
	}
}

var init_jwysiwyg = function(){
    jQuery('input#title').live('blur',function(event){
        var title = jQuery(this).val();
        var current_slug = jQuery('input#slug').val();
        var current_slug_id = jQuery('input#slug_id').val();
        if(!current_slug_id){
            if(!current_slug){
                var nonce = jQuery('form#object input[name="mp_nonce"]').val();
                jQuery.ajax({
                    url:mp_root_url+'mp-includes/pjax/create-slug.php',
                    data:({ title: title, nonce: nonce }),
                    type: "POST",
                    dataType: 'json',
                    success: function(result){
                        if(result.success){
                            jQuery('div#temporary-slug').html(result.message);
                            jQuery('a#mp-object-slug-switcher').show('normal',function(){
                                jQuery('div#temporary-slug').show('normal');
                                jQuery('label#object-slug-label').show('normal');
                            })
                        }else{
                            /* ERROR */
                        }
                    }
                });
            }
        }else{
            var nonce = jQuery('form#object input[name="mp_nonce"]').val();
            jQuery.ajax({
                url:mp_root_url+'mp-includes/pjax/create-slug.php',
                data:({ title: title, nonce: nonce, slug_id: current_slug_id }),
                type: "POST",
                dataType: 'json',
                success: function(result){
                    if(result.success){
                        jQuery('div#temporary-slug').html(result.message);
                        jQuery('a#mp-object-slug-switcher').show('normal',function(){
                            jQuery('div#temporary-slug').show('normal');
                            jQuery('label#object-slug-label').show('normal');
                        })
                    }else{
                        /* ERROR */
                    }
                }
            });
        }
    });
    jQuery('input#slug').live('blur',function(event){
        var new_slug = jQuery(this).val();
        var nonce = jQuery('form#object input[name="mp_nonce"]').val();
        jQuery('input#slug_id').val('');
        jQuery.ajax({
            url:mp_root_url+'mp-includes/pjax/create-slug.php',
            data:({ title: new_slug, nonce: nonce }),
            type: "POST",
            dataType: 'json',
            success: function(result){
                if(result.success){
                    jQuery('div#temporary-slug').html(result.message);
                    jQuery('div#temporary-slug').show('normal');
                }else{
                    /* ERROR */
                }
            }
        });
    });
    jQuery('a#mp-object-slug-switcher').live('click',function(e){
        e.preventDefault();
        var temp_slug = jQuery('div#temporary-slug').html();
        var current_slug = jQuery('input#slug').val();
        if(temp_slug){ if(!current_slug){ jQuery('input#slug').val(temp_slug); }}
        jQuery('div#mp-object-slug-wrapper').toggle(0,function(){
            /* AFTER ANIMATION */
        })
    });
    jQuery('a.switch-to-publish').live('click',function(e){
        e.preventDefault();
        jQuery('a.switch-to-publish').attr('class','switch-to-published button mini');
        jQuery('span.mp-data-view').hide();
        jQuery('span.mp-publish-view').show();
        jQuery('body').removeClass('data-view');
        jQuery('body').addClass('publish-view');
        if(jQuery('div#mp-objects-table').length>0){
            jQuery('div#mp-objects-table').toggle(0,function(){
                jQuery("textarea#content").wysiwyg({
                    css: mp_root_url+'mp-admin/css/forms.css',
                    rmUnusedControls: true,
                    autoGrow: true,
                    maxHeight: 600,
                    controls: {
                        bold: { visible : true },
                        italic: { visible : true },
                        underline: { visible : true },
                        createLink: { visible : true },
                        h1: { visible : true },
                        h2: { visible : true },
                        h3: { visible : true },
                        justifyLeft: { visible : true },
                        justifyRight: { visible : true },
                        justifyCenter: { visible : true },
                        justifyFull: { visible : true },
                        paragraph: { visible : true },
                        insertOrderedList: { visible : true },
                        insertUnorderedList: { visible : true },
                        html: { visible : true },
                        redo: { visible : true },
                        undo: { visible : true }
                    }
                });
            });
        }else{
            jQuery("textarea#content").wysiwyg({
                css: mp_root_url+'mp-admin/css/forms.css',
                rmUnusedControls: true,
                autoGrow: true,
                maxHeight: 600,
                controls: {
                    bold: { visible : true },
                    italic: { visible : true },
                    underline: { visible : true },
                    createLink: { visible : true },
                    h1: { visible : true },
                    h2: { visible : true },
                    h3: { visible : true },
                    justifyLeft: { visible : true },
                    justifyRight: { visible : true },
                    justifyCenter: { visible : true },
                    justifyFull: { visible : true },
                    paragraph: { visible : true },
                    insertOrderedList: { visible : true },
                    insertUnorderedList: { visible : true },
                    html: { visible : true },
                    redo: { visible : true },
                    undo: { visible : true }
                }
            });
        }
    });
    jQuery('a.switch-to-published').live('click',function(e){
        e.preventDefault();
        jQuery('span.mp-data-view').hide();
        jQuery('span.mp-publish-view').show();
        var new_content = jQuery('textarea#content').val();
        jQuery('textarea#content').hide();
        jQuery('div.wysiwyg').show();
        jQuery('body').removeClass('data-view');
        jQuery('body').addClass('publish-view');
        if(jQuery('div#mp-objects-table').length>0){
            jQuery('div#mp-objects-table').toggle(0,function(){
                jQuery('textarea#content').wysiwyg('setContent',new_content);
            });
        }else{
            jQuery('textarea#content').wysiwyg('setContent',new_content);
        }
    });
    jQuery('a.switch-to-data').live('click',function(e){
        e.preventDefault();
        jQuery('textarea#content').show();
        jQuery('span.mp-publish-view').hide();
        jQuery('span.mp-data-view').show();
        jQuery('body').removeClass('publish-view');
        jQuery('body').addClass('data-view');
        var wysiwyg_content = jQuery('textarea#content').wysiwyg('getContent');
        jQuery('div.wysiwyg').hide();
        if(jQuery('div#mp-objects-table').length>0){
            jQuery('div#mp-objects-table').toggle(0,function(){
                jQuery('textarea#content').val(wysiwyg_content);
            });
        }else{
            jQuery('textarea#content').val(wysiwyg_content);
        }
    });
}

var initForms = function(){
    jQuery('form#object').live('submit',function(e){
        e.preventDefault();
        var notifications = jQuery(this).find('span.notification');
        var id = jQuery('form#object input#id').val();
        var mongo_id = jQuery('form#object input#mongo_id').val();
        var type = jQuery('form#object input#type').val();
        if(!type){ type=jQuery('form#object select#type').val(); }
        var slug = jQuery('form#object input#slug').val();
        var slug_id = jQuery('form#object input#slug_id').val();
        var title = jQuery('form#object input#title').val();
        var content = jQuery('form#object textarea#content').val();
        var nonce = jQuery('form#object input[name="mp_nonce"]').val();
        var timestamp = new Date().getTime();
        var custom = '';
        var this_input = jQuery(this).find('input[type="submit"]');
        jQuery(this_input).addClass('loading_light');
        if(jQuery('div#mp-object-custom-wrapper').length>0){
            jQuery('div.mp-custom-field-wrapper').each(function(index){
                var obj = new Array(); var object = new Array();
                var this_id = jQuery(this).attr('data-i');
                var key = jQuery(this).find('input#cfk'+this_id).val();
                var value = jQuery(this).find('input#cfv'+this_id).val();
                if(!custom){
                    custom = 'cfk'+this_id+'==='+key+'__cfv'+this_id+'==='+value;
                }else{
                    custom = custom+'___cfk'+this_id+'==='+key+'__cfv'+this_id+'==='+value;
                }
            });
        }
        jQuery.ajax({
            url:mp_root_url+'mp-includes/pjax/add-object.php',
            data:({ id: id, mongo_id: mongo_id, type : type, slug : slug, slug_id : slug_id, title : title, content : content, nonce: nonce, custom:custom }),
            type: "POST",
            dataType: 'json',
            success: function(result){
                jQuery(this_input).removeClass('loading_light');
                if(result.success){
                    jQuery(notifications).removeClass('errors');
                    jQuery(notifications).addClass('success');
                    jQuery(notifications).html(result.message);
                    jQuery(notifications).animate({'opacity':0},0,function(){
                        jQuery(this).animate({'opacity':1},'normal',function(){
                            /* AFTER ANIMATION */
                        });
                    });
                    if(jQuery('table#objects').length>0){
                        if(result.state=='insert'){
                            jQuery('table#objects').dataTable().fnAddData([
                                '', type, slug, title, timestamp, timestamp, 'BUTTONS'
                            ]);
                        }else if(result.state=='update'){
                            jQuery('table#objects').dataTable().fnDraw();
                        }
                    }
                }else{
                    jQuery(notifications).removeClass('success');
                    jQuery(notifications).addClass('errors');
                    jQuery(notifications).html(result.message);
                    jQuery(notifications).animate({'opacity':0},0,function(){
                        jQuery(this).animate({'opacity':1},'normal',function(){
                            /* AFTER ANIMATION */
                        });
                    });
                }
            }
        });
    });
    jQuery('a#mp-add-object-type').live('click',function(e){
        e.preventDefault();
        var this_form_id = jQuery(this).attr('data-form-id');
        var this_form = jQuery('form#'+this_form_id);
        var state = jQuery('div#mp-object-type-wrapper').attr('class');
        jQuery('div#mp-object-type-wrapper').toggle(0,function(){
            if(state=='closed'){
                jQuery(this_form).find('input#_type').attr('name','mp[type]');
                jQuery(this_form).find('input#_type').attr('id','type');
                jQuery(this_form).find('select#type').attr('name','_mp[type]');
                jQuery(this_form).find('select#type').attr('id','_type');
                jQuery('div#mp-object-type-wrapper').removeClass('closed');
                jQuery('div#mp-object-type-wrapper').addClass('opened');
            }else{
                jQuery(this_form).find('input#type').attr('name','_mp[type]');
                jQuery(this_form).find('input#type').attr('id','_type');
                jQuery(this_form).find('select#_type').attr('name','mp[type]');
                jQuery(this_form).find('select#_type').attr('id','type');
                jQuery('div#mp-object-type-wrapper').removeClass('opened');
                jQuery('div#mp-object-type-wrapper').addClass('closed');
            }
        });
    });
    jQuery('a#mp-object-toggle-custom-fields').live('click',function(e){
        var custom = '';
        if(jQuery('div#mp-object-custom-wrapper').length>0){
            jQuery('div.mp-custom-field-wrapper').each(function(index){
                var obj = new Array(); var object = new Array();
                var this_id = jQuery(this).attr('data-i');
                var key = jQuery(this).find('input#cfk'+this_id).val();
                var value = jQuery(this).find('input#cfv'+this_id).val();
                if(!custom){
                    custom = 'cfk'+this_id+'==='+key+'__cfv'+this_id+'==='+value;
                }else{
                    custom = custom+'___cfk'+this_id+'==='+key+'__cfv'+this_id+'==='+value;
                }
            });
        }
        e.preventDefault();
        jQuery('div#mp-object-custom-wrapper').toggle(0,function(){
            if(jQuery('a#mp-object-add-custom-field').hasClass('hidden')){
                jQuery('a#mp-object-add-custom-field').removeClass('hidden');
                if(jQuery('div#mp-object-custom-wrapper div.mp-custom-field-wrapper').length<1){
                    var nonce = jQuery('form#object input[name="mp_nonce"]').val();
                    jQuery.ajax({
                        url:mp_root_url+'mp-includes/pjax/new-custom-field-row.php',
                        data:({ i: 0, nonce: nonce }),
                        type: "POST",
                        dataType: 'json',
                        success: function(result){
                            if(result.success){
                                jQuery('div#mp-object-custom-wrapper').append(result.content);
                                jQuery('div#mp-object-custom-wrapper').attr('data-i',0);
                                jQuery('a#mp-object-add-custom-field').removeClass('hidden');
                            }
                        }
                    });
                }
            }else{
                jQuery('a#mp-object-add-custom-field').addClass('hidden');
            }
        });
    });
    jQuery('a#mp-object-add-custom-field').live('click',function(e){
        e.preventDefault();
        var current_custom_id = parseInt(jQuery('div#mp-object-custom-wrapper').attr('data-i'));
        if(!current_custom_id){ current_custom_id = 0; }
        var nonce = jQuery('form#object input[name="mp_nonce"]').val();
        var new_id = current_custom_id+1;
        jQuery.ajax({
            url:mp_root_url+'mp-includes/pjax/new-custom-field-row.php',
            data:({ i: current_custom_id, nonce: nonce }),
            type: "POST",
            dataType: 'json',
            success: function(result){
                if(result.success){
                    jQuery('div#mp-object-custom-wrapper').append(result.content);
                    jQuery('div#mp-object-custom-wrapper').attr('data-i',new_id);
                }
            }
        });
    });
    jQuery('div.custom-actions a.remove-fields').live('click',function(e){
        e.preventDefault();
        var i = jQuery(this).attr('data-i');
        var this_field = jQuery(this).parent().parent();
        var current_count = jQuery(this_field).parent().attr('data-i');
        var new_count = current_count-1;
        jQuery(this_field).parent().attr('data-i',new_count);
        jQuery(this_field).remove();
    });
    jQuery('input#mp_file').live('click',function(){
        jQuery('input#mp_file_fake').addClass('active');
    });
    jQuery('input#mp_file_fake.active').live('focus',function(){
        jQuery('input#mp_file').trigger('click');
        jQuery('input#mp_file_fake.active').removeClass('active');
    });
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
    });
	jQuery('ul#mp-media-gallery a.copy-media').live('click',function(e){
		e.preventDefault();
		var mongo_id = $(this).attr('data-mongo-id');
		$('li#id_'+mongo_id).find('form.copy-media').remove();
		$('li#id_'+mongo_id).find('p.edit-media').remove();
		$('li#id_'+mongo_id).find('form.mp-form').remove();
		$('li#id_'+mongo_id).find('span.media-meta').append('<form class="copy-media mp-form"><label for="id-'+mongo_id+'">'+mp_languages.copy_media_id+'</label><span class="input-wrapper"><input id="id-'+mongo_id+'" type="text" class="blanked" readonly="readonly" value="'+mongo_id+'" /></span></form>');
	});
}
function mp_update_media_gallery(id, new_name){
    jQuery('ul#mp-media-gallery li#id_'+id).find('.filename').text('Download Link:<br />'+new_name);
}