(function ($) {
	$.fn.flexDisplay = function (options){
		var base = this;
		// plugin options
		
		var defaults = {
			classes       :  {'cn' : 'selected', 'ci' : 'current','hd' : 'hide'}, // cn = current nav for the anchors, hd = hide, ci = currentitem
			hasParent     :  true, // ie li > a   ... li gets class if true - else if false just a gets the class
			callback      :  null // on finish slide switch ie. callback: function(){alert('callbk');}
			//animation     :  {'type' : 'fade', 'timing' : 300}, //  add to next version ? slide, fade, toggle
		};


		// setup options
		var opt = $.extend(defaults,options);
		// for ie7 fix
		var remove = window.location.pathname;

		// run functions for each instance 
		return base.each(function (index) {
			this.instance = index;
			
			
			var $obj = $(this);

			$obj.connect = $obj.attr('href');
			// for ie7 fix
			$obj.connect.replace(remove , "");
			console.log($obj.connect);
			// set first element to current else hide
			if(this.instance === 0){
				if (opt.hasParent){
					$($obj).parent().addClass(opt.classes.cn);
					base.currN = $($obj).parent();
				} else {
					$($obj).addClass(opt.classes.cn);
					base.currN = $($obj);
				}

				base.curr = $obj.connect;
				$(base.curr).addClass(opt.classes.ci);

			} else {
				$($obj.connect).addClass(opt.classes.hd);
			}

			$obj.bind('click' ,function(evt){
				// prevent clickthrough & default behaviours
				evt.preventDefault();

				// check if current and selected are same - if so end
				if($obj.connect === base.curr){return false;} 

				// set what was current to dying
				var die  =  base.curr;
				var dieN =  base.currN;

				// set clicked as the new current
				base.curr = $obj.connect;

				dieN.removeClass(opt.classes.cn);

				if (opt.hasParent){
					base.currN = $obj.parent();
				
				} else {
					base.currN = $obj;
				}
				
				base.currN.addClass(opt.classes.cn);

				// hide dying element
				$(die).addClass('moving').removeClass(opt.classes.ci);
				setTimeout(function(){
						$(die).addClass(opt.classes.hd).removeClass('moving');										
				},900);
			

				//show current element
				$(base.curr).addClass(opt.classes.ci).removeClass(opt.classes.hd);

				// check if has callback => if so call the function
				if (jQuery.isFunction(opt.callback)){opt.callback.call(this);}
				
				// prevent clickthrough
				return false; 
			});
		});
  };
}(jQuery));

