$(function(){
	/*
	 * 	Pre init setup
	 */
	// Plugin for routing
	APP.Path = Path;

	// Client data
	APP.applicant = {type:'cbo'};
	APP.form_complete = false;
	APP.started = false;

	
	APP.progress = 1;
	APP.stages = 5;
	APP.on_stage = 1;

	APP.sects = 3;
	APP.on_sect = 1;

	// Setup sound manager
	soundManager.url =  APP.url+'assets/soundmanager/swf/';
	soundManager.flashVersion = 9; 
	soundManager.useFlashBlock = false; 




	// Commonly accessed elements
	APP.$webinar = $('#webinar-wrap');
	APP.$next = $('#go-next');
	APP.$back  = $('#go-back');
	APP.$messages = $('#messages');
	APP.$control_panel = $('#control-panel');
	APP.$applicant = $('#applicant');
	APP.$walkthrough = $('#walkthrough');
	APP.$volume = $('#volume');


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

	APP.slides = '';

	// Load up the Slides
	$.ajax({
		url: 'slides',
		success: function(data){
			APP.slides = $.parseJSON(data);
		}
	});

	// Lock Next button
	APP.lockNext = function(){
		APP.$next.on('click',function(e){
			e.preventDefault();
				return false;
		});
	}

	// Unlock Next button
	APP.unlockNext = function(){
		APP.$next.off('click');
	}

	// window.location.hash = '/part/'+APP.part;
	// $.publish("progress", 1);

	/*
	 * 	All subscriptions below
	 */

	// Updates progress
	$.subscribe("progress", function(e, dir){
		APP.$control_panel.find('.bar').animate({
			width : ((APP.on_stage + APP.on_sect ) / (APP.sects + APP.stages) ) * 100 +'%'
		},1900);
	});

	/*
	 * 	Runs on page load
	 */
	APP.init = (function(){
			/*
    		soundManager.onready(function() {
			   APP.audio = soundManager.createSound({
				    id: 'voiceover',
				    url: APP.url+'assets/audio/sample.mp3'
				  });

				 // APP.audio.play();
			});
			*/

			

		var PATH = APP.Path,
		// args for fancybox
		args_fancy = {
			padding : 0
			, margin  : 0
			, overlayColor : '#000'
			, overlayOpacity : 0.7
		}
		// Setting up initial part
		
		//  start form Validation
		APP.$applicant.validate({
			rules: {
				"applicant[email]" : {required: true, email: true}
			},
			messages: {
				"applicant[email]" : 'Please enter a valid email',
				"agree" : "Please agree to the terms!"
			},
			// AJAX submit
			submitHandler: function(form) {
				// disabled submit to prevent double entry
				$(form).find('input[type=submit]').attr('disabled','disabled');
				$(form).ajaxSubmit({
					url: APP.url+'register',
                    type:"post",
                    success: function(json){
	                	var response = jQuery.parseJSON(json);
	                	
	                	if(response.pass == 'true'){
	                		// Remove click handler
	                		
	                		
	                		// Kills old HTML to prevent double form submission
	                		setTimeout(function(){
								APP.$applicant.html('<div class="already-sent"><h2>Information Submitted</h2><p>Press Continue to move on to the next section detailing how to fill the form out.</p></div>');
							}, 300);

							APP.unlockNext();
							APP.$next.click();
							APP.form_complete = true;
	                	} else {
	                		$(form).find('input[type=submit]').removeAttr('disabled');
	                		var $errors = $('#form-errors');
	                		for(var error in response.errors){
	                			$errors.append('<div class="error-php">'+error+'</div>'); 
	                		}
	                	}
                    } 
                }); // <<-- end ajaxSubmit
			}
		});
		// ^^ end form Validation

		/*
		 * 	Setting up PATH.js
		 */
		var old = 'welcome';
		var newr = 'welcome';
		var app_started = false;

		var moveSection = function(id){
			soundManager.stopAll();
			
			old = newr;
			newr = id;

			$.publish("progress", 1);

			if(newr !== old){
				APP.$webinar.addClass('on-part-'+id).removeClass('on-part-'+old);

			 	APP.$webinar.find('.part-current').removeClass('part-current');
				APP.$webinar.find('#'+id).addClass('part-current');

				if(!app_started && id !== 'welcome'){
					hideWelcome();
					app_started = true;
				}
			}
		 }


		 var  hideWelcome = function(){
		 	// Animate Welcome position
			APP.$messages.find('.big').animate({
				left: '400px',
				opacity : 0
			}, 400, 'linear',function(){
				$(this).slideUp();
			});

			APP.$webinar.removeClass('part-start');
		 }


		 /*
		  *
		  */
		PATH.map("#/welcome").to(function(){
			moveSection('welcome');
			APP.on_stage = 1;

		}).exit(function(){
			hideWelcome();
			app_started = true;
		});


		

		APP.applicant_type = {
			is_start : false,
			$types : APP.$webinar.find('#applicant-types a')
		}

		PATH.map("#/applicant-type").to(function(){
			// parent object
			var __par = APP.applicant_type;

			APP.on_stage = 2;

			moveSection('applicant-type');
			
			APP.$back.attr('href','#/applicant-type');
			APP.$next.attr('href','#/applicant-form');


			if(!__par.toggled){
				APP.lockNext();
			} else {
				APP.unlockNext();
			}

			__par.$types.on('click',
				function(){
					__par.$types.removeClass('selected');
					$(this).addClass('selected');
					APP.applicant.type = $(this).attr('data-type');

					if(!__par.toggled){
						
						__par.toggled = true;
						APP.unlockNext();
					}

				});

		});

		APP.applicant_form = {
			is_start : false
		}

		PATH.map("#/applicant-form").to(function(){
			var __par = APP.applicant_form;
			APP.on_stage = 3;
			moveSection('applicant-form');

			if(!APP.form_complete){
				APP.lockNext();
				
			}

			APP.$back.attr('href','#/applicant-type');
			APP.$next.attr('href','#/walkthrough');
		});

		APP.walkthrough = {
			sect : 1,
			pt : 1,
			slides : APP.slides,
			is_start : false
		}

		PATH.map("#/walkthrough(/:sect)(/:pt)").to(function(){

			// Kill sound on each movement
			soundManager.stopAll();
			
			// Move to walkthrough
			moveSection('walkthrough');
			APP.on_stage = 4;
			
			$.publish("progress", 1);

			var __par = APP.walkthrough,
				type_app = APP.applicant.type,
				handleSlides = function(__par, typ){

					// The current slide
					var sect = APP.slides[typ][__par.sect - 1],
					// The next slide
					sect_nxt = APP.slides[typ][__par.sect],
					// the previous slide
					sect_bk = APP.slides[typ][__par.sect - 2],
					// The different points/steps per slide
					steps = sect.steps,

					// Calculate current step/point
					pt = steps[(__par.pt-1)],
					// Next step/point
					pt_nxt = steps[(__par.pt)],
					// Previous step/point
					pt_bk = steps[(__par.pt-2)],
					// Holder for HTML to be outputed
					html = '';
					
					// Set global for what section/slide app is on
					APP.on_sect = __par.sect;
					
					$.publish("progress", 1);

					// Play audio
					/*
					var audio = soundManager.createSound({
						 id: 'audio-'+sect+'-'+pt, 
						 url: pt.mp3, 
						 // optional sound parameters here, see Sound Properties for full list
						 volume: 50,
						 autoPlay: true
					});
					*/

					// audio.play(); 
					
					// Template for outputted html
					html += ('<div class="image-wrap"><img class="form-img" src="'+sect.img+'" width="640px" /></div>');
					html += '<div class="marker-wrap">'+'</div>';
					html += '<div class="instructions"><h4 class="title">'+pt.title+'</h4>'+pt.instruction+'</div>';

					// Marker/ highlight
					var marker = $('<div class="highlight" style="top:'+pt.dim.y+'px;height:'+pt.dim.h +'px;"><div class="pointer"></div></div>');
						marker.hide();

					// Append HTML	
					html = $('#section-parts').find('.inner').html(html);

					// Append/fadein marker
					setTimeout(function(){
						$('#section-parts').find('.marker-wrap').append(marker);
						$(marker).fadeIn(800);
					},300);

					// Handles updates of anchor for <- back
					// If route paramenter is undefined
					if(pt_bk === undefined){
						// If there is no back slide we are at the begginig so we need need to go back more
						if(sect_bk === undefined){
							APP.$back.attr('href','#/applicant-form');
						} else {
							// Go to previous slide
 							APP.$back.attr('href','#/walkthrough/'+(__par.sect-1)+'/'+(sect_bk.steps.length)+'');
 							
 						}
					} else {
						APP.$back.attr('href','#/walkthrough/'+__par.sect+'/'+(__par.pt-1)+'');
					}
					
					// Handles updates of anchor for Continue -> 
					// If route paramenter is undefined
					if(pt_nxt === undefined){

						// If there is no next section, we are at end
						if(sect_nxt === undefined){
							APP.$next.attr('href','#/thank-you');
						} else {
							// Go to next slide
 							APP.$next.attr('href','#/walkthrough/'+(__par.sect+1)+'/1');
 						}
					} else {
						APP.$next.attr('href','#/walkthrough/'+__par.sect+'/'+(__par.pt+1)+'');
					}
				};
			
				
			// convert string to INT
			// hold the section/slide we are on 	
			__par.sect = parseInt(this.params["sect"]);
			// the point/step
			__par.pt   = parseInt(this.params["pt"]);

			
			// if section isnt set, set base
			if(isNaN(__par.sect) || __par.sect === 0 ){
				__par.sect = 1;
				__par.pt = 1;

				//window.location.hash = '#/walkthrough/1/1';
			}

			// If point/part isnt set, go there
			if(isNaN(__par.pt) || __par.pt === 0 ){
				__par.pt = 1;
				//window.location.hash = '#/walkthrough/'+__par.sect+'/1';
			}
			
			
			
			if(old == 'welcome'){
				setTimeout(function(){
					handleSlides(__par, type_app);
				},900);
			} else {
				handleSlides(__par, type_app);
			}
    		

		});
		
		// Handles thankyou route
		PATH.map("#/thank-you").to(function(){
			moveSection('thank-you');
			APP.on_stage = 5;
		});
		

		// Handles welcome
		PATH.root("#/welcome");

		PATH.listen();

		// hook #help click to fancybox
		$('#help').fancybox(args_fancy);
		

	})();
	
	

})
