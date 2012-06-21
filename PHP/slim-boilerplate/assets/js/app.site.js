$(function(){
	var args_fancy = {
		padding : 0
		, margin  : 0
		, overlayColor : '#000'
		, overlayOpacity : 0.7
	}
	// Plugin for routing
	APP.Path = Path;
	
	// hook #help click to fancybox
	$('#help').fancybox(args_fancy);

	

	// Setup sound manager
	soundManager.url =  APP.url+'assets/soundmanager/swf/';
	soundManager.flashVersion = 9; 
	soundManager.useFlashBlock = false; 

});
