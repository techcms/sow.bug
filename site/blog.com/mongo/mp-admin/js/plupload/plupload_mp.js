plupload.addI18n({
	'Select files' : mp_languages.plupload_select,
	'Add files to the upload queue and click the start button.' : mp_languages.plupload_add1,
	'Filename' : mp_languages.plupload_filename,
	'Status' : mp_languages.plupload_status,
	'Size' : mp_languages.plupload_size,
	'Add files' : mp_languages.plupload_add2,
	'Start upload': mp_languages.plupload_start1,
	'Stop current upload' : mp_languages.plupload_stop,
	'Start uploading queue' : mp_languages.plupload_start2,
	'Drag files here.' : mp_languages.plupload_drag
});

var jcrop_api;

$(document).ready(function(){
	/* CHECK IF AVATAR CHANGED */
	$('div.admin-widget-wrapper').each(function(i){
		if($(this).attr('data-admin-widget-type')=='your-avatar'){
			if($(this).find('span.notification.success').length>0){
				var user_id = $(this).attr('data-user-id');
				var nonce = $(this).attr('data-avatar-nonce');
				$.ajax({
					url: mp_root_url+'mp-includes/ajax/get-avatar.php',
					data:({ user_id: user_id, nonce: nonce }),
					type: "POST",
					dataType: 'json',
					success: function(result){
						if(result.success){
							$('span.user-avatar a img.avatar').attr('src',result.message);
							$('div#mp-welcome').hide("normal",function(){ $(this).remove(); });
						}else{
							alert(result.message);
						}
					}
				});
			}
		}
	});
});

function updateCoords(c){
	ratio = $('#ratio').val();
	$('#x').val(c.x / ratio);
	$('#y').val(c.y / ratio);
	$('#w').val(c.w / ratio);
	$('#h').val(c.h / ratio);
};

function checkCoords(){
	ratio = $('#ratio').val();
	if (parseInt(($('#w').val())/ratio)) return true;
	alert('Please select a crop region then press submit.');
	return false;
};

function mp_plupload(id, max_files, url, nonce, action, user_id){
	if((typeof(id) == 'undefined')||(id == '')){ id = 'html5_uploader'; }
	if((typeof(max_files) == 'undefined')||(max_files == '')){ max_files = 10; }
	if((typeof(url) == 'undefined')||(url == '')){ url = mp_root_url+'mp-includes/pjax/plupload.php'; }
	if((typeof(nonce) == 'undefined')||(nonce == '')){ nonce = false; }
	if((typeof(action) == 'undefined')||(action == '')){ action = 'gallery'; }
	if((typeof(user_id) == 'undefined')||(user_id == '')){ user_id = false; }
	if(action=='avatar'){ max_files = 1; }
	multi_selection = true; if(max_files==1){ multi_selection = false; }
	if(action=='avatar'){
		these_filters =  [
			{title : "Image files", extensions : "jpg,gif,png"}
		];
	}else{
		these_filters =  [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		];
	}
	if(is_ios!=true){
	$('#'+id).pluploadQueue({
		// General settings
		runtimes : 'html5, html4',
		url : url,
		max_file_size : '10mb',
		chunk_size : '1mb',
		multi_selection: multi_selection, // THIS ONLY EFFECTS HOW MANY USER CAN SELECT FROM POP-UP
		unique_names : true,
		multipart_params : {nonce: nonce, action: action, id: user_id},
		// Resize images on clientside if we can
		// -> RESIZE BUG WITH PNGs resize : {width : 150, height : 150, quality : 90},
		// resize : {width : 150, height : 150, quality : 90},
		// Specify what files to browse for
		filters : these_filters,
		init : {
            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    if (up.files.length > max_files) {
                        up.removeFile(file);
                    };
				});
			},
			FileUploaded: function(up, file, res) {
				if(up.total.queued == 0) {
					file_name = file['name'].toLowerCase();
					file_array = file['name'].split('.');
					file_type = file_array[1];
					jsoned_results = jQuery.parseJSON( res['response'] );
					final_file = jsoned_results[file_name];
					if(final_file['original_id']){
						var original_id = final_file['original_id']['$id'];
					}
					if((typeof(final_file)=='undefined')||(typeof(final_file)=='')){
						if(jsoned_results.message){
							$('#'+id).parent().prepend('<span class="notification error"><p>'+jsoned_results.message+'</p></span>');
						}else{
							$('#'+id).parent().prepend('<span class="notification error"><p>'+mp_languages.error_uploading+'</p></span>');
						}
					}else{
						if(final_file.success){
							$('#'+id).parent().prepend('<span class="notification success"><p>'+final_file.message+'</p></span>');
							if(action=='avatar'){
								$('#'+id).parent().prepend('<div id="cropper-'+id+'" class="image-cropper"><span class="notification">Crop Image</span><img class="placeholder" src="'+mp_media_url+'/'+file_name+'" id="crop-'+id+'" /></div>');
								$('#crop-'+id).Jcrop({
									onSelect:    updateCoords,
									bgColor:     'black',
									bgOpacity:   .4,
									aspectRatio: 1 / 1
								}, function(){
									jcrop_api = this;
								});
								$('#cropper-'+id).show("normal",function(){
									$('#mp-img-src').val(mp_media_url+'/'+file_name);
									$('#mp-img-type').val(file_type);
									$('#mp-original-id').val(original_id);
									var this_img = $("#crop-"+id).next().find('img.placeholder');
									var this_img_width; var this_img_height;
									$('<img />').attr('src',this_img[0].src).load(function(){
										this_img_width = this.width;
										this_img_height = this.height;
										
										var available_width = $('#crop-'+id).parent().width();
										var ratio = available_width / this_img_width;

										$('#ratio').val(ratio);
										jcrop_api.destroy();
										$('#crop-'+id).next().find('img.placeholder').height(this_img_height * ratio);
										$('#crop-'+id).next().find('img.placeholder').width(available_width);
										$('#crop-'+id).next().find('.jcrop-tracker').width(available_width);
										$('#crop-'+id).next().find('.jcrop-tracker').height(this_img_height * ratio);
										$('#crop-'+id).next().width(available_width);
										$('#crop-'+id).next().height(this_img_height * ratio);
										$('#crop-'+id).Jcrop({
											onSelect:    updateCoords,
											bgColor:     'black',
											bgOpacity:   .4,
											aspectRatio: 1 / 1
										});
										$('#crop-'+id).parent().append('<input type="submit" class="submit" value="Crop and Replace Image" />');

									});
									
								});
							}else{
								window.location = mp_admin_url+'media/';
							}
						}else{
							$('#'+id).parent().prepend('<span class="notification error"><p>'+final_file.message+'</p></span>');
						}
					}
				}
			}
		}
    });
	}
}