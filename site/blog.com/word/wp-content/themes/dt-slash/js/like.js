$(function () {
	var resizeTimeout = false;
	$(window).resize(function(){
		
		clearTimeout(resizeTimeout);
		resizeTimeout = setTimeout(function() {
		
		
		function getOsVersion() {
			var agent = window.navigator.userAgent,
				start = agent.indexOf( 'OS ' );
		
			if( ( agent.indexOf( 'iPhone' ) > -1) && start > -1 ){
				return window.Number( agent.substr( start + 3, 3 ).replace( '_', '.' ) );
			} else {
				return 0;
			};
		
		};
		 function isiPhone(){
			return (
				(navigator.platform.indexOf("iPhone") != -1) ||
				(navigator.platform.indexOf("iPod") != -1)
			);
		}
		if( getOsVersion() < 5 && isiPhone()){
		   $(".close").click(function () {
	  
	   
		  if (parseInt( $(".customize-l").css('left') ) == $(window).width() - 284)
		  {
			 $(".customize-l").addClass('act').animate({
				 
				left:$(window).width() - 52,
				right: "auto"
			 }, {
				duration: 500,
				complete: function () {
				}
			 });
		  }
		  else
		  {
			 $(".customize-l").removeClass('act').animate({
				left:$(window).width() - 284,
				right:'auto'
			 }, {
				duration: 500,
				complete: function () {
				}
			 });
		  }
		  return false;
	   });
		if($('.customize-l').hasClass('act')){
			$('.customize-l').css({
				left:$(window).width() - 52,
				right:'auto'
			})
		}
		else{
			$('.customize-l').css({
				left:$(window).width() - 284,
				right:'auto'
			})
		}
		$("#new_buttons").wipetouch({
			preventDefault: false,
			wipeLeft: function(result) {
				$(".close").trigger("click");
			},
			wipeRight: function(result) {
				$(".close").trigger("click");
			}
		});

	}
	else{
		$(".close").click(function () {	  
	   
		  if (parseInt( $(".customize-l").css('right') ) == 0 )
		  {
			 $(".customize-l").addClass('act').animate({
				 
				right: -243
			 }, {
				duration: 500,
				complete: function () {
				}
			 });
		  }
		  else
		  {
			 $(".customize-l").removeClass('act').animate({
				right:0
			 }, {
				duration: 500,
				complete: function () {
				}
			 });
		  }
		  return false;
	   });
		if($('.customize-l').hasClass('act')){
			$('.customize-l').css({
				right:-243
			})
		}
		else{
			$('.customize-l').css({
				right:0
			})
		}

		$("#new_buttons").wipetouch({
			preventDefault: false,
			wipeLeft: function(result) {
				$(".close").trigger("click");
			},
			wipeRight: function(result) {
				$(".close").trigger("click");
			}
		});

	}
	if( '#like' == window.location.hash && parseInt( $(".customize-l").css('right') ) != 0 ) $(".close").click();
		}, 200)
	}).trigger('resize')
	
});