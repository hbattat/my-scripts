<?php

/*
Plugin Name: Contentstructor
Plugin URI: 
Description: Makes it quick and easy to develop a page structure and generate the pages at the beginning of WordPress development
Author: Andres Hermosilla
Version: 0.1
Author URI: http://ahermosilla.com/
*/




/**
 * 
 * @author        Andres Hermosilla
 * @last_edit     11/8/2011
 * @description   Registration hook and function for when the plugin installs. Adds a custom page as well as hooks in the Javavascript and adds AJAX action
 *
 */
 
/* WordPress hook for plugin activation */
register_activation_hook( __FILE__, 'activate_plugin_fnc'); 

/* Hooked Activation Function */
function activate_plugin_fnc(){
	$page = get_page_by_title('Create Structure');
	if(!$page) {
	 	 	/* Generate page that plugin uses for page creation */
	  		$post_gen = array(
				'post_title' => 'Create Structure',
				'menu_order' => 0,
				'post_status' => 'publish',
				'post_type' => 'page',
				//'post_content' => $form
     		);

		/* Insert the post into the database */
		$post_gen_id =  wp_insert_post($post_gen);
	}
	
	/* For next revision */
	/* Generate page that plugin uses for page editing */
	 $post_ed = array(
					     'post_title' => 'Edit Pages',
					     'menu_order' => 0,
					     'post_status' => 'publish',
					     'post_type' => 'page',
						 'post_content' => $post_gen_id
     
	);

	/* Insert the post into the database */
	//$post_ed_id =  wp_insert_post($post_ed);
	
	
	
 
	
} 

/* End of Activation Hook */


/* Only enables plugin magic if is admin user*/
function hm(){
	
	global $current_user;
	print_r( $current_user);
}
//add_action('admin_notices','hm');
//if ( current_user_can('manage_options') ) { 

/**
 * 
 * @author        Andres Hermosilla
 * @last_edit     11/8/2011
 * @description   Adds AJAX action available to the front end for handling the page generation request
 *
 */

add_action('wp_ajax_create_pages', 'create_pages');
add_action('wp_ajax_nopriv_create_pages', 'create_pages');
	
	function create_pages() {
		
		
		
		/* Creates page position */
		$count = 0; 
		foreach($_POST['pages'] as $page){
			/* Create post object */
 		 	$post_parent = array(
     			'post_title' => $page['parent'],
     			'menu_order' => $count,
     			'post_status' => 'publish',
     			'post_type' => 'page',
     		);

			/* Insert Post and save ID */
 			$post_id =  wp_insert_post($post_parent);
			$count++;
			
			/* Checks is page has children */
			if($page['children']){
				$count_child = 0;
				foreach($page['children'] as $page_child){
					/* Create post object */
					  $post_child = array(
					     'post_title' => $page_child['parent'],
					     'menu_order' => $count_child,
					     'post_status' => 'publish',
					     'post_type' => 'page',
						 'post_parent' => $post_id
     
					  );

					/* Insert the post into the database */
					$post_child_id =  wp_insert_post( $post_child );
					if($page_child['children']){
						$count_child_child  = 0;	
						foreach($page_child['children'] as $page_child_child){
							/* Create post object */
					 		 $post_child_child = array(
							     'post_title' => $page_child_child,
							     'menu_order' => $count_child_child,
							     'post_status' => 'publish',
								 'post_type' => 'page',
								 'post_parent' => $post_child_id 
     						);

							/* Insert the post into the database */
							$post_child_child_id =  wp_insert_post( $post_child_child);	
							$count_child_child++;	
						}
						
					}
					
					$count_child ++;
				}
			}
		}
		print_r($_POST);
		die(); /* This is required to return a proper result */
	}

/**
 * 
 * @author        Andres Hermosilla
 * @last_edit     11/8/2011
 * @description   Adds appropriate javascript to footer for sorting to work
 *
 */

function add_to_footer() { ?>
    <script>
	var app = {ajaxurl:"<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php"}
	</script> 
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js"></script>
	<script src="<?php bloginfo('wpurl') ; ?>/wp-content/plugins/contentstructor/plugin.js"></script>
    
     <?php      
}    
 
add_action('wp_footer', 'add_to_footer'); // For use on the Front end (ie. Theme)



/**
 * 
 * @author        Andres Hermosilla
 * @last_edit     11/8/2011
 * @description   Adds CSS to header to style the form for page geneartion
 *
 */

function add_to_header() { ?>
	<style>
#content-structor { 
	margin: 20px;
	width: 600px;
}
#content-structor #sortable {
    
    margin: 10px 0;
}
#content-structor .primary {
    margin: 0 0px 5px 0px;
    padding: 8px;
    font-size: 1.2em;
    background: #eee;
    position: relative;
    border-radius: 6px;
    border: solid 1px #ccc;
}
#content-structor #push,
#content-structor #add-page {
    color: #ffffff;
    background-color: #0064cd;
    border-radius: 4px;
    cursor: pointer;
    display: inline-block;
    padding: 5px 14px 6px;
    background-repeat: repeat-x;
    background-image: -khtml-gradient(linear, left top, left bottom, from(#049cdb), to(#0064cd));
    background-image: -moz-linear-gradient(top, #049cdb, #0064cd);
    background-image: -ms-linear-gradient(top, #049cdb, #0064cd);
    background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #049cdb), color-stop(100%, #0064cd));
    background-image: -webkit-linear-gradient(top, #049cdb, #0064cd);
    background-image: -o-linear-gradient(top, #049cdb, #0064cd);
    background-image: linear-gradient(top, #049cdb, #0064cd);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#049cdb', endColorstr='#0064cd', GradientType=0);
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
    border-color: #0064cd #0064cd #003f81;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
}
#content-structor #push{
	display: none;
}
#content-structor #push.visible{
	display: inline-block;
}
#content-structor #sortable input { }
#content-structor .secondary {
    padding: 6px 0 6px 20px;
    border-left: solid 1px #ccc;
    margin: 0 0 0 20px;
}
#content-structor #sortable  .secondary input {
    font-weight: normal;
    min-width: 200px;
    font-size: 90%;
}
#content-structor .secondary .child {
    margin: 0 0 0px 0;
    position: relative;
    border-bottom: solid 1px #ddd;
    padding: 6px;
    cursor: move;
}
#content-structor .secondary .child .this-del {
    right: 6px;
    font-size: 12px;
    line-height: 12px;
    padding: 3px 6px;
    top: -1px;
    right: -5px;
    position: relative;
}
#content-structor .secondary .child .plus {
	right: 6px;
    font-size: 12px;
    line-height: 12px;
    padding: 3px 6px;
    top: -1px;
    right: -9px;
    position: relative;
}
#content-structor .connected { }
#content-structor .this-del {
    position: absolute;
    font-size: 18px;
    line-height: 18px;
    background: #f3e0e0;
    right: 8px;
    top: 8px;
    padding: 4px 9px;
    text-align: center;
    display: inline-block;
    cursor: pointer;
    background-color: #c43c35;
    background-repeat: repeat-x;
    background-image: -khtml-gradient(linear, left top, left bottom, from(#ee5f5b), to(#c43c35));
    background-image: -moz-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: -ms-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ee5f5b), color-stop(100%, #c43c35));
    background-image: -webkit-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: -o-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: linear-gradient(top, #ee5f5b, #c43c35);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ee5f5b', endColorstr='#c43c35', GradientType=0);
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
    border-color: #c43c35 #c43c35 #882a25;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
    border-radius: 4px;
    color: #fff;
}
#content-structor .heading {
    margin: -8px -8px 6px -8px;
    border-bottom: solid 1px #ccc;
    padding: 8px 8px 10px 8px;
    background: #ddd;
    background: #ededed;
    background: -moz-linear-gradient(top,  #ededed 0%, #c9c9c9 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ededed), color-stop(100%,#c9c9c9));
    background: -webkit-linear-gradient(top,  #ededed 0%,#c9c9c9 100%);
    background: -o-linear-gradient(top,  #ededed 0%,#c9c9c9 100%);
    background: -ms-linear-gradient(top,  #ededed 0%,#c9c9c9 100%);
    background: linear-gradient(top,  #ededed 0%,#c9c9c9 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ededed', endColorstr='#c9c9c9',GradientType=0 );
}
#content-structor .heading input { min-width: 290px }
#content-structor .plus {
    color: #fff;
    position: absolute;
    right: 42px;
    cursor: pointer;
    z-index: 3;
    top: 8px;
    cursor: pointer;
    display: inline-block;
    background-color: #e6e6e6;
    background-repeat: no-repeat;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), color-stop(25%, #ffffff), to(#e6e6e6));
    background-image: -webkit-linear-gradient(#ffffff, #ffffff 25%, #e6e6e6);
    background-image: -moz-linear-gradient(top, #ffffff, #ffffff 25%, #e6e6e6);
    background-image: -ms-linear-gradient(#ffffff, #ffffff 25%, #e6e6e6);
    background-image: -o-linear-gradient(#ffffff, #ffffff 25%, #e6e6e6);
    background-image: linear-gradient(#ffffff, #ffffff 25%, #e6e6e6);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#e6e6e6', GradientType=0);
    padding: 5px 14px 6px;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
    color: #333;
    font-size: 13px;
    line-height: normal;
    border: 1px solid #ccc;
    border-bottom-color: #bbb;
    border-radius: 4px;
}
#content-structor .tertiary {
	padding:8px 0 8px 20px;	
	margin:0 0 0 6px;
	border-left:solid 1px #ccc;
}
#content-structor .child-child {
	padding:2px;
	margin:0 0 5px 0;	
}
#content-structor .instructions {
	
	padding:12px;
	border-radius:4px;
	border:dashed 1px #ccc;
}

</style>
     <?php      
}    
 
add_action('wp_head', 'add_to_header'); 

	

/**
 * 
 * @author        Andres Hermosilla
 * @last_edit     11/8/2011
 * @description   Filters the content and checks for title and if matches adds HTML for page creation form
 *
 */

function add_form($content = ''){
	global $post;
	if($post->post_title == 'Create Structure'){
		
	?>
    <!-- Plugin for making pages and structure very quickly! -->
<div id="content-structor">
		<p class="instructions">To Add primary pages press button below. To add a subpage to existing pages click + Add Subpage or to add subpages to subpages click the +. You can move existing subpages to other submenus. If you want to delete a primary menu item it will also remove the children, so be sure to move any submenu items you want moved.</p>
		<div id="add-page">
	<a id="control">Add Main Page</a>
</div>
<form id="page-structure">
	<div id="sortable" class="connected">
		<div class="primary" id="parent-1">
    		<div class="heading"><input type="text" value="Home" /></div>	
    		<a class="this-del">x</a><a class="plus" data-count="0">+ Add Subpage</a>
   			<div class="secondary  connected"></div>
    	</div>
        
    	<div class="primary" id="parent-2">
    		<div class="heading"><input type="text" value="About Us" /></div>	
    		<a class="this-del">x</a><a class="plus" data-count="0">+ Add Subpage</a>
    		<div class="secondary  connected"></div>
    	</div>
        <div class="primary" id="parent-3">
    		<div class="heading"><input type="text" value="Contact" /></div>	
        	<a class="this-del">x</a><a class="plus" data-count="0">+ Add Subpage</a>
    		<div class="secondary  connected">
            	<div class="child" id="child-1">
                	<input type="text" value="Subpage"><a class="this-del">x</a><a class="plus" data-count="1">+</a>
                	<div class="tertiary connected">
                    		<div id="sub-child-1" class="child-child">
                            	<input type="text" value="Subpage"><a class="this-del">x</a>
                            </div>
                    </div>
                </div>
            </div>
    	</div>
	</div>
</form>
<!--<a id="save">Save</a>-->
<a id="push" class="visible">Publish</a>
</div>
    <!-- // Plugin for making pages and structure very quickly! -->
<?php
		/* end ouput of HTML content for form */
	} elseif($post->post_title == 'Edit Pages') {
		return $content;
	} else {
		return $content;
	}

}
/* Filters the_content */	
add_filter('the_content', 'add_form');	

/* end check for admin */
//}