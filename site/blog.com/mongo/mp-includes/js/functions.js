/* FOR FEEDS */
function mp_get_feed(container, url, limit){
	if(!url){ url = 'http://labs.laulima.com/activity/feed/'; }
	var nonce = $(container).attr('data-nonce');
	jQuery.ajax({
		url:mp_root_url+'mp-includes/ajax/get-feed.php',
		data:({ nonce: nonce, url: url, limit: limit }),
		type: "POST",
		dataType: 'html',
		success: function(result){
			$(container).html(result);
		}
	});
}
function mp_fetch_feeds(){
	$('.fetch-feed').each(function(i){
		var container = $(this);
		var url = $(this).attr('data-url');
		var limit = $(this).attr('data-limit');
		mp_get_feed(container, url, limit);
	});
}

function mp_get_avatar(object){
	var this_avatar = $(object);
	var this_src = $(this_avatar).attr('src');
	var nonce = $(this_avatar).attr('data-avatar-nonce');
	var user_id = $(this_avatar).attr('data-user-id');
	var default_avatar = mp_root_url+'mp-includes/images/add_image.png';
	$(this_avatar).addClass('loading');
	$(this_avatar).attr('src','');
	$.ajax({
		url: mp_root_url+'mp-includes/ajax/get-avatar.php',
		data:({ user_id: user_id, nonce: nonce }),
		type: "POST",
		dataType: 'json',
		success: function(result){
			if(result===null){ return false; }
			if(result.success){
				if(result.message.entry){
					$(this_avatar).attr('src',result.message.entry[0]['thumbnailUrl']);
				}else{
					$(this_avatar).attr('src',result.message);
				}
			}else{
				if(result.message){
					alert(result.message);
				}else{
					$(this_avatar).removeClass('loading').attr('src',default_avatar);
				}
			}
			$(this_avatar).removeClass('loading');
			return false;
		},
		failure: function(){
			return false;
		}
	});
}
function mp_fetch_avatars(){
	$('img.fetch-avatar').each(function(){
		var avatar = $(this);
		mp_get_avatar(avatar);
	});
}

function mp_get_content(container){
	var this_container = $(container);
	var mongo_id = $(this_container).attr('data-mongo-id');
	var nonce = $(this_container).attr('data-nonce');
	var shortcodes = $(this_container).attr('data-shortcodes');
	$(this_container).addClass('loading');
	$.ajax({
		url:mp_root_url+'mp-includes/ajax/get-object.php',
		data:({ nonce: nonce, mongo_id: mongo_id, shortcodes: shortcodes }),
		type: "POST",
		dataType: 'json',
		success: function(result){
			if(result.success==true){
				$(container).html(result.content);
				$(this_container).removeClass('loading');
			}
		}
	});
}
function mp_fetch_contents(){
	$('.fetch-content').each(function(){
		var container = $(this);
		mp_get_content(container);
	});
}

/* FIRE OFF THE FUNCTIONS */
$(window).load(function(){
	mp_fetch_feeds();
	mp_fetch_avatars();
	mp_fetch_contents();
});