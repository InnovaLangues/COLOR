	var $ = jQuery.noConflict();
		$(document).ready(function(){
		
		// If javascript disabled */
		$("body").removeClass("nojs").addClass("js");
		
		// Homepage slider */
		$('#slider').cycle({
	            timeout: 5000,  // milliseconds between slide transitions (0 to disable auto advance)
	            fx:      'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc...            
				prev: '#prevslide',  // selector for element to use as event trigger for previous slide 
				next: '#nextslide',  // selector for element to use as event trigger for next slide 
				pager:   '#pager',  // selector for element to use as pager container
	            pause:   0,	  // true to enable "pause on hover"
				cleartypeNoBg: true, // set to true to disable extra cleartype fixing (leave false to force background color setting on slides) 
	            pauseOnPagerHover: 0 // true to pause when hovering over pager link
	        });	
	});
	$(document).ready(function(){
	$('#pauseslide').click(function() { 
	    $('#slider').cycle('pause'); 
	});
	$('#playslide').click(function() { 
	    $('#slider').cycle('resume'); 
	});
});