/*Slidercat (jQuery FlexSlider v2.2.2) js helper for Wordpress
 *Version 1.0
*/

jQuery(function ($) {
	$(window).load(function() { 
		$('div.slidercat[id^="slidercat_"]').each( function() {
	 	var $div = $(this);
  		var token = $div.data('token');
		var myScid = window['slidercat_' + token];
		if (myScid.taxasnavfor_str === "") {
			var scanimation = true;
			var scstartat = "1";
			} else {
			var	scanimation = false;
			var scstartat = "1";
		}
		if (scanimation === true){
			if ( myScid.taxsync_str === "") {
			var scanimation = true;
			var scstartat = "-1";
			} else {
			var	scanimation = false;
			var scstartat = "1";
			}
		}
  		$div.flexslider({
//		namespace: "flex-",
		selector: ".slides > div", 
		animation: myScid.taxfx_str,
		easing: myScid.taxease_str, 
		direction: myScid.taxdirect_str,
		reverse: myScid.taxrevordr_str,	
		animationLoop: myScid.taxanimloop_str,
		smoothHeight: myScid.taxsmoothh_str,
		startAt: parseInt(scstartat),
 		slideshow: scanimation, 
		slideshowSpeed: 0,
		animationSpeed: parseInt( myScid.taxanimspd_str ),
		initDelay: 0,
		randomize: myScid.taxrandom_str,
		
		useCSS: false,
//		pauseOnAction: true, //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
//		pauseOnHover: true, 
		video: myScid.taxvideo_str,
		controlNav: false,
		asNavFor: myScid.taxasnavfor_str,
		sync: myScid.taxsync_str,
		//itemMargin: 5,
	
		mousewheel: myScid.taxmousew_str,   //Requires jquery.mousewheel.js 
		pausePlay: myScid.taxpause_str,
		pauseText: "",     
   		playText: "",
		
   		itemWidth: myScid.taxcarwdth_str,
  		minItems: myScid.taxcarmin_str,
 		maxItems: myScid.taxcarmax_str,
			        
		init: function(slider) {
			
			$('#slidercat_'+ myScid.taxid_str + '.slidercatitem' ).each(function() {
				jQuery(this).css('display', 'block');	
			});
				},      
        start: function(slider){
        	   slider.removeClass('loading'); 
               slider.flexAnimate(1);
               slider.flexAnimate(0);
        		}, 
	
		before: function(slider){
			
				$('div:not(".flex-active-slide") .animated', slider).not('.disabled').each(function(index) {
                        var el = $(this);
                        var cloned = el.clone();
                        el.before(cloned);
                        $(this).remove();
                    });
				
				$('#slidercat_'+ myScid.taxid_str + '.slidercatitem' ).each(function() {
					jQuery(this).css('display', 'block');
					});
        		},
					
		after: function(slider){
				slider.stop();
				slider.vars.slideshowSpeed = $(slider.slides[slider.currentSlide]).data('duration');
				slider.vars.pausePlay =
				slider.play();
				},
				
	  }); // flexslider
	}); // slidercat each func  
  }); // window load func			
}); //jquery func	