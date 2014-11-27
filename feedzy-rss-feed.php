<?php
/**
 * Plugin Name: FEEDZY RSS Feeds by b*web
 * Plugin URI: http://b-website.com/feedzy-rss-feeds-plugin-wordpress-gratuit-utilisant-simplepie
 * Description: FEEDZY RSS Feeds is a small and lightweight plugin. Fast and easy to use, it aggregates RSS feeds into your WordPress site through simple shortcodes.				
 * Author: Brice CAPOBIANCO
 * Author URI: http://b-website.com/
 * Version: 1.5.3
 * Text Domain: feedzy_rss_translate
 */


/***************************************************************
 * SECURITY : Exit if accessed directly
***************************************************************/
if ( !function_exists('add_action') ) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if ( !defined('ABSPATH') ) {
	exit;
}


/***************************************************************
 * Load plugin textdomain
 ***************************************************************/
if (!function_exists('feedzy_rss_load_textdomain')) {
	function feedzy_rss_load_textdomain() {
		$path = dirname(plugin_basename( __FILE__ )) . '/langs/';
		$loaded = load_plugin_textdomain( 'feedzy_rss_translate', false, $path);
	}
	add_action('init', 'feedzy_rss_load_textdomain');
}


/***************************************************************
 * Add custom meta link on plugin list page
 ***************************************************************/
if ( ! function_exists( 'feedzy_meta_links' ) ) {
	function feedzy_meta_links( $links, $file ) {
		if ( strpos( $file, 'feedzy-rss-feed.php' ) !== false ) {
			$links[0] = '<a href="http://b-website.com/" target="_blank"><img src="' . plugins_url('img/icon-bweb.png', __FILE__ ) . '" style="margin-bottom: -4px;" alt="b*web"/></a>&nbsp;&nbsp;'. $links[0];
			$links = array_merge( $links, array( '<a href="http://b-website.com/feedzy-rss-feeds-wordpress-plugin-using-simplepie" target="_blank" title="'. __( 'Documentation and examples', 'an-translate' ) .'"><strong style="color:#db3939">'. __( 'Documentation and examples', 'an-translate' ) .'</strong></a>' ) );
			$links = array_merge( $links, array( '<a href="http://b-website.com/category/plugins" target="_blank" title="'. __( 'More b*web Plugins', 'an-translate' ) .'">'. __( 'More b*web Plugins', 'feedzy_rss_translate' ) .'</a>' ) );
			$links = array_merge( $links, array( '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7Z6YVM63739Y8" target="_blank" title="'. __( 'Donate', 'an-translate' ) .'"><strong>'. __( 'Donate', 'an-translate' ) .'</strong></a>' ) );
		}
		return $links;
	}
	add_filter( 'plugin_row_meta', 'feedzy_meta_links', 10, 2 );
}


/***************************************************************
 * Load plugin files
 ***************************************************************/
require_once( plugin_dir_path( __FILE__ ) . 'feedzy-rss-feeds-ui.php' );


/***************************************************************
 * Enqueue custom CSS
 ***************************************************************/
function feedzy_register_custom_style() {
	wp_register_style( 'feedzy-style', plugins_url('css/feedzy-rss-feeds.css', __FILE__ ), NULL, NULL);
}
function feedzy_print_custom_style() {
	global $feedzyStyle;
	if ( ! $feedzyStyle )
		return;

	wp_print_styles('feedzy-style');
}
add_action('init', 'feedzy_register_custom_style');
add_action('wp_footer', 'feedzy_print_custom_style');


/***************************************************************
 * Get an image from the feed
 ***************************************************************/
if (!function_exists('feedzy_returnImage')) {
	function feedzy_returnImage ($text) {
		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		$pattern = "/<img[^>]+\>/i";
		preg_match($pattern, $text, $matches);
		$text = $matches[0];
		return $text;
	}
}
 

/***************************************************************
 * Filter out image url which we got from previous returnImage() function
 ***************************************************************/
if (!function_exists('feedzy_scrapeImage')) {
	function feedzy_scrapeImage($text) {
		$pattern = '/src=[\'"]?([^\'" >]+)[\'" >]/';     
		preg_match($pattern, $text, $link);
		$link = $link[1];
		$link = urldecode($link);
		return $link;
	}
}


/***************************************************************
 * Main shortcode function
 ***************************************************************/
if (!function_exists('feedzy_rss')) {
	function feedzy_rss( $atts, $content="" ) {
		
		global $feedzyStyle;
		$feedzyStyle = true;
		//Retrieve & extract shorcode parameters
		extract(shortcode_atts(array(  
			"feeds" 				=> '',  		//comma separated feeds url
			"max" 					=> '5',			//number of feeds items (0 for unlimited)
			"feed_title"			=> 'yes',		//display feed title yes/no
			"target"				=> '_blank',	//_blank, _self
			"title"					=> '', 			//strip title after X char
			"meta"					=> 'yes', 		//yes, no
			"summary"				=> 'yes', 		//strip title
			"summarylength"			=> '', 			//strip summary after X char
			"thumb"					=> 'yes', 		//yes, no
			"default"				=> '',			//default thumb URL if no image found (only if thumb is set to yes)
			"size"					=> '',			//thumbs pixel size
			"keywords_title"		=> '' 			//only display item if title contains specific keywords (comma-separated list/case sensitive)
		), $atts));
		$count = 0;

		 if($max == '0'){
			$max = '999';
		} else if(empty($max) || !ctype_digit($max)) {
			$max = '5';
		} 
		
		if(empty($size) || !ctype_digit($size))
			$size = '150';

		if(!empty($title) && !ctype_digit($title))
			$title = '';
			
		if(!empty($keywords_title))
			$keywords_title = array_map('trim', explode(',', $keywords_title));

		if(!empty($summarylength) && !ctype_digit($summarylength))
			$summarylength = '';

		if(!empty($default)){
			$default = $default;
		} else {
			$default = plugins_url('img/feedzy-default.jpg', __FILE__ );
		}
		
		
		
		if (!class_exists('SimplePie'))
			require_once(ABSPATH . WPINC . '/class-feed.php');

		if (!empty ($feeds)) {
		
			$feedURL = explode(',',$feeds);
			$feedURL = array_splice($feedURL, 0, 3);
			if (count($feedURL) === 1) {
				$feedURL = $feedURL[0];
			};
	
		}
		 
		//Process SimplePie
		$feed = new SimplePie();
		$feed->set_feed_url($feedURL);
		$feed->enable_cache(true);
		$feed->enable_order_by_date(true);
		$feed->set_cache_class( 'WP_Feed_Cache' );
		$feed->set_file_class( 'WP_SimplePie_File' );
		$feed->set_cache_duration( apply_filters( 'wp_feed_cache_transient_lifetime', 7200, $feedURL ) );
		do_action_ref_array( 'wp_feed_options', array( $feed, $feedURL ) );
		$feed->strip_comments(true);
		$feed->strip_htmltags(false);
		$feed->init();
		$feed->handle_content_type();

		if ($feed->error()) {
		
			$content .= '<div id="message" class="error"><p>'. __( 'Sorry, this feed is currently unavailable or does not exists anymore.', 'feedzy_rss_translate' ) .'</p></div>';
	
		}

		$content .= '<div class="feedzy-rss">';
		
		if($feed_title == 'yes'){
			
			$content .= '<div class="rss_header">';
			$content .= '<h2><a href="'.  $feed->get_permalink() .'">'. $feed->get_title() .'</a> <span> '. $feed->get_description() .'</span></h2>';
			$content .= '</div>';
		
		}

		//Loop through RSS feed
		foreach ($feed->get_items() as $item){
			
			$continue = true;
			//Check if keywords are in title
			if(!empty($keywords_title)){
				$continue = false;
				foreach($keywords_title as $keyword){
					if (strpos($item->get_title(), $keyword) !== false) {
						$continue = true;
					}
				}
				
			}
			
			if($continue == true){

				//Count items
				if($count >= $max)
					break;
				$count++;
	
				//Fetch image thumbnail
				if($thumb == 'yes'){
					$thethumbnail = "";			

					
					if ($enclosure = $item->get_enclosure()) {
												
						//item thumb
						if ($thumbnail = $enclosure->get_thumbnail()){
								
								$thethumbnail = $thumbnail;	
						
						}								
						
						//media:thumbnail
						if(isset($enclosure->thumbnails)){

							foreach ((array) $enclosure->thumbnails as $thumbnail){
							
								$thethumbnail = $thumbnail;							
							
							}
						
						}
						
						//enclosure
						if( $thumbnail = $enclosure->embed() ) {
						
							$pattern= '/https?:\/\/.*\.(?:jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/iU';
							
							if (preg_match($pattern, $thumbnail, $matches)){
								
								$thethumbnail = $matches[0];
							
							}

						}		

						//media:content
						foreach ((array) $enclosure->get_link() as $thumbnail){
							
							$pattern= '/https?:\/\/.*\.(?:jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/iU';
							$imgsrc = $thumbnail;

							
							if ( preg_match($pattern, $imgsrc, $matches) ){
								
								$thethumbnail = $matches[0];;
								break;
							
							}
						
						}
					}
					
					//description image
					if(empty($thethumbnail)) {		
						
						$feedDescription = $item->get_description();
						$image = feedzy_returnImage($feedDescription);
						$thethumbnail = feedzy_scrapeImage($image);
					
					}
				
					//content image
					if(empty($thethumbnail)) {		
						
						$feedDescription = $item->get_content();
						$image = feedzy_returnImage($feedDescription);
						$thethumbnail = feedzy_scrapeImage($image);
					
					}
					
					
				}
				
				//Padding ratio based on image size
				$paddinTop = number_format((15/150)*$size,0);
				$paddinBottom = number_format((25/150)*$size,0);
				
				//Build element DOM
				$content .= '<div class="rss_item" style="padding: ' . $paddinTop . 'px 0 ' . $paddinBottom . 'px">';
				if($thumb == 'yes'){

					 if(!empty($thethumbnail)){
	
						$content .= '<a href="'.$item->get_permalink().'" class="rss_image" target="'. $target .'" style="width:'. $size .'px; height:'. $size .'px;" title="'.$item->get_title().'" >';
						$content .= '<span style="width:'. $size .'px; height:'. $size .'px; background-image:  none, url('.$thethumbnail.'), url('.$default.');" alt="'.$item->get_title().'"></span/>';
						$content .= '</a>';
					
					} else if(empty($thethumbnail)){
					
						$content .= '<a href="'.$item->get_permalink().'" class="rss_image" target="'. $target .'" style="width:'. $size .'px; height:'. $size .'px;" title="'.$item->get_title().'" >';
						$content .= '<span style="width:'. $size .'px; height:'. $size .'px; background-image:url('.$default.');" alt="'.$item->get_title().'"></span/>';
						$content .= '</a>';
				
					}
				}
				$content .= '<span class="title"><a href="'. $item->get_permalink() .'" target="'. $target .'">';
				if(is_numeric($title) && strlen($item->get_title()) > $title){
					
					$content .= preg_replace('/\s+?(\S+)?$/', '', substr($item->get_title(), 0, $title)) .'...';
				
				} else {
					
					$content .= $item->get_title();
			
				}
				$content .= '</a></span>';
				$content .= '<div class="rss_content">';
				if($meta == 'yes'){
					
					$content .= '<small>'. __( 'Posted by', 'feedzy_rss_translate' ) .' ';
					
					if ($author = $item->get_author()) {
						
						$domain = parse_url($item->get_permalink());
						$content .= '<a href="http://'. $domain["host"]. '" target="'. $target .'" title="'.$domain["host"].'" >'. $author->get_name() .' </a>';
					
					}
					
					$content .= __( 'on', 'feedzy_rss_translate' ) .' '. $item->get_date(get_option('date_format')) .' '. __( 'at', 'feedzy_rss_translate' ) .' '. $item->get_date(get_option('time_format'));
					$content .= '</small>';
				
				}
				if($summary == 'yes'){
					
					$content .= '<p>';
					$description = trim(strip_tags($item->get_description()));
					$description = trim(chop($description,'[&hellip;]')); 			
				
					if(is_numeric($summarylength) && strlen($description) > $summarylength){
					
						$content .= preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, $summarylength)) .' […]';
				
					} else {
					
						$content .= $description .' […]';
				
					}
					
						$content .= '</p>';
					
					}
					$content .= '</div>';
				$content .= '</div>';
		
			} //endContinue
			
		} //endforeach
		
		$content .= '</div>';
		return $content;
			
	} //end of feedzy_rss
	add_shortcode( 'feedzy-rss', 'feedzy_rss' );
}


/***************************************************************
 * Insert cover picture to main rss feed
 ***************************************************************/
if (!function_exists('feedzy_insert_thumbnail_RSS')) {
	function feedzy_insert_thumbnail_RSS($content) {
	     global $post;
		 
	     if ( has_post_thumbnail( $post->ID ) ){
	          $content = '' . get_the_post_thumbnail( $post->ID, 'thumbnail' ) . '' . $content;
	     }
		 
	     return $content;
	}
	add_filter('the_excerpt_rss', 'feedzy_insert_thumbnail_RSS');
	add_filter('the_content_feed', 'feedzy_insert_thumbnail_RSS');
}