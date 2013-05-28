/* VERY HACKY AND ALMOST CONSISTENT HACK / FIX FOR IPAD ZOOM */
/* TODO: ONLY WORKS 60% OF TIME - NEED TO GAIN CONTROL OF THIS */
var ios_timeout = false;
window.onorientationchange = function(){
    ios_timeout = false;
    var viewportmeta = document.querySelector('meta[name="viewport"]');
    viewportmeta.content = 'maximum-scale=1.0';
    ios_timeout = setTimeout(function(){
        var orientation = window.orientation;
        var viewport_meta = document.querySelector('meta[name="viewport"]');
        if ((orientation === 0) || (orientation === 180)){
           //alert('iPad is in Portrait mode');
           viewport_meta.content = 'maximum-scale=2.0';
        }else{
            //alert('iPad is in Landscape mode. The screen is turned to the left');
            viewport_meta.content = 'maximum-scale=2.0';
        }
    },1000);
};
/* ISCROLL FIRST */
var myScroll;
function iscroll_loaded() {
	iscroll_select_fix();
	myScroll = new iScroll('mp-content');
}
function force_mu_plugins_select(id){
	var this_el = jQuery('#'+id);
	var this_id = jQuery('#'+id).val();
    jQuery(this_el).parent().parent().find('article').addClass('hidden');
    if(this_id!='none'){
        jQuery('article#'+this_id).removeClass('hidden');
    }
}
function iscroll_select_fix(){
	$('div.admin-widget-wrapper select').each(function(){
		var id = $(this).attr('id');
		this.addEventListener('touchstart' /*'mousedown'*/, function(e) {
			force_mu_plugins_select(id);
			e.stopPropagation();
		}, false);
	})
}

var timer = 0;
function resize_timeOut(){
	if(!timer){
		timer = 1;
		timeOut_trigger();
	}
}
var timeOut_trigger = function(){
	setTimeout(timeOut,1500);
}
function timeOut(){
	timer = 0;
	myScroll.refresh();
}
if (document.addEventListener){
	document.addEventListener('touchmove', function (e) {
		//e.preventDefault();
		var mp_content_height = $('#mp-content').height();
		var content_height = $('#mp-content .scroll-wrapper').height();
		if(content_height > mp_content_height){
			resize_timeOut();
			//myScroll.refresh();
		}
	}, false);
}

function iosInit(){
	iscroll_loaded();
	/* THIS FIXES A BUG WITH ISCROLL AND INPUT FIELDS */
	$('div.admin-widget-wrapper .blanked, #object-actions').live('click', function(){
		$(this).focus();
	});
	$('div.admin-widget-wrapper select').each(function(){
		$(this).live('click',function(){
			var id = $(this).attr('id');
			force_mu_plugins_select(id);
		});
	});
};
window.onload = iosInit;