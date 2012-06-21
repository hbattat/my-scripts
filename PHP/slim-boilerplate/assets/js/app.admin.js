$(function(){
	/*
	 * 	Pre init setup
	 */
	APP.Path = Path;

		

	/*
	 * 	Runs on page load
	 */
	APP.init = (function(){
		var PATH = APP.Path,
		// args for fancybox
		args_fancy = {
			padding : 0
			, margin  : 0
			, overlayColor : '#000'
			, overlayOpacity : 0.7
		}

		var $toggles = $('#organization-list').find('.organization');
		$toggles.each(function(){
			var toggled = false;
			var $content = $(this).find('.toggle-content');
			$content.hide();
			$(this).find('.expand').addClass('toggler').on('click',function(){
				$(this).toggleClass('is-toggled');
				if(toggled){
					$content.slideUp(100);
					toggled = false;
				} else {
					$content.slideDown(200);
					toggled = true;
				}
			});
		});

		PATH.map("#/part/:num").to(function(){
    			
		});
		PATH.root("#/dashboard");

		PATH.listen();

		// hook #help click to fancybox
		$('#help').fancybox(args_fancy);
		
		
	})();
	
	

})
