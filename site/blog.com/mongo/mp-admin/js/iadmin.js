/* SWIPE ACTIONS
$('body').touchwipe({
	wipeLeft: function(){
		// CLOSE SIDEBAR
		$('#left-column').animate({'width':0},0,function(){
			$('#right-column').animate({'width':'100%'},0);
			// SIDEBAR NOW CLOSED
		});
	},
	wipeRight: function(){
		// OPEN SIDEBAR
		$('#left-column').animate({'width':'35%'},0,function(){
			$('#right-column').animate({'width':'65%'},0);
			// SIDEBAR NOW OPEN
		});
	}
	//wipeUp: function(){ alert('You swiped up!') },
	//wipeDown: function(){ alert('You swiped down!') }
}); */

$('li.menus').live('click',function(e){
	var page = $(this).attr('data-page');
	if(history.pushState){
		e.preventDefault();
		var nonce = $('input#mp-admin-nonce').val();
		var current_selection = $('li.menus.current');
		var selection = $(this);
		var is_loading = false;
		if($('ul.menu-lists li.loading').length>0){ is_loading = true; }
		if((!$(this).hasClass('current'))&&(is_loading===false)){
			$(selection).addClass('loading');
			$.ajax({
				url:mp_root_url+'mp-includes/pjax/get-mp-admin-page.php',
				data:({ nonce: nonce, page: page }),
				type: "POST",
				dataType: 'json',
				success: function(result){
					if(result.success==true){
						if(page=='settings') url=mp_admin_url+'options/';
						else if(page=='dashboard') url=mp_admin_url;
						else url = mp_admin_url+page+'/';
						window.history.pushState({},"", url);
						if($(current_selection).hasClass('got-submenu')){
							$(current_selection).animate({height:39},500,function(){
								if($(selection).hasClass('got-submenu')){
									submenu_height = $(selection).find('.sub-menu').height();
									li_height_plus_padding = 89;
									$(selection).animate({height:li_height_plus_padding+submenu_height},500);
								}
							});
						}else{
							if($(selection).hasClass('got-submenu')){
								submenu_height = $(selection).find('.sub-menu').height();
								li_height_plus_padding = 89;
								$(selection).animate({height:li_height_plus_padding+submenu_height},500);
							}
						}
						$(selection).removeClass('loading');
						$(current_selection).removeClass('current');
						$(selection).addClass('current');
						$('div#current-content').animate({opacity:0},500,function(){
							$('div#current-content').html(result.message);
							initDataTables();
							mp_fetch_feeds();
							if(is_ios){
								iscroll_select_fix();
								myScroll.refresh();
							}
							$('div#current-content').animate({opacity:1},500,function(){
								// AND THEN ...?
							});
						});
					}
				}
			});
		}
	}else{
		if(page=='dashboard'){ this_url = mp_admin_url; }
		else if(page=='settings'){ this_url = mp_admin_url+'options/'; }
		else { this_url = mp_admin_url+page+'/'; }
		window.location.href = this_url;
	}
});
$('h3.admin-widget-title').live('click',function(e){
	var this_icon = $(this);
	var widget_type = $(this).parent().attr('data-admin-widget-type');
	alert('Additional options for the '+widget_type+' admin widget will be coming soon!');
});
$('li.menus a.button').live('click',function(e){
	e.preventDefault();
	var this_page = $(this).attr('data-page');
	if(this_page == 'logout'){
		this_url = mp_admin_url + this_page;
	}else{
		this_url = mp_admin_url + this_page + '/';
	}
	window.location.href = this_url;
	return false;
});
if(history.pushState){
	window.onpopstate = function(e){
		if(e.state){
			var path_array = window.location.pathname.split('/');
			var component = path_array[path_array.length-2];
			//if((component=='media')||(component=='settings')){
				if((component!='settings')&&(component!='media')&&(component!='objects')) {
					component = 'dashboard';
				}
				var this_element = $('ul.menu-lists li#'+component);
				var nonce = $('input#mp-admin-nonce').val();
				var page = $(this_element).attr('data-page');
				var current_selection = $('li.menus.current');
				var selection = $(this_element);
				if(!$(this_element).hasClass('current')){
					$(selection).addClass('loading');
					$.ajax({
						url:mp_root_url+'mp-includes/pjax/get-mp-admin-page.php',
						data:({ nonce: nonce, page: page }),
						type: "POST",
						dataType: 'json',
						success: function(result){
							if(result.success==true){
								if(page=='settings') url=mp_admin_url+'options/';
								else if(page=='dashboard') url=mp_admin_url;
								else url = mp_admin_url+page+'/'
								window.history.pushState({},"", url);
								$(selection).removeClass('loading');
								$(current_selection).removeClass('current');
								$(selection).addClass('current');
								$('div#current-content').animate({opacity:0},500,function(){
									$('div#current-content').html(result.message);
									initDataTables();
									myScroll.refresh();
								});
							}
						}
					});
				}
			//}else{

			//}
		}
	};
}