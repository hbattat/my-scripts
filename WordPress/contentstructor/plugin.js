/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 


@author : Andres Hermosilla
@date   : 11/1/11
@desc   : Javascript magic for sorting and adding pages to the tree view


- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */	
$(function() {
// STARTS $JQUERY READY	
	
	/// Initial Sortor setup
	var $sorter = $( "#sortable" ).sortable({
		// event handler for changes in sort order
		update: function() {
			var i = 1;
	   		$sorter.children('.primary').each(function(){
				// updates each items id
				$(this).attr('id','parent-'+i)
				i++;
			});
		 }
	});
	
	// find amount of items
	var items_parent = $sorter.sortable("toArray" ).length
	
	// Setup initial secondary sort lists
	var $secondary = $('.secondary');
		$secondary.sortable({
				connectWith: ".secondary",
				update: function() {
					// updates each items id
					var k = 1;
					$('.child',$secondary).each(function(){
							$(this).attr('id','child-'+k)
							k++;
					});	
					
					
				}
				
			});
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

@desc  :  For adding primary navigation items

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */	
		
	$('#control').bind('click',function(){
			var $parent = $('<div id="parent-'+items_parent+'" class="primary"><div class="heading"><input type="text" value="" /></div><a class="plus" data-count="0">+Add Subpage</a><a class="this-del">x</a></div>');
			
			$('#sortable').append($parent);
			var $secondary = $('<div class="secondary connected"></div>');
			$parent.append($secondary);
			// Set up sorter for secondary menu items
			$secondary.sortable({
				connectWith: ".secondary",
				update: function() {
					var j = 1;
					
					$('.child',$secondary).each(function(){
							// updates each items id
							$(this).attr('id','child-'+j)
							j++;
					});	
					
					
				}
				
			});
		});
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

@desc  :  Remove items from list, both submenu and main menu	

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */		
	
	$('.this-del').live('click',function(){
		var $par = $(this).parent();
		if($par.hasClass('primary')){
			var response = confirm("You sure you want to delete");
			if (response){
				$par.remove();
			} 
			
		} else {
			$par.remove();
		}
	});
			
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

@desc  :  Adds submenu items

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
$('.plus').live('click',function(){
	var $this = $(this), $par = $this.parent();
	var count = $this.attr('data-count');
		count++
			
	$this.attr('data-count',count);
	// append child to main menu item
	if($par.hasClass('primary')){
		var $child = $('<div id="child-'+count+'" class="child"><input type="text" /><a class="this-del">x</a><a class="plus" data-count="0">+</a></div>')
		$par.find('.secondary').append($child)
		
		// setup for tertiary
		var $tertiary = $('<div class="tertiary connected"></div>');
		$child.append($tertiary);
		
		$tertiary.sortable({
				connectWith: ".tertiary",
				update: function() {
					var j = 1;
					
					$('.child-child',$secondary).each(function(){
							// updates each items id
							$(this).attr('id','sub-child-'+j)
							j++;
					});	
					
					
				}
				});
	} else {
		$par.find('.connected').append('<div id="sub-child-'+count+'" class="child-child"><input type="text" /><a class="this-del">x</a></div>')	
	}
});
			
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

@desc  :  Apply and create pages structure

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */	
var $push = $('#push');

$('#save').bind('click',function(){
	$push.addClass('visible');
	$(this).addClass('hidden');
	
});
	
$push.bind('click',function(){
	
		$(this).removeClass('visible');	
	
		// variable for storing all the data
		var pages = []
		
		// loops through main menu items
		$('.primary').each(function(i){
			var $this = $(this);
			pages[i] = {}
			pages[i]['parent']  = $this.find('input').val();
					
			var $children = $this.find('.secondary .child');
			pages[i]['children'] = {}
			
			// loop through page submenu/children
			$children.each(function(k){
				var $this = $(this), subs = {};
				//pages[i]['children'][k]['parent'] = $this.find('input').val(); 
				subs.parent = $this.find('input').val(); 
				subs.children = {}
				var $sub_children = $this.find('.tertiary .child-child');
				//pages[i]['children'][k]['children'] = {};
				$sub_children.each(function(z){
					var $ths = $(this);
					subs.children[z] =  $ths.find('input').val(); 
					
				});
				
				pages[i]['children'][k] = subs
				
			});	
		});
			
	$.ajax({
  		type: 'POST',
		url: app.ajaxurl,
  		data:  {'action':'create_pages','pages':pages},
  		success: function(resp){
	
  		}
	});
	
});
			


// ENDS $JQUERY READY
});