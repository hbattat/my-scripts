<?php
/*
Plugin Name: Gallery Shortcode +
Plugin URI: http://ahermosilla.com
Description: Enhance the functionality of the [gallery] shortcode as well as clean it up!
Version: 1.0
Author: Andres Hermosilla
Author URI: http://ahermosilla.com
License:  GPL2
*/

// http://codex.wordpress.org/Writing_a_Plugin

// Remove existing [gallery] shortcode 
// http://codex.wordpress.org/Function_Reference/remove_shortcode
remove_shortcode('gallery');
	
// Re-add new [gallery] shortcode
// http://codex.wordpress.org/Function_Reference/add_shortcode 
add_shortcode('gallery','gallery_shortcode_plus');


// Cleanly & safely adding [gallery] styles to theme output
// http://codex.wordpress.org/Function_Reference/wp_enqueue_style
add_action('wp_enqueue_scripts', 'gallery_styles');

function gallery_styles(){
	// base css for core gallery
	wp_register_style('gallery_core', plugins_url('css/gallery.all.css', __FILE__));
	wp_enqueue_style( 'gallery_core');


	// http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/#!prettyPhoto
	wp_register_style('pretty_photo', plugins_url('css/prettyPhoto.css', __FILE__));
	wp_enqueue_style( 'pretty_photo');

	//http://galleria.io/
	wp_register_style('gallery_plus', plugins_url('css/galleria.classic.css', __FILE__));
   	wp_enqueue_style( 'gallery_plus');
   	
}

// Cleanly & safely adding [gallery] scripts to theme output
// http://codex.wordpress.org/Function_Reference/wp_enqueue_script
function gallery_scripts() {
	// http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/#!prettyPhoto
	wp_register_script('prettyphoto', plugins_url('js/jquery.prettyPhoto.js', __FILE__), array('jquery'),'',true);
	wp_enqueue_script('prettyphoto');

	//http://galleria.io/
	wp_register_script('galleria', plugins_url('js/galleria-1.2.6.min.js', __FILE__), array('jquery'),'',true);
	wp_enqueue_script('galleria');

	wp_register_script( 'galleria_theme', plugins_url('js/galleria.classic.min.js', __FILE__), array('galleria'),'',true);
	wp_enqueue_script('galleria_theme');

	// The init script we wrote
	wp_register_script( 'shortcode_js', plugins_url('js/gallery.shortcode.js', __FILE__), array('galleria'),'',true);
    wp_enqueue_script('shortcode_js');             
}    
 
add_action('wp_enqueue_scripts', 'gallery_scripts'); 
 
// Function that runs everytime that [gallery] is called
function gallery_shortcode_plus($attr) {
	global $post, $wp_locale;

	static $instance = 0;
	$instance++;

	

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'type'       => 'core', // lightbox, slider, core
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'div',
		'icontag'    => 'figure',
		'captiontag' => 'figcaption',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'height'     => 400,
		'width'      => 600,
	), $attr));

	$id = intval($id);

	if ( 'RAND' == $order ){
		$orderby = 'none';
	}

	// Base query for pulling image attachments
	$query = array(
		'post_status' 	 => 'inherit',
		'post_type' 	 => 'attachment',
		'post_mime_type' => 'image', 
		'order' 		 => $order,
		'orderby' 		 => $orderby
	);

	// is include set?
	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$query['include'] = $include;
		$_attachments = get_posts($query);

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	// is exclude set?		
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$query['exclude'] = $exclude; 
		$attachments = get_children($query);
	
	// default	
	} else {
		$query['post_parent'] = $id;
		$attachments = get_children($query);
	}

	// If there are no attachments, return nothing
	if (empty($attachments)){
		return '';
	}

	if (is_feed()) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ){
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		}
		return $output;
	}

	// Build HTML for gallery
	$itemtag 	 = tag_escape($itemtag);
	$captiontag  = tag_escape($captiontag);
	$columns 	 = intval($columns);
	$itemwidth 	 = $columns > 0 ? floor(100/$columns) : 100;
	$selector    = "gallery-{$instance}";
	$size_class  = sanitize_html_class( $size );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-{$type} ";
	
	$output =  $gallery_div;

	$i = 0;

	// If setting is not a slider
	if($type !== 'slider'){
		$output .= "gallery-columns-{$columns} gallery-size-{$size_class}'>\n";

		foreach ( $attachments as $id => $attachment ) {
			
			// Change link, depending on whether it's a lightbox or not		
			if($type === 'core'){
				$href = isset($attr['link']) && 'file' == $attr['link'] ? $attachment->guid : get_attachment_link($id);
			} else {
				$href = $attachment->guid;
			}

			$link = '<a rel="gallery['.$instance.']" href="'.$href.'">'."\n\t\t\t";
			$link .= wp_get_attachment_image( $id, $size, array(
							'class'	=> "attachment-$size",
							'alt'   => trim(strip_tags( get_post_meta($id, '_wp_attachment_image_alt', true))),
							'title' => trim(strip_tags( $attachment->post_title ))
			));
			$link .= "\n\t\t</a>\n";
			

			$output .= "\t<{$itemtag} class='gallery-item'>\n";
			$output .= "\t<{$icontag} class='gallery-icon'>\n\t\t$link\t</{$icontag}>\n";

		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "\t<{$captiontag} class='wp-caption-text gallery-caption'>\n";
			$output .=  "\t\t". wptexturize($attachment->post_excerpt) . "\n\t</{$captiontag}>";
		}

		$output .= "\n\t</{$itemtag}>\n";
		
		// Check if end of row
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '<br class="clearfix" />';
		}

		$output .= "<br class=\"clearfix\" />\n";
	
	// Different output for slider
	} else if($type ==='slider'){
		
		$output .= "' height='".$height."' width='".$width."'>\n";
		foreach ( $attachments as $id => $attachment ) {
			
			$output .= "\t".'<a href="'.$attachment->guid.'">'."\n";
            $output .= "\t\t".'<img title="'.wptexturize($attachment->post_excerpt).'" alt="';
            $output .= trim(strip_tags( get_post_meta($id, '_wp_attachment_image_alt', true) ));
            $output .='" src="'.wp_get_attachment_thumb_url( $id )."\">\n";
        	$output .= "\t".'</a>'."\n";

        	
		}
	}
	
	$output .= "</div>\n";

	return $output;
}

?>