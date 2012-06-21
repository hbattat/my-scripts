$(function(){
	
	
	var  hideWelcome = function(){
		// Animate Welcome position
		APP.$messages.find('.big').animate({
			left: '400px',
			opacity : 0
		}, 400, 'linear',function(){
			$(this).slideUp();
		});

		APP.$webinar.removeClass('part-start');
	};
	var $applicant = $('#applicant');
	
	APP.Path = Path;
	
	APP.$next = $('#go-next');
	APP.$back  = $('#go-back');
	APP.$messages = $('#messages');
	APP.$webinar = $('#webinar-wrap');

	Path.map("#/welcome").to(function(){
		
	}).exit(function(){
		hideWelcome();
	});

	Path.map("#/applicant-form").to(function(){
		// In case is linked to directly
		hideWelcome();

		APP.$webinar.addClass('on-part-applicant-form').removeClass('on-part-welcome');
		APP.$webinar.find('.part-current').removeClass('part-current');
		APP.$webinar.find('#applicant-form').addClass('part-current');
	});

	

	Path.root("#/welcome");

	Path.listen();
	//  start form Validation
	$applicant.validate({
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
		                	// Kills old HTML to prevent double form submission
		                	setTimeout(function(){
								$applicant.html('<div class="already-sent"><h2>Information Submitted</h2><p>Press Continue to move on to the next section detailing how to complete the application.</p></div>');
							}, 300);
							window.location = APP.url +'walkthrough';
	                	} else {
	                		
	                		$(form).find('input[type=submit]').removeAttr('disabled');
	                		var $errors = $('#form-errors');
	                		for(var i in response.errors){
	                			$errors.html('<div class="error-php">'+response.errors[i]+'</div>'); 
	                		}
	                	}
                    } 
                }); // <<-- end ajaxSubmit
			}
		});
		// ^^ end form Validation
});
