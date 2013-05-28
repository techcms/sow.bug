/* Fit homepage images to screen size */

var current_big_image = 0;
var resize_me = 1;
   
$(function () {
	 function isiPhone(){
			return (
				(navigator.platform.indexOf("iPhone") != -1) ||
				(navigator.platform.indexOf("iPod") != -1)
			);
		}
	
	$("#pg_preview").css({
		height: $(window).height()+"px",
		width: $(window).width()+"px" 
	});
   $(window).resize(function () {
      $(".h").css({
         height: $(window).height()+"px"
      });
	  $("#pg_preview").css({
		height: $(window).height()+"px",
		width: $(window).width()+"px"
	  });
   });
   
if(resize_me > 0){

   $("#pg_preview img").each(function () {
	   var img = $(this);

	   var img_width = img.attr("width");
	   var img_height = img.attr("height");
/*	   img.attr("width", img_width);
	   img.attr("height", img_height);*/
	   img.attr("w", img_width);
	   img.attr("h", img_height);
      
      img.css({
         width: 'auto',
         'min-height': '0px',
         visibility: 'visible'
      });
      
      img.removeAttr("width").removeAttr("height");
      
      var img_w = parseInt( img.attr("w") );
      var img_h = parseInt( img.attr("h") );
      var current_img_prop = img_w/img_h;
      
      $(window).resize(function () {
         if ( img.index() != current_big_image )
            return;
        
	  //$(window).trigger("orientationchange");
         var window_h = $('body').height();
         var window_w = $('body').width();
         var h = window_h;
         var w = window_w;
         var w_margin = 0;
         var h_margin = 0;
		
         var current_prop = window_w/window_h;
         
         
         if (current_prop > current_img_prop)
         {
            w = window_w;
            h = w / current_img_prop;  
         }
         else
         {
            h = window_h;
            w = h * current_img_prop;  
         }
         w_margin = (window_w - w) / 2;
         h_margin = (window_h - h) / 2;
         
         img.css({
            height: h+"px",
            width:  w+"px",
            marginLeft: w_margin+"px",
            marginTop: h_margin+"px"
         });         
      });
   });
   $(window).trigger('resize');
} else {
	$(window).resize(function () {
		var window_width= $(window).width();
		$("#pg_preview img").each(function () {
			var img = $(this);
			var img_width = img.width();
			img.css({
				left: (window_width-img_width)/2+"px",
				//position: 'static',
				margin: '0 auto',
				visibility: 'visible'
			});
		});
	});
	
	$("#pg_preview img").load(function() {
		$(window).trigger('resize');
	});
	$(window).bind('orientationchange', function(event) {
 
		$(window).trigger("resize");
	});
}

});


/* Slider main code */

$(function() {

	//index of current item
	var current				= 0;
	//speeds / ease type for animations
	var fadeSpeed			= 400;
	var animSpeed			= homeSlider.animSpeed;
	var easeType			= 'easeOutCirc';
	//caching
	var $thumbScroller		= $('#thumbScroller');
	var $scrollerContainer	= $thumbScroller.find('.container');
	var $scrollerContent	= $thumbScroller.find('.content');
	var $pg_title 			= $('#pg_title');
	var $pg_preview 		= $('#pg_preview');
	var $pg_desc1 			= $('#pg_desc1');
	var $pg_desc2 			= $('#pg_desc2');
	var $overlay			= $('#overlay');
	//number of items
	var scrollerContentCnt  = $scrollerContent.length;
	var sliderHeight		= $(window).height();
	//we will store the total height
	//of the scroller container in this variable
	var totalContent		= 0;
	//one items height
	var itemHeight			= 0;
	
	function fix_elems($nextDesc1, $nextDesc2)
	{	
		Cufon.CSS.ready(function() {
		
/*
			//var t = $(window).height() - $nextDesc2.height() - 53 - 20;
			$nextDesc2.css('bottom', 60+"px"); 

			var header_right = $nextDesc2.width() - $nextDesc1.width() + 200;
			//t -= $nextDesc1.height();
			//t -= 10; 
			var header_bottom = $nextDesc2.height() + 70;
			$nextDesc1.css( { 'bottom': header_bottom, 'right':header_right });
*/





/* First-slide zero-width issue: fixed by Nastya */
			//var t = $(window).height() - $nextDesc2.height() - 53 - 20;
			$nextDesc2.css('bottom', 60+"px"); 
			if ($nextDesc2.width()!=0) {
				var header_right = $nextDesc2.width() - $nextDesc1.width() + 200;
			}
			else {
				
				var header_right =650 - $nextDesc1.outerWidth();
			}
			//t -= $nextDesc1.height();
			//t -= 10; 
			var header_bottom = $nextDesc2.height() + 70;
			$nextDesc1.css( { 'bottom': header_bottom, 'right':header_right });

		});
	}
	
	fix_elems( $pg_desc1.children(":first"), $pg_desc2.children(":first") )
	
	//First let's create the scrollable container,
	//after all its images are loaded
	var cnt		= 0;
	$thumbScroller.find('img').each(function(){
		var $img 	= $(this);
		$('<img/>').load(function(){
			++cnt;
			if(cnt == scrollerContentCnt){
				//one items height
				itemHeight = $thumbScroller.find('.content:first').height();
				itemHeight = 96;
				buildScrollableItems();
				//show the scrollable container
				$thumbScroller.stop().animate({'right':'0px'},animSpeed);
				$('#thumbScroller.scroll').stop().animate({'right':'25px'},animSpeed);
			}
		}).attr('src',$img.attr('src'));
		
	});
	
	//when we click an item from the scrollable container
	//we want to display the items content
	//we use the index of the item in the scrollable container
	//to know which title / image / descriptions we will show

	$scrollerContent.bind('click',function(e){
		var $this 				= $(this);
		
      $("#thumbScroller").trigger("hover");
		
		var idx 				= $this.index();
		//if we click on the one shown then return
		if(current==idx) return;
		
		
		
		//if the current image is enlarged,
		//then we will remove it but before
		//we animate it just like we would do with the thumb
		var $pg_large			= $('#pg_large');
		if($pg_large.length > 0){
			$pg_large.animate({'left':'0px','opacity':'0'},animSpeed,function(){
				$pg_large.remove();
			});
		}
		
		//get the current and clicked items elements
		var $currentTitle 		= $pg_title.find('h1:nth-child('+(current+1)+')');
		var $nextTitle 			= $pg_title.find('h1:nth-child('+(idx+1)+')');
		var $currentThumb		= $pg_preview.find('img.pg_thumb:eq('+current+')');
		var $nextThumb			= $pg_preview.find('img.pg_thumb:eq('+idx+')');
		var $currentDesc1 		= $pg_desc1.find('div:nth-child('+(current+1)+')');
		var $nextDesc1 			= $pg_desc1.find('div:nth-child('+(idx+1)+')');
		var $currentDesc2 		= $pg_desc2.find('div:nth-child('+(current+1)+')');
		var $nextDesc2 			= $pg_desc2.find('div:nth-child('+(idx+1)+')');
		
		var h_one = 96;
		var t = $this.index()*h_one + h_one/2 + 10;
		if (e.no_anim_arrow)
		{
		   //console.log("anim arrow");
		   $(".marker").animate({
		      top: t+"px"
		   }, {
		      queue: false,
		      duration: animSpeed
		   });
		}
		
		//the new current is now the index of the clicked scrollable item
		current		 			= idx;
		current_big_image = current;
		$(window).trigger("resize");
		if (!e.manual)
		   go_next_slide(0, current, 1);
		
		//animate the current title up,
		//hide it, and animate the next one down
		$currentTitle.animate({'top':'-50px'},animSpeed,function(){
			$(this).hide();
			$nextTitle.show().animate({'top':'25px'},animSpeed);
		});
		
		//fade the next image in,
		//fade current out
		//so that the next gets visible		

		$nextThumb.css( { 'display' : 'block' , 'opacity' : '0' } ).fadeTo( 600 , 1 , function(){});
		$currentThumb.fadeTo( 700, 0, function() {$(this).css( { 'display':'none' , 'z-index':'50' } )});
		$nextThumb.css( {'z-index':'90'} );
		
		//animate both current descriptions left / right and fade them out
		//fade in and animate the next ones right / left
		
		if ($('div', $pg_desc1).length > 1) {
		var right_t =  650 - $nextDesc1.outerWidth();

		
		$currentDesc1.animate({'right':'385px', 'opacity':'0'},animSpeed,function(){
			$(this).hide();
			$pg_desc1.children().not( $nextDesc1 ).css('opacity', 0);
			$nextDesc1.show().animate({'right':right_t, 'opacity':'1'},animSpeed);
		});
		$currentDesc2.animate({'right':'245px', 'opacity':'0'},animSpeed,function(){

		$(this).hide();
		
		   fix_elems($nextDesc1, $nextDesc2);
		
		   $pg_desc2.children().not( $nextDesc2 ).css('opacity', 0);
		
			$nextDesc2.show().animate({
			   'right':'200px',
			   'opacity':'1' //,
			   //'top': t+"px"
		   },animSpeed);
		});
		}
		e.preventDefault();
	});

	//resize window event:
	//the scroller container needs to update
	//its height based on the new windows height
	$(window).resize(function() {
		var w_h			= $(window).height();
		$thumbScroller.css('height',w_h);
		$('#thumbScroller.scroll').css('height',w_h-40);
		window.deviceAgent = navigator.userAgent.toLowerCase();
		window.agentID = deviceAgent.match(/(iphone|ipod|ipad)/);
		if(!agentID){
		}
		else{
			$scrollerContainer.css('height',w_h);
		}
		sliderHeight	= w_h;
	});
	
	//create the scrollable container
	//taken from Manos :
	//http://manos.malihu.gr/jquery-thumbnail-scroller
	
	$('#thumbContainter').bind('mousemove',function(e){ 
				//console.log(("e.pageY: " + e.pageY - $('#thumbContainter').offset().top)); 
	});
	function buildScrollableItems(){
		totalContent = (scrollerContentCnt-1)*itemHeight;
		//alert( totalContent );
		
		$thumbScroller.css('height',sliderHeight)
		.mousemove(function(e) {
			
		   if (!allow_slider_mouseover)
		      return;
			if($scrollerContainer.height()>sliderHeight){
				var mouseCoords		= (e.pageY - $thumbScroller.offset().top - 44 + 23);
				var mousePercentY	= mouseCoords/sliderHeight;
				
				mousePercentY = (e.pageY - $thumbScroller.offset().top - 23) / ( $(window).height() - 23 - 44 );
				
			   //console.log( (e.pageY - 23) + " / " + ( $(window).height() - 23 - 44 ) );
			   //console.log( mousePercentY );
				
			   var h = ((totalContent-(sliderHeight-itemHeight))-sliderHeight);
			   h = totalContent - ($(window).height() - 23 - 44);
			   //if (!$.browser.msie)
			   
			      h += itemHeight;
			      
			   //alert(h);
			   //alert(totalContent);
			   //mousePercentY = 1;
			   //console.log( $("#thumbScroller .container").height() );
			   //h = $("#thumbScroller .container").height() + 20;
				var destY			= -1*h*(mousePercentY);
				var thePosA			= -destY;
				var thePosB			= destY-mouseCoords;
				if(mouseCoords==destY)
					$scrollerContainer.stop();
				else if(mouseCoords>destY)
					$scrollerContainer.stop()
				.animate({
					top: -thePosA
				},
				animSpeed,
				easeType);
				else if(mouseCoords<destY)
					$scrollerContainer.stop()
				.animate({
					top: thePosB
				},
				animSpeed,
				easeType);
			}
		}).find('.thumb')
		.fadeTo(fadeSpeed, 0.6)
		.hover(
		function(){ //mouse over
			$(this).fadeTo(fadeSpeed, 1);
		},
		function(){ //mouse out
			$(this).fadeTo(fadeSpeed, 0.6);
		}
	);
	}
	
});


/* Slider thumbstrip, controls and autoplay */
var homeSlider = {
   animSpeed: 600,
   interval: 5000,
   autostart: 0 // autoplay on
};

var sh_tout = false;
var sh_interval = homeSlider.interval + homeSlider.animSpeed;
var allow_slider_mouseover = true;

function stop_slideshow()
{
   if (sh_tout)
      clearTimeout(sh_tout);
}

function go_next_slide(p, new_slide, it_is_manual) {
   stop_slideshow();
   var nextSlide = current_big_image;
   
   if (it_is_manual)
   {
      nextSlide = new_slide;
   }
   else
   {
   
      nextSlide += (p > 0 ? -1 : 1);
      
      if (nextSlide > $("#thumbScroller .content").length-1)
         nextSlide = 0;
      if (nextSlide == -1)
         nextSlide = $("#thumbScroller .content").length-1; 
         
   }
   
   /*
   var e = new jQuery.Event("mousemove", {
      pageY: ( $(window).height()/2 )
   });
   $('#thumbScroller').trigger(e);
   */
   
	var h_one = 96;
	var ct = nextSlide*h_one + h_one/2 + 10;
	ct -= $(window).height() / 2;
	
	var e = new jQuery.Event("click");
	e.manual = true;
	
	e.no_anim_arrow = 0;
	
	if (ct < 0)
	{
	   ct = 0;
	   //e.no_anim_arrow = 1;
	}
	
	var max_h = $("#thumbScroller .container").height() - $(window).height() + 23;
	
	//console.log(ct +"; "+ max_h);
	
	if (ct > max_h)
	{
	   ct = max_h;
	   //e.no_anim_arrow = 1;
	}
	
	ct *= -1;
	
	var init_mt = parseFloat( $(".marker").css('top') );
	var new_mt = nextSlide*h_one + h_one/2 + 10;
	
	var old_ct = parseFloat( $("#thumbScroller .container").css('top') );
	
	var nochange = 0;
	
	if ( Math.abs( old_ct - ct ) < 1.5 )
	{
	   e.no_anim_arrow = 1;
	   nochange = 1;
	}
	
   allow_slider_mouseover = false;
   
   if (it_is_manual)
   {
      nochange = 1;
   }
	
	if ( ( $("#thumbScroller .container").height() < $(window).height() - 23 -44 ) || nochange )
	{
	   $(".marker").animate({
	      'top': new_mt+"px"
	   }, {
	      duration: homeSlider.animSpeed,
	      complete: function () {

   	      allow_slider_mouseover = true;
	      }
	   });
	}
	else
	{
      $("#thumbScroller .container").animate({
         top: ct+"px"
      }, {
         duration: homeSlider.animSpeed,
         queue: false,
         complete: function () {
            allow_slider_mouseover = true;
         },
         step: function (now, fx) {
            if (!e.no_anim_arrow || it_is_manual)
            {
               //console.log(now, fx);
               var mt = 0;
               mt += init_mt;
               var delta = (fx.start - now);
               var delta_all = (fx.start - fx.end);
               var delta_full = (new_mt - init_mt);
               
               var eps = Math.abs(Math.abs(delta_all) - 96.5);
               
               //console.log("eps="+eps+"; delta="+delta+"; delta_all="+delta_all+"; delta_full="+delta_full);
               
               if ( eps <= 1 )
               {
                  delta_full = delta;
               }
               else
               {
                  delta_full *= delta/delta_all;
               }
                  
               mt += delta_full;
               //mt += delta;
               //console.log(delta_full);
               $(".marker").css('top', mt+"px");
            }
         }
      });
   }
   
   if (!it_is_manual)
      $("#thumbScroller .content:eq("+nextSlide+")").trigger(e);
   
   if ( $(".navig a:eq(1)").hasClass('stop') && !it_is_manual )
   {
      start_slideshow();
   }
}

function start_slideshow()
{
   sh_interval = homeSlider.interval + homeSlider.animSpeed;
/*   var time = new Date();
   console.log(':'+time.getSeconds()+' --> '+sh_interval);
*/   if (sh_tout)
      clearTimeout(sh_tout);
   sh_tout = setTimeout(go_next_slide, sh_interval);
   
}


$(function () {

   if ( !$(".container").length ) 
      return;


	  
   if ( $(".content").length < 2 )
   {
	  
      $(".navig, #thumbContainter").hide();
      $("#pg_desc2 div, #pg_desc1 div").css({
         right: '20px'
      });
      return;
   }

   $(".navig a:eq(1)").click(function () {
      if ( $(this).hasClass("play") )
      {
         $(this).removeClass("play").addClass("stop");
         start_slideshow();
      }
      else
      {
         $(this).removeClass("stop").addClass("play");
         stop_slideshow();
      }
      return false;
   });

   $(".content:first").click();

   if (homeSlider.autostart)
      $(".navig a:eq(1)").trigger("click");
   
   var can_click = 1;
   $(".navig a:eq(0), .navig a:eq(2)").click(function () {
      if (!can_click)
         return false;
      can_click = 0;
      go_next_slide( $(this).index() == 0 ? 1 : 0 );
      setTimeout(function () {
         can_click = 1;
      }, homeSlider.animSpeed+100);
      return false;
   });
   
   $("#thumbScroller").hover(function () {
      if ( $(".stop").length )
         stop_slideshow();
   }, function () {
      if ( $(".stop").length )
         start_slideshow();
   });
   
  /* if ( $("#thumbScroller .container").length )
      $("body").bind('mousewheel', function () {
         return false;
      });*/
	  
	 if(ResizeTurnOff){}
	 else{	
		$(document).wipetouch({
			preventDefault: false,
			wipeLeft: function(result) {
				$(".navig a:eq(2)").trigger('click', true);
				hideHeader();
			},
			wipeRight: function(result) {
				 $(".navig a:eq(0)").trigger('click');
				hideHeader();
			},
			wipeUp: function(result) {
				hideHeader();
			},
			wipeDown: function(result) { 
				showHeader();
			}
		});
	 }
	
	window.deviceAgent = navigator.userAgent.toLowerCase();
	window.agentID = deviceAgent.match(/(iphone|ipod|ipad)/);
	if(agentID){
	$('#thumbScroller').addClass('scroll');
		
		jQuery.fn.oneFingerScroll = function() {
			var scrollStartPos = 0;
			$(this).bind('touchstart', function(event) {				
				// jQuery clones events, but only with a limited number of properties for perf reasons. Need the original event to get 'touches'
				var e = event.originalEvent;
				scrollStartPos = $(this).scrollTop() + e.touches[0].pageY;
				//e.preventDefault();
			});
		
			$(this).bind('touchmove', function(event) {
				
				var e = event.originalEvent;
				$(this).scrollTop(scrollStartPos - e.touches[0].pageY );
				e.preventDefault();
			});
			return this;
		};
		$('#thumbScroller').oneFingerScroll();
	}
});


/*
Cufon.CSS.ready(function() {
   var d = $("body:not(.home-static) #pg_desc1 > div:eq(0)");
   var right_t =  650 - d.outerWidth();
   d.css('right', right_t+"px");
   
   var hd = $("body.home-static #pg_desc1 > div:eq(0)");
   var hd_right_t =  262 - d.outerWidth();
   hd.css('right', hd_right_t+"px");
});
*/

/* Autoadvance */
$(window).load(function(){

	// Событие window.load гарантирует, что все изображения
	// будут загружены прежде, чем автопроигрывание начнет действовать.
	
	var timeOut = null;

	$('#pg_preview .thumbContainter').click(function(e,simulated){
		
		// Параметр simulated устанавливается методом trigger/
				
		if(!simulated){
			
			// Произошло реальное событие click. Сбрасываем таймер.
						
			clearTimeout(timeOut);
		}
	});

	// Самовыполняющаяся функция:
	
	(function autoAdvance(){
		
		// Имитация события click на кнопке "следующий".
		$('#pg_preview .thumb').trigger('click',[true]);
		
		// Устанавливаем таймер на 2 секунды.
		timeOut = setTimeout(autoAdvance,2000);		
	})();
});


