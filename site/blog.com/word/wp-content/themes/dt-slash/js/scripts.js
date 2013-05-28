$(function() {
	$(".gallery-box").css('overflow','visible');
});
/*************************************************************/
function dt_clear_contact_fields() {
    $('#form_name', $('#order_form')).val('');
    $('#form_email', $('#order_form')).val('');
    $('#form_phone', $('#order_form')).val('');
    $('#form_message', $('#order_form')).val('');
}

/*************************************************************/
// menu
var menu_timeout_open = false;
var menu_timeout_close = false;

$(function () {

   var menu_speed_show = 300;
   var menu_show_timeout = 300;

   $("#nav li").each(function () {
      var sub_ul = $(this).children("div");
      
      if (!sub_ul.length)
      {
         $(this).hover(function () {
            if (menu_timeout_open)
               clearTimeout(menu_timeout_open);
         },
         function () {
         });
         return;
      }
      
      prev_left = 190;
      
      var new_left = parseInt( prev_left ) - 10;
      var init_left = new_left+20;

      if ($.browser.msie && $.browser.version < 9)
      {
         sub_ul.css({
            display: 'none'
         });
      }
      else
      {
         sub_ul.css({
            display: 'none',
            opacity: 0
         });
      }
      
      $(this).hover(function () {
         if (menu_timeout_open)
            clearTimeout(menu_timeout_open);
         
         menu_timeout_open = setTimeout(function () {
            sub_ul.find("div").hide();
            if ($.browser.msie && $.browser.version < 9)
            {
				sub_ul.css('left',new_left).show();
            }
            else
            {
               sub_ul.css({
                  display: 'block',
                  opacity: 0,
                  left: init_left
               }).animate({
                  opacity: 1,
                  left: new_left
               }, {
                  duration: menu_speed_show,
                  queue: false,
                  complete: function () {
                     if ($.browser.msie) this.style.removeAttribute('filter');
                  }
               });
            }
         }, menu_show_timeout);
      },
      function () {
         sub_ul.hide();
      });
   });
   
   $("#nav").hover(function () { },function () {
      //$("#nav div").hide();
      if (menu_timeout_open)
         clearTimeout(menu_timeout_open);
   });
   
   $("#nav div").each(function () {
      var tout_hide = false;
      var d = $(this);
      d.hover(function () {
         if (tout_hide)
            clearTimeout(tout_hide);
      },
      function () {
         tout_hide = setTimeout(function () {
            d.hide();
         }, 500);
      });
   });

});
// end menu

/*************************************************************/

// flickr animations
$(function () {
   $(".flickr").parent().hover(function () {
   },
   function () {
      $(".flickr i").animate({
         opacity: 0
      }, {
         duration: 300,
         queue: false
      });
   });
   $(".flickr i").hover(function () {
      $(this).animate({
         opacity: 0

      }, {
         duration: 300,
         queue: false
      });      
      $(".flickr i").not( $(this) ).animate({
         opacity: 0.4
      }, {
         duration: 300,
         queue: false
      });
   }, function () {
      
   });
});
// end flickr animations

/****************************************************************************************************************/
// go up arrow
$(function () {
   $(".go_up").click(function () {
      $("html:not(:animated)"+( ! $.browser.opera ? ",body:not(:animated)" : "")).animate({scrollTop: 0}, 500);
      return false;
   });
});
// end go up arrow

/****************************************************************************************************************/
// form validation
function update_form_validation() {
   $("[placeholder]").each(function () {
      $(this).val( $(this).val().replace( $(this).attr("placeholder"), '' ) );
      $(this).unbind().placeholder();
   });
   $("form .go_button, form .do_add_comment").unbind().click(function () {
   
      $(this).parents("form").find("input, textarea").each(function () {
         $(this).val( $(this).val().replace( $(this).attr("placeholder"), '' ) ).unbind().placeholder();
      });
      $(".formError").remove();
      
      var e=$(this).parents("form");
      e.find("input, textarea").each(function () {
         $(this).unbind();
         $(this).val( $(this).val().replace( $(this).attr("placeholder"), "" ) );
      });
      if (!e.attr("valed"))
      {
         if ( e.hasClass("ajaxing") )
         {
            e.validationEngine({
               ajaxSubmit: true,
               ajaxSubmitFile: e.attr("action")
            });
            
         }
         else
         {
            e.validationEngine();
         }
      }
      e.attr("valed", "1");
      e.submit(); 
      e.find("input, textarea").each(function () {
         $(this).placeholder();
      });      
      return false;
   });
   $("form .do_clear").unbind().click(function (e) {
        var msg_area = $(this).parents('.uniform').parent().find('div.ajaxSubmit');
        if( msg_area.length ) {
            msg_area.hide('slow');
        }
        
        $(this).parents("form").find("input, textarea").not('input[type="hidden"]').each(function () {
            $(this).val("").unbind().placeholder();
        });
      
        $(".formError").remove();
      
        if ($(this).attr("remove") && !$(this).parents("#form_prev_holder").length) 
        {
            move_form_to( $("#form_prev_holder") );
            $("#form_holder .do_clear").removeAttr('remove');
        }
      
        return false;
    });
}
$(update_form_validation);
//update_form_validation();
// end form validation

/****************************************************************************************************************/
// comments form
function move_form_to(ee)
{
      var e = $("#form_holder").html();
      var tt = $("#form_holder .header").text();
      
      var sb =$("#form_holder .go_button").attr("title");
      
      var to_slide_up = ($(".comment_bg #form_holder").length ? $("#form_holder") : $(".share_com"));
      
      to_slide_up.slideUp(500, function () {
         $("#form_holder").remove();
         
         ee.append('<div id="form_holder">'+e+'</div>');
		 
         $("#form_holder .header").html(tt);
         $("#form_holder [valed]").removeAttr('valed');
         $("#form_holder .do_clear").attr('remove', 1);
         
         $("#form_holder .go_button cufon").remove();
         $("#form_holder .go_button span span :not(i)").remove();
         $("#form_holder .go_button span i").after( sb );
         
         //alert(sb);
         
         Cufon('#form_holder .header', {
            color: '-linear-gradient(#0a0a0a, #444444)'
         });
         
         Cufon('a.button.big', {
           color: '-linear-gradient(#c4c4c4, 0.4=#f9f9f9, #f9f9f9)',textShadow: '1px 1px #000'
         });
         
         $(".formError").remove();
         
         $("#form_holder").hide();
         
         to_slide_up = ($(".comment_bg #form_holder").length ? $("#form_holder") : $(".share_com"));
         if (to_slide_up.hasClass('share_com')) $("#form_holder").show();
         
         to_slide_up.slideDown(500);
         
         if (ee.attr("id") != "form_prev_holder")
         {
            var eid = ee.parent().attr("id");
            if (!eid)
               eid = "";
            $("#comment_parent").val( eid.replace('comment-', '') );
         }
         else
         {
            $("#comment_parent").val("0");
         }
         
         update_form_validation();
      });
}
$(function () {
   $(".comment .comments").click(function () {
      move_form_to( $(this).parent().parent().parent() );
      return false;
   });
});
// end comments form

/****************************************************************************************************************/
// form-search
$(function () {
	$('.int').focus(function () {
		$(this).parent().addClass('i-f');}).blur(function () {
			$(this).parent().removeClass('i-f')
		});
});
//widget find last element
$(function() {
	$('.widget .post:last-child').addClass('last');
});
$(function() {
	$('.widget ul li:last-child').addClass('last');
});
$(function() {
	$('.article > .item-gal:first').addClass('first');
});
$(function() {
	$('.menu > li:last').addClass('last');
});
//video player
$('#JPlayer').find('display').css('zIndex', '999');


/****************************************************************************************************************/

/* Define iOS devices */
jQuery.extend(jQuery.browser,
	{SafariMobile : navigator.userAgent.toLowerCase().match(/iP(hone|ad)/i) }
);

// form-search
$(function () {
	$('.int').focus(function () {
		$(this).parent().addClass('i-f');}).blur(function () {
			$(this).parent().removeClass('i-f')
		});
	}); 

/****************************************************************************************************************/

/* Hide hampage overlay mast for iOS device */
$(function() {
	if ($.browser.SafariMobile){
		$("#big-mask").css("display", "none");
	}
});

/****************************************************************************************************************/
//footer
$(function () {
	///
	$(window).resize(function () {
		h = $(window).height() - $("#top_bg").height() - $("#content").height();
		//$("#content").css('min-height', h+"px");
		
	}).trigger("resize");
});

/****************************************************************************************************************/
/* Create masonry layout with Isotope (http://isotope.metafizzy.co/) */

// Masonry for regular POSTS

$(function(){
	var resizeTimeout = false;
	$(window).resize(function(){
		
		clearTimeout(resizeTimeout);
		resizeTimeout = setTimeout(function() {
			var $multicol = $('#multicol:not(.portfolio_massonry)');
			if (!$multicol.length)
				return;
			$multicol.addClass('isotoped'); 
			
			Cufon.CSS.ready(function() {
				if ($(window).width() < 1024) {
					$multicol.isotope({
					itemSelector : '#multicol .article_box, #multicol .gallery-box',
					transformsEnabled: false,
					animationEngine: 'css',
					masonry : {
						columnWidth : 280
					}
				});
				
				}
				else{
					$multicol.isotope({
						itemSelector : '#multicol .article_box, #multicol .gallery-box',
						transformsEnabled: false,
						animationEngine: 'jquery',
						masonry : {
							columnWidth : 280
						},
						animationOptions: {
							duration: 750,
							easing: 'linear',
							queue: false
						}
					});
				}
			});
		}, 200);
	}).trigger("resize");
});

/****************************************************************************************************************/

// Masonry for PHOTOS (FLAT GALLERY) and ALBUMS (2-LEVELS GALLERY)
$(function(){
	var resizeTimeout = false;
	$(window).resize(function(){
		
		clearTimeout(resizeTimeout);
		resizeTimeout = setTimeout(function() {
		
			var $multicol = $('#multicol-gal');
			if (!$multicol.length)
				return;
			$multicol.addClass('isotoped');
			
			Cufon.CSS.ready(function() {
				if ($(window).width() < 1024) {
					$multicol.isotope({
					itemSelector : '#multicol-gal .gallery-box',
					transformsEnabled: false,
					animationEngine: 'css',
					masonry : {
						columnWidth : 280
					}
				});
				
				}
				else{
					$multicol.isotope({
						itemSelector : '#multicol-gal .gallery-box',
						transformsEnabled: false,
						animationEngine: 'jquery',
						masonry : {
							columnWidth : 280
						},
						animationOptions: {
							duration: 750,
							easing: 'linear',
							queue: false
						}
					});
				}
			});
		}, 200);
	}).trigger("resize");
});

/****************************************************************************************************************/
/* Swap featured image by vector objects os slashed shape */
$(document).ready(function() {
	
	// Swap featured images with vector objects
	$(".img-holder").each(function () {
		//$(this).removeClass('ro');
		var img = $(this).find("img");
		var img_w = img.attr("width");
		var img_h = img.attr("height");
		var img_h_m = img_h - 30;
		var img_src = img.attr("src");
		img.css('display' , 'none');

		var paper = Raphael(this, img_w, img_h);
		// iOS devices can not apply path background image correctly. We are using clipping instead.
		if ($.browser.safari){
			var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
			var p_img = paper.image(img_src, 0, 0, img_w, img_h);
			p_img.attr({
				stroke: "none",
				"clip-rect": p
			});
		}
		else{
			var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
			c.attr({
				stroke: "none",
				fill: "url("+img_src+")"
			});
		}
	});

	// Swap HomePage thumbnails with vector objects
	$(".slide-h").each(function () {
		var img_w = $(this).find("img").attr("width");
		var img_h = $(this).find("img").attr("height");
		var img_h_m = img_h - 15;
		var img_src = $(this).find("img").attr("src");

		var paper = Raphael(this, img_w, img_h);

		if ($.browser.safari){
			var p = "M0,15L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,15";
			var img = paper.image(img_src, 0, 0, img_w, img_h);
			img.attr({
				stroke: "none",
				"clip-rect": p
			});
		}
		else{
			var c = paper.path("M 0 15   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 15");
			c.attr({
				stroke: "none",
				fill: "url("+img_src+")"
			});
		}
	});

});

/************************************************************************************************************************************************/

/* Hover holder for svg-objects */
$(function() {
	$('#multicol:not(.portfolio_massonry) .img-holder, #multicol-gal .img-holder').append('<div class="i-am-overlay"></div>').each(function () {
		var $span = $('> div.i-am-overlay:not(.highslide-maincontent)', this);
		if ($.browser.msie && $.browser.version < 9)
			$span.hide();
		else 
			$span.css('opacity', 0);
		$(this).hover(function () {
			if ($.browser.msie && $.browser.version < 9)
				$span.show();
			else
				$span.stop().fadeTo(500, 1);
		}, function () {
			if ($.browser.msie && $.browser.version < 9)
				$span.hide();
			else
				$span.stop().fadeTo(300, 0);
		});
	});
});

/****************************************************************************************************************/
/* Hoovers for svg-objects */
$(function() {
	// Add rollovers to gallery-posts thumbnails
   var slideshow_group_counter = 100;
	$("#multicol:not(.portfolio_massonry) .img-holder:not(.n-s) div.i-am-overlay").append('<a href="#" class="zoom-gal"></a><a href="#" class="detal"></a>').each(function () {
		$('a.detal', this).attr('href', $(this).parent('div').find('a').attr('href'));

		elems = $(this).parents(".article").find(".gal_in_posts a");

		var group = 'group'+slideshow_group_counter;
		var tid = 'for_'+group;
		elems.eq(0).attr("id", tid);
		slideshow_group_counter++;

		var slideshow_options_bak = {};
		$.extend(slideshow_options_bak, gallery_slideshow);
		gallery_slideshow.slideshowGroup = group;
		hs.addSlideshow(slideshow_options_bak);

		$('a.zoom-gal', this).attr('href', 'javascript:;').attr('onclick', 'document.getElementById(\''+tid+'\').onclick()');

		elems.each(function () {
			$(this).addClass("hs_attached");
			if (!$(this).attr("href"))
				return;
			this.onclick = function () {
				gallery_group.slideshowGroup = group;
				gallery_group.thumbnailId = tid;
				return hs.expand(this, gallery_group);
			};
		});

		var item_info = $(this).parents(".article").find(".photo-info");
		item_info.css('display','none');

		var info_box = $(this).find("a.detal");
		info_box.hover(function() {
			item_info.stop().fadeTo(400,1);
		}, function() {
			item_info.stop().fadeTo(200,0, function() { item_info.hide() });
		});

		var img_w = $(this).parent("div").width();
		var img_h = $(this).parent("div").height() + 2;
		var img_h_m = img_h - 30;

		var paper = Raphael(this, img_w, img_h);
		
		if ($.browser.safari){
			var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
			var p_img = paper.image(gal_h, 0, 0, img_w, img_h);
			p_img.attr({
				stroke: "none",
				"clip-rect": p
			});
		}
		else{
			var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
			c.attr({
				stroke: "none",
				fill: "url("+gal_h+")"
			});
		}
	});

	// Add rollovers to regular-posts thumbnails 
	$("#multicol:not(.portfolio_massonry) .img-holder.n-s div.i-am-overlay").append('<a class="zoom" onclick="return hs.expand(this)"></a><a class="detal"></a>').each(function () {
		$('a.detal', this).attr('href', $(this).parent('div').find('a').attr('href')); 
		$('a.zoom', this).attr('href', $(this).parent('div').find('a').attr('data-img'));

		var img_w = $(this).parent("div").width()+18;
		if ($.browser.msie)
			var img_h = $(this).parent("div").height();
		else
			var img_h = $(this).parent("div").height()+2;
		var img_h_m = img_h - 30;
		var paper = Raphael(this, img_w, img_h);
		
		if ($.browser.safari){
			var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
			var p_img = paper.image(gal_h, 0, 0, img_w, img_h);
			p_img.attr({
				stroke: "none",
				"clip-rect": p
			});
		}
		else{
			var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
			c.attr({
				stroke: "none",
				fill: "url("+gal_h+")"
			});
		}
	});
	
	$("#multicol-gal.two_level_gal .img-holder div.i-am-overlay").append('<a href="#" class="zoom-gal"></a><a class="detal"></a>').each(function () {
 
		var item_info = $(this).parents(".article").find(".photo-info");
		item_info.css('display','none');

		var info_box = $(this).find("a.detal");
		info_box.hover(function() {
			item_info.stop().fadeTo(400,1);
		}, function() {
			item_info.stop().fadeTo(200,0, function() { item_info.hide() });
		});

		var img_w = $(this).parent("div").width();
		var img_h = $(this).parent("div").height() + 2;
		var img_h_m = img_h - 30;

		var paper = Raphael(this, img_w, img_h);
		
		if ($.browser.safari){
			var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
			var p_img = paper.image(gal_h, 0, 0, img_w, img_h);
			p_img.attr({
				stroke: "none",
				"clip-rect": p
			});
		}
		else{
			var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
			c.attr({
				stroke: "none",
				fill: "url("+gal_h+")"
			});
		}
	});
	
	$("#multicol-gal.one_level_gal .img-holder div.i-am-overlay").append('<span href="#" class="zoom-gal"></span>').each(function () {
 
		var img_w = $(this).parent("div").width();
		var img_h = $(this).parent("div").height() + 2;
		var img_h_m = img_h - 30;

		var paper = Raphael(this, img_w, img_h);
		
		if ($.browser.safari){
			var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
			var p_img = paper.image(gal_h, 0, 0, img_w, img_h);
			p_img.attr({
				stroke: "none",
				"clip-rect": p
			});
		}
		else{
			var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
			c.attr({
				stroke: "none",
				fill: "url("+gal_h+")"
			});
		}
	});
	
});


/*****************************************************************************************************************************************************/
//hover effect
$(function() {
	$('.fadeThis, a.alignleft:not(.not), a.alignright, a.aligncenter, a.alignnone, .button-h, .gall_std li a').append('<span class="hover"></span>').each(function () {
	  var $span = $('> span.hover', this);
     if ($.browser.msie && $.browser.version < 9)
        $span.hide();
     else
        $span.css('opacity', 0);
	  $(this).hover(function () {
	    if ($.browser.msie && $.browser.version < 9)
	      $span.show();
	    else
   	    $span.stop().fadeTo(500, 1);
	  }, function () {
	    if ($.browser.msie && $.browser.version < 9)
	      $span.hide();
	    else
  	      $span.stop().fadeTo(500, 0);
	  });
	});
	$('.gall_std li a').each(function(){
		var im = $(this).find('img');
		var im_h = im.height();
		var im_w = im.width();
		$('span.hover', this).css({
			height:im_h,
			width:im_w
		})
	})
});


/******************************************************************************************************************************/
/* Service tooltips: tags, categories, social links, etc. */
$(function() {
	$('.ico-l').each(function () {
		var $tip = $('> .info-block', this);
		var $span = $('> span', this);
		if (!$span.length) {
			var $span = $('> a', this);
		};

		var $old_html = $tip.html();
		var $new_html = '<div class="box-i-l"><div class="box-i-r"><span class="box-i">' + $tip.html() + '</span></div></div>';
		$(this).hover(function () {
			$span.addClass('act');
			$offset = $span.offset();
			$tip.html($new_html);

			if ($.browser.msie && $.browser.version < 9) {
				$tip.show();
			} else {
				$tip.css('display', 'none');
				$tip.stop().fadeTo(300, 1);}
		}, function () {
			$span.removeClass('act');
			if ($.browser.msie && $.browser.version < 9) {
				$tip.hide();
				$tip.html($old_html);
			} else {
				$tip.css('display', 'block');
				$tip.stop().fadeTo(50, 0, function() {$tip.css('display', 'none'); $tip.html($old_html);});
			}
		});
	});
});

/****************************************************************************************************************/

/* New gallery */
$(function () {

   // normal hs
   $(".fadeThis").each(function () {
      if ( $(this).attr("href") == '#' )
         $(this).attr("href", $(this).find("img").attr("src") );
      this.onclick = function () {
         hs.expand(this, hs_config2);
         return false;
      };
   });

   // gallery 1 level 
   $(".one_level_gal").each(function () {
      els = $(".img-holder", this);
      els.each(function () {
         var config = {};
         $.extend(config, hs_config1);
         var im = $(this).children("img");
         var s = im.attr("src");
         //config.src = s;
         config.slideshowGroup = slideshow_options.slideshowGroup;
         var mini = $(this).children("a");
         if ( mini.length )
         {
            //config.src = mini.attr("href");
         }
         else
         {
            mini = $("<a />");
            mini.attr("href", s);
            mini.appendTo( $(this) );
         }
         
         mini.append( im.clone() );
         
         mini[0].onclick = function () {      
            return hs.expand(this, config);
         };
         
         $(this).click(function () {
            mini.trigger("onclick");
            return false;
         });
      });
   });
   
});

/*************************************************************************************************************************************************/
$(document).ready(function(){
	Cufon.CSS.ready(function() {
		var right_s = $('.static #pg_desc2 div, .video #pg_desc2 div').width() - $('.static #pg_desc1 div, .video #pg_desc1 div').width() + 20;
		var b = 70 +  $('.static #pg_desc2 div, .video #pg_desc2 div').height();
		
		$('.static #pg_desc1 div').css( {'right' : right_s , 'bottom' : b} );
		$('.static #pg_desc2 div').css( {'right' : 20, 'bottom' : 60 } );
		
		$('.video #pg_desc1 div').css( {'right' : right_s , 'bottom' : b} );
		$('.video #pg_desc2 div').css( {'right' : 20 , 'bottom' : 60} );
	});
}); 

/****************************************************************************************************************/

//jQuery.noConflict();
jQuery(document).ready(function($){
	
	/* Quick Isotope for Gallery */
	function do_isotope(iContainer, iItem, iWidth) {
		var $iContainer = $(iContainer);
		if (!iContainer.length)
			return;
		var resizeTimeout = false;
		$(window).resize(function(){
			
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(function() {
				Cufon.CSS.ready(function() {
					if ($(window).width() < 1024) {
						$iContainer.isotope({
							containerClass : 'isotoped',
							itemSelector : iItem,
							transformsEnabled: false,
							animationEngine: 'css',
							masonry : {
								columnWidth : iWidth
							}
						});
					}
					else{
						$iContainer.isotope({
							containerClass : 'isotoped',
							itemSelector : iItem,
							transformsEnabled: false,
							animationEngine: 'jquery',
							masonry : {
								columnWidth : iWidth
							},
							animationOptions: {
								duration: 750,
								easing: 'linear',
								queue: false
							}
						});
					}
				});
			},200)
		}).trigger('resize')
	}
	/*--------------------------------------------------*/

	/*  Slasher for Gallery  */
	function do_slash(iItem) {
		// Swap featured images with vector objects
		$(iItem).each(function () {
			var img = $(this).find("img");
			var img_w = img.attr("width");
			var img_h = img.attr("height");
			var img_h_m = img_h - 30;
			var img_src = img.attr("src");
			
			$(this).css('height',img_h);
			$(this).css('widtht',img_w);
			img.css('display' , 'none');
	
			var paper = Raphael(this, img_w, img_h);
			// iOS devices can not apply path background image correctly. We are using clipping instead.
			if ($.browser.safari){
				var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
				var p_img = paper.image(img_src, 0, 0, img_w, img_h);
				p_img.attr({
					stroke: "none",
					"clip-rect": p
				});
			}
			else{
				var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
				c.attr({
					stroke: "none",
					fill: "url("+img_src+")"
				});
			}
		});
	}
	/*--------------------------------------------------*/

	/* Overlay for gallery */
	function build_overlay (iItem) {
		// Add rollovers to gallery thumbnails
		$(iItem).each(function () { 
			var img_w = $(this).parent('div').width();
			if ($.browser.msie)
				var img_h = $(this).parent('div').height() + 1;
			else
				var img_h = $(this).parent('div').height() + 2;
			var img_h_m = img_h - 30;
	
			var paper = Raphael(this, img_w, img_h);
			if ($.browser.safari){
				var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
				var p_img = paper.image(gal_h, 0, 0, img_w, img_h);
				p_img.attr({
					stroke: "none",
					"clip-rect": p
				});
			}
			else{
				var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
				c.attr({
					stroke: "none",
					fill: "url("+gal_h+")"
				});
			}
		});
		$(iItem).append('<span class="zoom"></span>').each(function () {
			var $span = $(this);
			if ($.browser.msie && $.browser.version < 9)
				$span.hide();
			else
				$span.css('opacity', 0);
			$(this).parent('div').hover(function () {
				if ($.browser.msie && $.browser.version < 9)
					$span.show();
				else
					$span.stop().fadeTo(300, 1);
			}, function () {
				if ($.browser.msie && $.browser.version < 9)
					$span.hide();
				else
					$span.stop().fadeTo(300, 0);
			});
		});
	}
	/*--------------------------------------------------*/

	/* zGallery */
	var in_work = 0;
	$('.two_level_gal .img-holder .zoom-gal').click(function() {
		 
		if (in_work > 0){
			return false;
		}
			
		in_work = 1;
		
		var pb = $(this).parents(".gallery-box");
		var cl = pb.attr("class").replace(/^.*link_(gal[0-9]+).*$/, 'for_$1');
		var photos = $('.' + cl).clone();
	
		if (!photos.length){
			
			if( !pb.hasClass('dt-pass-protected') )
				alert('Error. Album "'+cl+'" is empty.');
			
			in_work = 0;
			return false;
		}
	
		var h2 = photos.children('strong').detach().html();
		var old_html = photos.html();
	
		photos.children("a").each(function() {
			var href = $(this).attr('href');
			var src = $(this).attr('data-src');
			var width = $(this).attr('data-width');
			var height = $(this).attr('data-height');
			var alt = $(this).attr('data-alt');
			var caption = $('<div></div>').append($(this).next('div.highslide-caption')).html();
			
			var new_html = '<div class="gallery-box"><div class="overlay"><a href="'+ href +'"><img src="'+ src +'" width="'+ width +'" height="'+ height +'" alt="'+ alt +'" /></a>'+ caption +'</div></div>';
			$(this).replaceWith(new_html);
		});
			
		var new_html = '<div class="holder"><a class="button" href="#"><span class="but-r"><i class="back"></i>BACK</span></a><h2 class="cufon_me">' + h2 + '</h2><div class="multicol-gallery">' + photos.html() + '</div><div class="but-bottom"><a class="button bot" href="#"><span class="but-r"><i class="back"></i>BACK</span></a></div></div>';
		var gallery = $('<div class="bg '+ cl +'" />');
		gallery.html(new_html).appendTo('body').addClass('hidden').addClass('gal-full');
		
		Cufon.refresh(); //undefined selector. this may slow down site.
		do_slash('.bg .gallery-box');
		build_overlay('.bg .gallery-box .overlay');
	
		var elems = gallery.find(".gallery-box");
	
		elems.each(function () {
			$(this).addClass("hs_attached");
			var link = $(this).find('a');
	
			link[0].onclick = function () {
				return hs.expand(this, slideshow_albums);
			};
	
			$(this).click(function () {
				$(link).trigger("onclick");
				return false;
			});
		});
	
		var prev_scroll_top = $(window).scrollTop();
		
		$('html:not(:animated)'+( ! $.browser.opera ? ',body:not(:animated)' : '')).animate({scrollTop: 0}, 500, function () {
	
			$(".holder", gallery).css({
				visibility: 'visible',
				opacity: 0,
				'z-index': 999
			});
	
			$('#bg').css({
				position: 'fixed',
				top: 0,
				width: '100%'
			});
	
			gallery.css({
				display: 'block',
				opacity: 0,
				visibility: 'visible'
			}).animate({
				opacity: 1
			}, {
				queue: false,
				duration: 1000,
				complete: function (){
	
					do_isotope('.bg .multicol-gallery' , '.bg .gallery-box', 260);
	
					$(".holder", gallery).animate({
						opacity: 1
					}, {
						queue: false,
						duration: 1000,
						complete: function () {
							$("a.button").unbind('click').click(function () {
								$("html:not(:animated)"+( ! $.browser.opera ? ",body:not(:animated)" : "")).animate({scrollTop: 0}, 500, '', function () {
									gallery.animate({
										opacity: 0
									}, {
										queue: false,
										duration: 500,
										complete: function () {
											$('.bg').remove();
											$('#bg').css({position: 'relative'});
											$("html:not(:animated)"+( ! $.browser.opera ? ",body:not(:animated)" : "")).animate({scrollTop: prev_scroll_top}, 500, '', function () {});										
										}
									});
	
								});
	
								in_work = 0;
								return false;
							});
						}
					});
				}
			});
		});
		return false;
	});

});
/****************************************************************************************************************/
function add_raphael_to_portfolio(){
	// Swap featured images with vector objects
	$(".img-holder.ro").each(function () {
		var img = $(this).find("img");
		var img_w = img.attr("width");
		var img_h = img.attr("height");
		var img_h_m = img_h - 30;
		var img_src = img.attr("src");
		img.css('display' , 'none');

		var paper = Raphael(this, img_w, img_h);
		// iOS devices can not apply path background image correctly. We are using clipping instead.
		if ($.browser.safari){
			var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
			var p_img = paper.image(img_src, 0, 0, img_w, img_h);
			p_img.attr({
				stroke: "none",
				"clip-rect": p
			});
		}
		else{
			var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
			c.attr({
				stroke: "none",
				fill: "url("+img_src+")"
			});
		}
	});
} 

/****************************************************************************************************************/
function dt_portfolio_ajax () {	
	// filters
	$(".filters").each(function () {
		var holder = $(this);
		var portfolio_in_work = 0;

		$(this).find(".filter").click(function (event) {			

			if (portfolio_in_work > 0){
				return false;
			}
			portfolio_in_work = 1;
			
			// reassign act class properly
			holder.find(".act").removeClass("act");
			$(this).addClass("act");

			// add cufon effects
			Cufon('a.button.big', {
				color: '-linear-gradient(#c4c4c4, 0.4=#f9f9f9, #f9f9f9)',textShadow: '1px 1px #000'
			});
			Cufon('a.button.big.act', {
				color: '-linear-gradient(#4d4d4d, #797979)',textShadow: '1px 1px #000'
			});	
            
            // get current category
			var cl = $(this).attr('href').slice(1);

			// isotope container
			var iso = $(".isotoped");
			
			if (!iso.length){
				alert("No posts to filter.");
				return false;
			}
		
			// formant filter
			//var f = '.item_'+cl;
			var f = '.'+cl;
			if ( cl == 'all' || 'none' == cl )
				f = '*';

			// engage isotope filter
			iso.isotope({ filter: f });

			// get ids of elements that still presents after filtering
			var class_str = new Array();
			$('.isotope-item', $(".isotoped")).not('.isotope-hidden').each(
				function(){
					class_str.push( $(this).attr('id') );
				}
			);
			
			// fire ajax
			jQuery.post(
				dt_ajax.ajaxurl,
				{
					// action function
					action : 'dt_ajax_portfolio_filter',
					// category slug for filter in query
					post_ctagory_slug: cl,
					// ids that wuld be excluded from result
					item_ids: class_str,
					
					tax_arr: dt_ajax.tax_kboom
					
				},
				function( response ){
					for(var i in response.spare) {
						if (!response.spare.hasOwnProperty(i)) continue;
						$(".isotoped").isotope( 'remove', $('#'+response.spare[i]) );
					}
					var html_cuf = $(response.html_content);
					// insert with isotope retrived elements
					Cufon.CSS.ready(function() {
					$(".isotoped").isotope( 'insert', 
						$(response.html_content),
						function(){
							$('.portfolio._m').html($(response.paginator));
						
							// remove needless objects
							$(".isotoped").isotope( 'remove', $('.isotope-hidden', $(".isotoped")) );
							
							$(".isotoped").isotope({sortBy : 'date',	sortAscending : false});
							
							// process images with raphael
							add_raphael_to_portfolio();
							
							// add cufon effect
						Cufon('h1, h2, h3, h4, h5, .article h4', {
							  color: '-linear-gradient(#444444, #0a0a0a)',
							  hover: {
								color: '-linear-gradient(#666666, #7c7c7c)'
							  }
							});
							
							// add cufon effects to pagination
							Cufon('.portfolio._m > .paginator > li > a', {
								color: '-linear-gradient(#c4c4c4, 0.4=#f9f9f9, #f9f9f9)', textShadow: '1px 1px #000'
							});
							Cufon('.portfolio._m > .paginator > li.act > a', {
								color: '-linear-gradient(#4d4d4d, #797979)', textShadow: '1px 1px #000'
							});
							
							// highslide
							//dt_hs_init_st();
							$(".isotoped").isotope( 'reLayout')
							// by Miroslav
							portfolio_overlay();
							remove_ro();
							portfolio_in_work = 0;
						});
					});
				}
			);
			return false;
		});
   });

/****************************************************************************************************************/
	// paginator event binding
	$('.portfolio._m').click( function(event){
		if ($(event.target).parents('li').is('li')){
			var cur_li = $(event.target).parents('li');
			var parent = $(event.target).parents('#nav-above');
            var filter = $('.filter.act');
            var slug = 'all';
            var paged = 1;
            
			// reassign act class properly
			parent.find('.act').removeClass('act');
			//cur_li.addClass('act');
            
            if( filter.length ) {
                slug = filter.attr('href').slice(1);
            }
            
            if( cur_li.length ) {
                paged = cur_li.attr('class').slice(5);
            }
            
			// fire ajax
			jQuery.post(
				dt_ajax.ajaxurl,
				{
					// action function
					action : 'dt_ajax_portfolio_filter',
					
					post_ctagory_slug: slug,
					
					dt_paged: paged,
					
					tax_arr: dt_ajax.tax_kboom
				},
				function( response ){
					// remove needless objects
					$(".isotoped").isotope( 'remove', $('.isotope-item') );
					
					// insert with isotope retrived elements
					Cufon.CSS.ready(function() {
					$(".isotoped").isotope( 'insert', 
						$(response.html_content),
						function(){
							$('.portfolio._m').html($(response.paginator));
							
							// process images with raphael
							add_raphael_to_portfolio();
						
							// add cufon effect
							Cufon('h1, h2, h3, h4, h5, .article h4', {
							  color: '-linear-gradient(#444444, #0a0a0a)',
							  hover: {
								color: '-linear-gradient(#666666, #7c7c7c)'
							  }
							});
							
							// add cufon effects to portfolio
							Cufon('.portfolio._m > .paginator > li > a', {
								color: '-linear-gradient(#c4c4c4, 0.4=#f9f9f9, #f9f9f9)', textShadow: '1px 1px #000'
							});
							Cufon('.portfolio._m > .paginator > li.act > a', {
								color: '-linear-gradient(#4d4d4d, #797979)', textShadow: '1px 1px #000'
							});
							
							// by Miroslav
							portfolio_overlay();
							remove_ro();
						});
					});
				}
			);
            
            cur_li.addClass('act');
            
			// go top
			$("html:not(:animated)"+( ! $.browser.opera ? ",body:not(:animated)" : "")).animate({scrollTop: 0}, 500);
		}
		return false;
	});

	// portfolio raphaelled images event binding
	$('#multicol').click( function(event){
		if ($(event.target).parent().is('svg')){
			return false;
		}
	});
}

/****************************************************************************************************************/
function dt_home_video_jplayer () {
}

function dt_isotope_sort_init () {
	var $multicol = $('#multicol.portfolio_massonry');
	if (!$multicol.length)
		return;
	$multicol.addClass('isotoped');
	var resizeTimeout = false;
	$(window).resize(function(){
		
		clearTimeout(resizeTimeout);
		resizeTimeout = setTimeout(function() {
			Cufon.CSS.ready(function() {
				
				if ($(window).width() < 1024) {
					$multicol.isotope({
						transformsEnabled: false,
						animationEngine: 'css',
						getSortData : {
							date : function ( $elem ) {
								var date = $elem.attr('class').toString().split( " " );
								date = date[(date.length - 1)];
								return parseInt( date );
							},
							id : function ( $elem ) {
								return parseInt( $elem.attr('id') );
							}
						}
					});
				
				}
				else{
					$multicol.isotope({
						transformsEnabled: false,
						animationEngine: 'jquery',
						getSortData : {
							date : function ( $elem ) {
								var date = $elem.attr('class').toString().split( " " );
								date = date[(date.length - 1)];
								return parseInt( date );
							},
							id : function ( $elem ) {
								return parseInt( $elem.attr('id') );
							}
						}
					});
				}
			});
		}, 200);
	}).trigger('resize')
}

/****************************************************************************************************************/
function dt_ajax_contact_send () {
	var data_arr = new Array();
	$('#order_form').find('input, textarea').each(function()
	{
		data_arr['"'+$(this).attr('name').toString()+'"'] = $(this).val().toString();
	});
	
	var form = $('#order_form');
	jQuery.post(
		dt_ajax.ajaxurl,
		{
			action : 'dt_ajax_contact_send',
/*			contact_form_nonce: $('#contact_form_nonce').val(),
			_wp_http_referer: $('#contact_form_nonce').next('input').val(),
*/			name: $('#form_name').val(),
			email: $('#form_email').val(),
			phone: $('#form_phone').val(),
			msg: $('#form_message').val(),
			id: dt_ajax.tax_kboom
			
		},
		function( response ){
			if ( response.success ) {
				return $('.highslide-maincontent').html('<h2>Your message was send!</h2>').hs.htmlExpand(this);
			}
		}
	);
}

/****************************************************************************************************************/
/* New scripts from Miroslav */
function portfolio_overlay() {

	$('#multicol.portfolio_massonry .img-holder.ro').append('<div class="i-am-overlay"></div>').each(function () {
		var $span = $('> div.i-am-overlay:not(.highslide-maincontent)', this);
		if ($.browser.msie && $.browser.version < 9)
			$span.hide();
		else 
			$span.css('opacity', 0);
		$(this).hover(function () {
			if ($.browser.msie && $.browser.version < 9)
				$span.show();
			else
				$span.stop().fadeTo(500, 1);
		}, function () {
			if ($.browser.msie && $.browser.version < 9)
				$span.hide();
			else
				$span.stop().fadeTo(300, 0);
		});
	});

	$("#multicol.portfolio_massonry .img-holder.ro.n-s:not(.type-video) div.i-am-overlay").each(function () {
		var parent = $(this).parents('.article_box');
		// for pass protection
		if( !parent.hasClass('dt-pass-protected') ) {
			$(this).append('<a class="zoom" onclick="return hs.expand(this, {slideshowGroup: \'' + parent.attr('id') + '\'})"></a>');
			$('a.zoom', this).attr('href', $(this).parent('div').find('a').attr('data-img'));
		}
		$(this).append('<a class="detal"></a>');
		$('a.detal', this).attr('href', $(this).parent('div').find('a').attr('href')); 
		
        
        // experemental
//        if( !$('a.zoom', this).attr('title') ) {
//            $('a.zoom', this).attr('title', $(this).parent('div').find('a').attr('title'));
//        }
        // experemental end
        
		var img_w = $(this).parent("div").width()+18;
		if ($.browser.msie)
			var img_h = $(this).parent("div").height();
		else
			var img_h = $(this).parent("div").height()+2;
		var img_h_m = img_h - 30;
		var paper = Raphael(this, img_w, img_h);
		
		if ($.browser.safari){
			var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
			var p_img = paper.image(gal_h, 0, 0, img_w, img_h);
			p_img.attr({
				stroke: "none",
				"clip-rect": p
			});
		}
		else{
			var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
			c.attr({
				stroke: "none",
				fill: "url("+gal_h+")"
			});
		}
	});
	
	$("#multicol.portfolio_massonry .img-holder.ro.n-s.type-video div.i-am-overlay:not(.highslide-maincontent)").append('<a class="detal"></a><a class="zoom" href="#"></a>').each(function () {
		$(this).parent().find('.highslide-maincontent').appendTo($(this));
		$('a.detal', this).attr('href', $(this).parent('div').find('a').attr('href')); 
		$('a.zoom', this).addClass('dt_highslide_video');
		
		$('.dt_highslide_video').click(
			function(){ 
				$(this).attr('data-width', $(this).next('.highslide-maincontent').attr('data-width'));
				return hs.htmlExpand(this, { width: $(this).attr('data-width') });
			}
		);

		var img_w = $(this).parent("div").width()+18;
		if ($.browser.msie)
			var img_h = $(this).parent("div").height();
		else
			var img_h = $(this).parent("div").height()+2;
		var img_h_m = img_h - 30;
		var paper = Raphael(this, img_w, img_h);
		
		if ($.browser.safari){
			var p = "M0,30L"+img_w+",0L"+img_w+","+img_h_m+"L0,"+img_h+"L0,30";
			var p_img = paper.image(gal_h, 0, 0, img_w, img_h);
			p_img.attr({
				stroke: "none",
				"clip-rect": p
			});
		}
		else{
			var c = paper.path("M 0 30   L "+img_w+" 0   L "+img_w+" "+img_h_m+"   L 0 "+img_h+"   L 0 30");
			c.attr({
				stroke: "none",
				fill: "url("+gal_h+")"
			});
		}
	});
}

/****************************************************************************************************************/
function remove_ro() {
	$(".img-holder.ro").removeClass('ro');
}

jQuery(document).ready(function($){
	dt_isotope_sort_init();
	dt_portfolio_ajax();
	dt_home_video_jplayer();
	portfolio_overlay();
	remove_ro();
	
		$('.item-gal .dt_highslide_video').click(
			function(){ 
				$(this).attr('data-width', $(this).next('.highslide-maincontent').attr('data-width'));
				return hs.htmlExpand(this, { width: $(this).attr('data-width') });
			}
		);
		$('.dt_highslide_image').click( 
			function() {
				hs.expand(this, hs_config2);
				return false;
			}
		);
		
	var regular_slideshow_group_counter = 200;
	$('.hsgallery .photo').each( function() {
			elems = $(this).next("div").find("a");
			
			var group = 'group'+regular_slideshow_group_counter;
			var tid = 'for_'+group;
			elems.eq(0).attr("id", tid);
			regular_slideshow_group_counter++;

			var slideshow_options_bak = {};
			$.extend(slideshow_options_bak, gallery_slideshow);
			gallery_slideshow.slideshowGroup = group;
			hs.addSlideshow(slideshow_options_bak);

			$(this).attr('href', 'javascript:;').attr('onclick', 'document.getElementById(\''+tid+'\').onclick()');

			elems.each(function () {
				$(this).addClass("hs_attached");
				if (!$(this).attr("href"))
					return;
				this.onclick = function () {
					gallery_group.slideshowGroup = group;
					gallery_group.thumbnailId = tid;
					return hs.expand(this, gallery_group);
				};
			});
		
	});
});

/****************************************************************************************************************/
$(window).resize(function () {
	  $(".video #JPlayer_wrapper").css({
		height: $(window).height()+"px",
		width: $(window).width()+"px"
	  });
	  $(".i-m-video #big-mask").css({
		"min-height": 0,
		height: $(window).height()-33+"px"
	  });
});

/****************************************************************************************************************/
$(function(){
	var f_h = ($('.customize-l').outerHeight()+80)/2;
	$('#show-l.fixed').css('marginTop', -f_h);

	$(window).resize(function(){ 
			$('#dt-mobile-menu select#nav-mob').css({
				marginTop:($('#dt-mobile-menu').height() - $('#dt-mobile-menu select#nav-mob').height())/2
			})
			var w_h = $(window).height();
			if ($("#bg").length > 0 && w_h > $("#bg").height()) {
				$("#bg").css({"min-height" : w_h +"px"})
			}
			if($(window).width() < 740 ) {
				$('.comment').each(function(){
					$('.head', this).css({
						width:$(this).width() - $('.shad_dark', this).width() -21 
					})
				})
				$(window).bind('orientationchange', function(event) {
					$('.comment').each(function(){
						$('.head', this).css({
							width:$(this).width() - $('.shad_dark', this).width() -21 
						})
					})
				})
			}
			else {
				$('.comment .head').css({
					width:100 + '%'
				})
			}
	}).trigger("resize");
});
/*$(function(){
	var w_h = $(window).width();
	$('.bot-home').css('width', w_h);
});*/

/****************************************************************************************************************/
//mobile menu
jQuery(function($){
	
/*window.ResizeTurnOff = true;*/
if(ResizeTurnOff){
	
	$('html').addClass('notResponsive');
}else{
	$('html').removeClass('notResponsive');
	
	$("#dt-mobile-menu option").each(function(){
		var $this	= $(this),
			text	= $this.text(),
			prefix	= "";
		switch ( parseInt($this.attr("data-level"))) {
			case 1:
				prefix = "";
			break;
			case 2:
				prefix = "— ";
			break;
			case 3:
				prefix = "—— ";
			break;
			case 4:
				prefix = "——— ";
			break;
			case 5:
				prefix = "———— ";
			break;
		}
		
		$this.text(prefix+text);

	});

	$("#dt-mobile-menu select").change(function() {
		window.location.href = $(this).val();
	});
	/*------------------------------------------------------------------------*/
	//mobile widgets
	if (moveWidgets === true) {
		
	 	var resizeTimeout = false;
		$(window).resize(function(){
			
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(function() {
				window.deviceAgent = navigator.userAgent.toLowerCase();
				window.agentID = deviceAgent.match(/(iphone|ipod|ipad)/);
				if(agentID){
					$('.go_up').addClass('ip');
				}
				else {
					$('.go_up').removeClass('ip');
				}
				var winW = $(window).width();
	
				if (winW < 980) {
					$('#widget-container').addClass('masonry');
					//$('#aside_c').wrapInner("<div id='widget-container' />");
					//alert(winW)
	
					$("html").removeClass("full-layout").addClass("mobile-layout");
					
					var $widgetsContainer	= $("#aside_c > #widget-container"),
						$widgets			= $("> .wrap-widget", $widgetsContainer),
						$contentArea		= $("#content");
		
					if ($widgets.length > 0 && $contentArea.length > 0) {
		
						$widgetsContainer.detach().appendTo($contentArea);
						$widgetsContainer.detach().appendTo($contentArea).insertAfter($('#nav-above.portfolio _m'));
						
							$('#widget-container.masonry').isotope({
							itemSelector : '.wrap-widget',
							transformsEnabled: false,
							animationEngine: 'css',
							masonry : {
								columnWidth : 280
							}
						});
					
						}
					
				} else {
					$('#widget-container').removeClass('masonry');
					//alert(winW)
					$("html").removeClass("mobile-layout").addClass("full-layout");
		
					var $widgetsContainer	= $("#content > #widget-container"),
						$widgets			= $("> .wrap-widget", $widgetsContainer);
		
					if ($widgets.length > 0) {				
						$widgetsContainer.detach().appendTo("#aside_c");
					}
				}
				
			}, 200);
		}).trigger("resize");
	}
	/*--------------------------------------------------------------------------------------------------------------*/
	//mobile misk
	$(window).bind('orientationchange', function(event) { 
		$(window).trigger("resize");
	});
	
	jQuery(window).bind("popstate", function() {
		jQuery(window).trigger('resize');
	});
	 if($('#bg').length){}
	 else{
		 window.hideHeader = function() {
			if ($(window).width() < 740) {
				$("#dt-mobile-menu").stop().animate({
					"top" : -$("#dt-mobile-menu").outerHeight() - 60
				}, 700);
			}
		}
		
		window.showHeader = function() {
			if ($(window).width() < 740) {
				$("#dt-mobile-menu").stop().animate({
					"top" : 0
				}, 700);
			}
		}
		
		$(document).on('touchmove', function(e) { if ($(window).width() < 1000) e.preventDefault(); });
		if ($.browser.SafariMobile){
	
			$(window).on("orientationchange",  function() {
			
				if(window.orientation == 90 || window.orientation == -90) {
					$("html, body, #holder, .pg_content, #pg_preview").not('.page-template-home-video-php').css({
						"min-height" : "315px"
					});
					
				} else {
					$("html, body, #holder, #pg_preview, .pg_content.video").not('.page-template-home-video-php').css({
						"min-height" : "490px"
					});			
				}
			
				setTimeout(scrollTo, 0, 0, 1);
				$(window).trigger("resize");
			
			}).trigger("orientationchange");
	
			setInterval( function() {$(window).trigger("orientationchange");}, 3000);	
		}
		//hide show header on iphone
		$(document).wipetouch({
			preventDefault: false,
			wipeLeft: function(result) {
				hideHeader();
			},
			wipeRight: function(result) {
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
}
	/*------------------------------------------------------------------------------------------------------------------------*/
	// add width height to logo
	jQuery('#logo img, #logo-mob img').each( function() {
		var img = new Image();
		var logo_img = jQuery(this);
		img.onload = function() {
			logo_img.attr('width', this.width);
			logo_img.attr('height', this.height);
		}
		img.src = logo_img.attr('src');
	});
	/*------------------------------------------------------------------------------------------------------------------------*/	
	// find hidden pass form
	var passForm = jQuery('#dt-gal-pass-form form.protected-post-form');
	if( passForm.length ) {
		// get pass input
		var passInput = jQuery('input[name="post_password"][type="password"]', passForm);
	}else {
		var passInput = new Array();
	}	
	/*------------------------------------------------------------------------------------------------------------------------*/
	// add onclick to protected hover
	jQuery('.gallery-box.dt-pass-protected .zoom-gal').on('click', function() {
		var pass = prompt('Enter password', '');
		if( pass && passInput.length ) {
			passInput.val(pass);
			passForm.submit();
		}
		return false;
	});
	$('#nav li').each(function(){
		if ($(this).find("div").length > 0) {
			$(this).addClass('children');
		}
		else{
			$(this).removeClass('children');
		}
	});
	window.isiPhone = function (){
		return (
			(navigator.platform.indexOf("iPhone") != -1) ||
			(navigator.platform.indexOf("iPod") != -1)
		);
	}
	window.deviceAgent = navigator.userAgent.toLowerCase();
	window.agentID = deviceAgent.match(/(iphone|ipod|ipad)/);
	window.ua = navigator.userAgent.toLowerCase();
	window.isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
	if(isAndroid || isiPhone() || agentID) {
		
		$("#big-mask").css("display", "none");
		var hasTouch = ("ontouchstart" in window);
		if (hasTouch && document.querySelectorAll) {
			var i, len, element,
				dropdowns = document.querySelectorAll("#nav li.children");
		 
			function menuTouch(event) {
				var i, len, noclick = !(this.dataNoclick);
				// reset flag on all links
				for (i = 0, len = dropdowns.length; i < len; ++i) {
					dropdowns[i].dataNoclick = false;
				}		 
				// set new flag value and focus on dropdown menu
				this.dataNoclick = noclick;
				this.focus();
			}		 
			function menuClick(event) {
				// if click isn't wanted, prevent it
				if (this.dataNoclick) {
					event.preventDefault();
				}
			}		 
			for (i = 0, len = dropdowns.length; i < len; ++i) {
				element = dropdowns[i];
				element.dataNoclick = false;
				element.addEventListener("touchstart", menuTouch, false);
				element.addEventListener("click", menuClick, false);
			}
		}
	}
});



/****************************************************************************************************************/
// submit pass form
function dt_submit_pass(_this) {
	jQuery(_this).parents('form.protected-post-form').submit();
	return false;
}

/****************************************************************************************************************/