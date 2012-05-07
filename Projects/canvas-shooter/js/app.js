
var $eyes = $('#eyes');

(function timer() {
t=setTimeout(function(){
	$eyes.toggleClass('blink');
	setTimeout(function(){
		$eyes.toggleClass('blink');
		setTimeout(function(){
			$eyes.toggleClass('blink');
				setTimeout(function(){
			  $eyes.toggleClass('blink');
		},320)
		},320)
		
		},320);
	
  timer();
 
	},6000);
})();