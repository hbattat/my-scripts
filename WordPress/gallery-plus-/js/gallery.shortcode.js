jQuery(function($){
	 var 	$sliders    = $('.gallery-slider'),
	 		$lightboxes = $('.gallery-lightbox');

	  	$sliders.each(function(){
	  		var $self = $(this),
	  			h     = parseInt($self.attr('height')),
	  			w     = parseInt($self.attr('width'));
	  		
	  		$self.galleria({
	  			height : h, 
	  			width  : w 
	  		});

	  		

	  	});

	  	$lightboxes.each(function(){
	  		var $self = $(this)

	  		$self.find('a').prettyPhoto({
	  			theme: 'dark_square'
	  		});
	  	});

})