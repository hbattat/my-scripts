$(function(){
//////////////////////////////////////////READY.SET.GO


/**
 * 	Listens for slide movement to move progress Bar
 */
var $section_label = $('#section-label');
$.subscribe("progress", function(){
	$section_label.html('Slide '+ (STEPS.cur + 1));
	APP.$control_panel.find('.bar').animate({
		width : (STEPS.cur  / STEPS.total ) * 100 +'%'
	},1000);
});

APP.Path = Path;


/**
 * 	Save same DOM Traversals
 */
APP.$webinar = $('#webinar-wrap');
APP.$next = $('#go-next');
APP.$back  = $('#go-back');
APP.$control_panel = $('#control-panel');
APP.$walkthrough = $('#walkthrough');
APP.$volume = $('#volume');
APP.end = false;
/**
 * 	Handles Table of contents for slides
 */
var $menu_anc = $('.slide-menu a').each(function(i){
	var $this = $(this);
	var slide = $this.parents('.slide-menu').index();

	$this.on('click',function(){
		$menu_anc.removeClass('current');
		$this.removeClass('current');
		SLIDES.goTo(slide);
		STEPS.goTo(i);
	});
});

/**
 * 	Setup for slides and function declarations
 */
var SLIDES = {}
	SLIDES.cur = 1; // offset so initial goTo can fire
	SLIDES.$els = APP.$walkthrough.find('.slide'); // all the slide elements
	SLIDES.$cur = SLIDES.$els.eq(SLIDES.cur); // current slide element
	SLIDES.total = SLIDES.$els.length; // # of slides
	// finds next/prev slide and returns it
	SLIDES.find = function(prev){
		if(prev){
			this.cur --;
		} else {
			this.cur ++;
		}

		this.$cur = this.$els.eq(this.cur);
		return this.$els.eq(this.cur);
	}
	// handles slide movement - basic
	SLIDES.move = function(goback){
		this.$els.removeClass('current-slide');
		this.$els.eq(this.cur).addClass('current-slide');
		
		if(goback){}
	}
	// handles direct slide movement
	SLIDES.goTo = function(i){
		if(i !== this.cur){
			this.cur = i;
			this.$cur = this.$els.eq(i);
			this.move();
		}
	}
/**
 * 	Setup for steps (within each slide) and function declarations
 */	
var STEPS = {}
	STEPS.cur = 1 // current step offsett so initial goTo can fire
	STEPS.$els = APP.$walkthrough.find('.step'); // all step els
	STEPS.total = STEPS.$els.length; // # of steps
	// finds next/prev step and returns it
	STEPS.find = function(prev){

		if(prev){
			this.cur --;
		} else {
			this.cur ++;
		}

		return SLIDES.$cur.find(this.$els.eq(this.cur));
	}
	// handles direct slide movement
	STEPS.move = function(goback){
		this.$els.removeClass('current');
		var $cur = this.$els.eq(this.cur);
		$cur.addClass('current');

		var action = $cur.attr('data-action');

		if (typeof action !== 'undefined' && action !== false) {
			eval(action);
		}

		
		// Add marker
		$marker = $cur.find('.highlight').clone();
		APP.$walkthrough.find('.marker-wrap').html($marker);
		$marker.fadeIn();
		
		// connect to table of contents menu
		$menu_anc.removeClass('current');
		$menu_anc.eq(this.cur).addClass('current');
		
		$.publish("progress", 1);

		// go back if passed true
		if(goback){}
	}
	// Handles direct linking
	STEPS.goTo = function(i){
		if(i !== this.cur){
			this.cur = i;
			this.$cur = this.$els.eq(i);
			this.move();
		}
	}

/**
 * 	Functions that attach to forwards and back click handler
 */		
var goNext = function(){
	var $next_step = STEPS.find();

	if($next_step.length === 1){
		STEPS.move();
	} else {
		$next_slide = SLIDES.find();
		
		if($next_slide.length === 1){
			SLIDES.move();
			STEPS.move();
		}  else {
				// End
			goThanks();
			APP.end = true;
		}
	}
	
}

var goBack = function(){
	if(APP.end){
		APP.end = false;
		leaveThanks();
	} 

	var $prev_step = STEPS.find(true);
	if($prev_step.length === 1){
		STEPS.move(true);
	} else {
		$prev_slide = SLIDES.find(true);

		if($prev_slide.length === 1){
			SLIDES.move(true);
			STEPS.move(true);
		} else {
			// very beggining

		}
	}
}

var goThanks = function(){
	APP.$webinar.removeClass('on-part-walkthrough'); 
	APP.$walkthrough.removeClass('part-current');

	APP.$webinar.addClass('on-part-thank-you'); 
	$('#thank-you').addClass('part-current');
}

var leaveThanks = function(){
	APP.$webinar.addClass('on-part-walkthrough'); 
	APP.$walkthrough.addClass('part-current');

	APP.$webinar.removeClass('on-part-thank-you'); 
	$('#thank-you').removeClass('part-current');
}
/**
 * 	Various click events for controls
 */
APP.$back.on('click',function(e){
	e.preventDefault();
	goBack();
});

APP.$next.on('click',function(e){
	e.preventDefault();
	goNext();
});

APP.$volume.on('click',function(){
	var $this = $(this);
	if($this.hasClass('muted')){
		$this.removeClass('muted');
		soundManager.unmute();
	} else {
		$this.addClass('muted');
		soundManager.mute();
	}
});
var $toc = $('#toc');
var $toc_menu = $toc.find('.toc-menu');
$toc.find('.toggler').on('click',function(e){
	e.preventDefault();
	$toc_menu.slideToggle();
});
	
var showForms = function(type, id){
	var args_fancy = {
		padding : 0
		, margin  : 0
		, overlayColor : '#000'
		, overlayOpacity : 0.7
	};
	var dir_img = APP.url + 'assets/images/';

	var html = '<div id="screenshot" class="modal" style="width:620px;">';
		html += '<hgroup class="modal-header gradient-grey">Form References</hgroup>';
		html += '<div class="modal-inner">';

	if (typeof id === 'undefined') {
    	html += '<img src="'+dir_img + 'screen-form-990.png" />';
		html += '<img src="'+dir_img + 'screen-tax-letter.png" />';
	} else {
		switch (id) {
		    case 'ez':
		        html += '<img src="'+dir_img + 'screen-form-990e.png" />';
		        break;
		    
		    case '990pt3':
		    	html += '<img src="'+dir_img + 'screen-form-990.png" />';
		    	html += '<img src="'+dir_img + 'screen-form-990-pt-3.png" />';
		        //screen-form-990-pt-3.png
		        break;
		    default:
		    	html += '<img src="'+dir_img + 'screen-form-990.png" />';
				html += '<img src="'+dir_img + 'screen-tax-letter.png" />';
		}
	}

	
		
		html += '</div>';
		html += '</div>';

	$.fancybox(
		html,
		args_fancy
	);
}

STEPS.goTo(0);
SLIDES.goTo(0);

if(SLIDES.total > 0){
	// APP.$walkthrough.find('.inner').css({'width': 684 *SLIDES.total+'px'});
}
	

	

	


	Path.listen();
//////////////////////////////////////////READY.SET.END
});
