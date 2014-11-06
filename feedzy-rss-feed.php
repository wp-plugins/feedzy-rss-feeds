<?php
/**
 * Plugin Name: FEEDZY RSS Feeds by b*web
 * Plugin URI: http://b-website.com/feedzy-rss-feeds-plugin-wordpress-gratuit-utilisant-simplepie
 * Description: FEEDZY RSS Feeds is a small and lightweight plugin. Fast and easy to use, it aggregates RSS feeds into your WordPress site through simple shortcodes.				
 * Author: Brice CAPOBIANCO
 * Author URI: http://b-website.com/
 * Version: 1.02
 * Text Domain: feedzy_rss_translate
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//Load plugin textdomain
if (!function_exists('feedzy_rss_load_textdomain')) {
	function feedzy_rss_load_textdomain() {
		$path = dirname(plugin_basename( __FILE__ )) . '/langs/';
		$loaded = load_plugin_textdomain( 'feedzy_rss_translate', false, $path);
	}
	add_action('init', 'feedzy_rss_load_textdomain');
}


//Enqueue custom CSS
function register_feedzy_custom_style() {
	wp_register_style( 'feedzy-CSS', plugins_url('/feedzy-rss-style.css', __FILE__ ), NULL, NULL);
}
function print_feedzy_custom_style() {
	global $enqueueStyle;
	if ( ! $enqueueStyle )
		return;

	wp_print_styles('feedzy-CSS');
}
add_action('wp_footer', 'print_feedzy_custom_style');
add_action('init', 'register_feedzy_custom_style');


//This function will get an image from the feed
if (!function_exists('returnImage')) {
	function returnImage ($text) {
		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		$pattern = "/<img[^>]+\>/i";
		preg_match($pattern, $text, $matches);
		$text = $matches[0];
		return $text;
	}
}
 

//This function will filter out image url which we got from previous returnImage() function
if (!function_exists('scrapeImage')) {
	function scrapeImage($text) {
		$pattern = '/src=[\'"]?([^\'" >]+)[\'" >]/';     
		preg_match($pattern, $text, $link);
		$link = $link[1];
		$link = urldecode($link);
		return $link;
	}
}


//Main shortcode function
if (!function_exists('feedzy_rss')) {
	function feedzy_rss( $atts, $content="" ) {
		
		global $enqueueStyle;
		$enqueueStyle = true;
	
		//Retrieve & extract shorcode parameters
		extract(shortcode_atts(array(  
			"feeds" 				=> '',  		//comma separated feeds url
			"max" 					=> '5',			//number of feeds items
			"feed_title"			=> 'yes',		//display feed title true/false
			"target"				=> '_blank',	//_blank, _self
			"title"					=> '', 			//strip title after X char
			"meta"					=> 'yes', 		//yes, no
			"summary"				=> 'yes', 		//strip title
			"summarylength"			=> '', 			//strip summary after X char
			"thumb"					=> 'yes', 		//yes, no
			"size"					=> '150'		//thumbs pixel size
		), $atts));
		$count = 0;

		if(!is_numeric($size))
			$size = '150';

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
			
			if($count >= $max)
				break;
			$count++;

			//Fetch image thumbnail
			if($thumb == 'yes'){
				$thethumbnail = "";			
				if ($enclosure = $item->get_enclosure()) {
					foreach ((array) $enclosure->get_link() as $thumbnail){
						$pattern= '/https?:\/\/.*\.(?:jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/iU';
						$imgsrc = $thumbnail;
						preg_match($pattern, $imgsrc, $matches);
						$thumbnail = $matches[0];
						if (!empty($thumbnail)){
							$thethumbnail = $thumbnail;
							break;
						}
					}
				}
				if(empty($thethumbnail)) {		
					$feedDescription = $item->get_description();
					$image = returnImage($feedDescription);
					$thethumbnail = scrapeImage($image);
				}
				if(empty($thethumbnail)) {		
					$feedDescription = $item->get_content();
					$image = returnImage($feedDescription);
					$thethumbnail = scrapeImage($image);
				}
			}
			//Build element DOM
			$content .= '<div class="rss_item">';
			if($thumb == 'yes'){
				if(!empty($thethumbnail)) { 
					$content .= '<a href="'.$item->get_permalink().'" class="rss_image" target="'. $target .'" style="width:'. $size .'px; height:'. $size .'px;" title="'.$item->get_title().'" >';
					$content .= '<span style="width:'. $size .'px; height:'. $size .'px; background-image:url('.$thethumbnail.');" alt="'.$item->get_title().'"></span/></a>';
				} else {
					$content .= '<span class="rss_image" style="width:'. $size .'px; height:'. $size .'px;" /></span>';
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
		
		} //endforeach
		
		$content .= '</div>';
		return $content;
			
	} //end of feedzy_rss
	add_shortcode( 'feedzy-rss', 'feedzy_rss' );
}


//Insert cover picture to main rss feed
if (!function_exists('insertThumbnailRSS')) {
	function insertThumbnailRSS($content) {
	     global $post;
	     if ( has_post_thumbnail( $post->ID ) ){
	          $content = '' . get_the_post_thumbnail( $post->ID, 'thumbnail' ) . '' . $content;
	     }
	     return $content;
	}
	add_filter('the_excerpt_rss', 'insertThumbnailRSS');
	add_filter('the_content_feed', 'insertThumbnailRSS');
}