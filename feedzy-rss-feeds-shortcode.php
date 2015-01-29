<?php
/***************************************************************
 * SECURITY : Exit if accessed directly
***************************************************************/
if ( !defined( 'ABSPATH' ) ) {
	die( 'Direct acces not allowed!' );
}


/***************************************************************
 * Get an image from the feed
 ***************************************************************/
function feedzy_returnImage ($text) {
	$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	$pattern = "/<img[^>]+\>/i";
	preg_match($pattern, $text, $matches);
	$text = $matches[0];
	return $text;
}
 

/***************************************************************
 * Filter out image url which we got from previous returnImage() function
 ***************************************************************/
function feedzy_scrapeImage($text) {
	$pattern = '/src=[\'"]?([^\'" >]+)[\'" >]/';     
	preg_match($pattern, $text, $link);
	$link = $link[1];
	$link = urldecode($link);
	return $link;
}


/***************************************************************
 * Main shortcode function
 ***************************************************************/
function feedzy_rss( $atts, $content = '' ) {

	global $feedzyStyle;
	$feedzyStyle = true;
	$count = 0;

	//Retrieve & extract shorcode parameters
	extract( shortcode_atts( array(
		"feeds" => '', 			//comma separated feeds url
		"max" => '5', 			//number of feeds items (0 for unlimited)
		"feed_title" => 'yes', 	//display feed title yes/no
		"target" => '_blank', 	//_blank, _self
		"title" => '', 			//strip title after X char
		"meta" => 'yes', 		//yes, no
		"summary" => 'yes', 	//strip title
		"summarylength" => '', 	//strip summary after X char
		"thumb" => 'yes', 		//yes, no
		"default" => '', 		//default thumb URL if no image found (only if thumb is set to yes)
		"size" => '', 			//thumbs pixel size
		"keywords_title" => '' 	//only display item if title contains specific keywords (comma-separated list/case sensitive)
		), $atts ) );

	if ( $max == '0' ) {
		$max = '999';
	} else if ( empty( $max ) || !ctype_digit( $max ) ) {
		$max = '5';
	}

	if ( empty( $size ) || !ctype_digit( $size ) ){
		$size = '150';
	}

	if ( !empty( $title ) && !ctype_digit( $title ) ){
		$title = '';
	}

	if ( !empty($keywords_title)){
		$keywords_title = array_map( 'trim', explode( ',', $keywords_title ) );
	}

	if ( !empty( $summarylength ) && !ctype_digit( $summarylength ) ){
		$summarylength = '';
	}

	if ( !empty( $default ) ) {
		$default = $default;
	
	} else {
		$default = plugins_url( 'img/feedzy-default.jpg', __FILE__ );
	}



	if ( !class_exists( 'SimplePie' ) ){
		require_once( ABSPATH . WPINC . '/class-feed.php' );
	}

	if ( !empty( $feeds ) ) {

		$feedURL = explode( ',', $feeds );
		$feedURL = array_splice( $feedURL, 0, 3 );
		if ( count( $feedURL ) === 1 ) {
			$feedURL = $feedURL[0];
		}
		
	}
	
	//Load SimplePie Instance
	$feed = new SimplePie();
	$feed->set_feed_url( $feedURL );
	$feed->enable_cache( true );
	$feed->enable_order_by_date( true );
	$feed->set_cache_class( 'WP_Feed_Cache' );
	$feed->set_file_class( 'WP_SimplePie_File' );
	$feed->set_cache_duration( apply_filters( 'wp_feed_cache_transient_lifetime', 7200, $feedURL ) );
	do_action_ref_array( 'wp_feed_options', array( $feed, $feedURL ) );
	$feed->strip_comments( true );
	$feed->strip_htmltags( false );
	$feed->init();
	$feed->handle_content_type();

	if ($feed->error()) {

		$content .= '<div id="message" class="error"><p>' . __('Sorry, this feed is currently unavailable or does not exists anymore.', 'feedzy_rss_translate') . '</p></div>';
	
	}

	$content .= '<div class="feedzy-rss">';

	if ($feed_title == 'yes') {

		$content .= '<div class="rss_header">';
		$content .= '<h2><a href="' . $feed->get_permalink() . '">' . $feed->get_title() . '</a> <span> ' . $feed->get_description() . '</span></h2>';
		$content .= '</div>';
		
	}

	//Loop through RSS feed
	foreach ( $feed->get_items() as $item ) {

		$continue = true;
		//Check if keywords are in title
		if ( !empty( $keywords_title ) ) {
			$continue = false;
			foreach ( $keywords_title as $keyword ) {
				if ( strpos( $item->get_title(), $keyword ) !== false ) {
					$continue = true;
				}
			}
		}

		if ( $continue == true ) {

			//Count items
			if ( $count >= $max ){
				break;
			}
			$count++;

			//Fetch image thumbnail
			if ( $thumb == 'yes' ) {
				$thethumbnail = "";


				if ( $enclosures = $item->get_enclosures() ) {
					
					foreach( (array) $enclosures as $enclosure ){

						//item thumb
						if ( $thumbnail = $enclosure->get_thumbnail() ) {
							$thethumbnail = $thumbnail;
						}
	
						//media:thumbnail
						if ( isset( $enclosure->thumbnails ) ) {
	
							foreach ( (array) $enclosure->thumbnails as $thumbnail ) {
								$thethumbnail = $thumbnail;
							}
							
						}
	
						//enclosure
						if ( $thumbnail = $enclosure->embed() ) {
	
							$pattern = '/https?:\/\/.*\.(?:jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/iU';
	
							if ( preg_match( $pattern, $thumbnail, $matches ) ) {
								$thethumbnail = $matches[0];
							}
							
						}
	
						//media:content
						foreach ( (array) $enclosure->get_link() as $thumbnail ) {
	
							$pattern = '/https?:\/\/.*\.(?:jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/iU';
							$imgsrc = $thumbnail;
	
	
							if ( preg_match( $pattern, $imgsrc, $matches ) ) {
								$thethumbnail = $matches[0];
								break;
							}
							
						}
						
						//break loop if thumbnail found
						if ( ! empty( $thethumbnail ) ) {
							break;
						}

					}
					
				}


				//content image
				if ( empty( $thethumbnail ) ) {

					$feedDescription = $item->get_content();
					$image = feedzy_returnImage( $feedDescription );
					$thethumbnail = feedzy_scrapeImage( $image );
					
				}

				//description image
				if ( empty( $thethumbnail ) ) {
					
					$feedDescription = $item->get_description();
					$image = feedzy_returnImage( $feedDescription );
					$thethumbnail = feedzy_scrapeImage( $image );
				
				}
				
			}

			//Padding ratio based on image size
			$paddinTop = number_format( (15 / 150) * $size, 0 );
			$paddinBottom = number_format( (25 / 150) * $size, 0 );

			//Build element DOM
			$content .= '<div class="rss_item" style="padding: ' . $paddinTop . 'px 0 ' . $paddinBottom . 'px">';
			
			if ( $thumb == 'yes' ) {
				
				$contentThumb = '';
				
				$contentThumb .= '<div class="rss_image" style="width:' . $size . 'px; height:' . $size . 'px;">';
				$contentThumb .= '<a href="' . $item->get_permalink() . '" target="' . $target . '" title="' . $item->get_title() . '" >';

				if ( !empty( $thethumbnail  )) {
					
					$contentThumb .= '<span style="width:' . $size . 'px; height:' . $size . 'px; background-image:  none, url(' . $thethumbnail . '), url(' . $default . ');" alt="' . $item->get_title() . '"></span/>';
				
				} else if ( empty( $thethumbnail ) ) {
				
					$contentThumb .= '<span style="width:' . $size . 'px; height:' . $size . 'px; background-image:url(' . $default . ');" alt="' . $item->get_title() . '"></span/>';
				
				}
				
				$contentThumb .= '</a>';
				$contentThumb .= '</div>';

				//Filter: feedzy_thumb_output
				$content .= apply_filters( 'feedzy_thumb_output', $contentThumb, $feedURL );
				
			}
			
			$contentTitle = '';
			$contentTitle .= '<span class="title"><a href="' . $item->get_permalink() . '" target="' . $target . '">';
		   
			if ( is_numeric( $title ) && strlen( $item->get_title() ) > $title ) {

				$contentTitle .= preg_replace( '/\s+?(\S+)?$/', '', substr( $item->get_title(), 0, $title ) ) . '...';
			
			} else {

				$contentTitle .= $item->get_title();
			
			}
			
			$contentTitle .= '</a></span>';

			//Filter: feedzy_title_output
			$content .= apply_filters( 'feedzy_title_output', $contentTitle, $feedURL );

			$content .= '<div class="rss_content">';

			
			//Define Meta args
			$metaArgs = array(
						'author' => true,
						'date' => true,
						'date_format' => get_option( 'date_format' ),
						'time_format' => get_option( 'time_format' )
					);
					
			//Filter: feedzy_meta_args
			$metaArgs = apply_filters( 'feedzy_meta_args', $metaArgs, $feedURL );

			if ( $meta == 'yes' && ( $metaArgs[ 'author' ] || $metaArgs[ 'date' ] ) ) {

				$contentMeta = '';
				$contentMeta .= '<small>' . __( 'Posted', 'feedzy_rss_translate' ) . ' ';

				if ( $item->get_author() && $metaArgs[ 'author' ] ) {
					
					$author = $item->get_author();
					$domain = parse_url( $item->get_permalink() );
					$contentMeta .= __( 'by', 'feedzy_rss_translate' ) . ' <a href="http://' . $domain[ 'host' ] . '" target="' . $target . '" title="' . $domain[ 'host' ] . '" >' . $author->get_name() . '</a> ';

				}
				
				if ( $metaArgs[ 'date' ] ) {
					$contentMeta .= __( 'on', 'feedzy_rss_translate') . ' ' . date_i18n( $metaArgs[ 'date_format' ], $item->get_date( 'U' ) ) . ' ' . __( 'at', 'feedzy_rss_translate' ) . ' ' . date_i18n( $metaArgs[ 'time_format' ], $item->get_date( 'U' ) );
				}
				
				$contentMeta .= '</small>';
				
				//Filter: feedzy_meta_output
				$content .= apply_filters( 'feedzy_meta_output', $contentMeta, $feedURL );

			}
			if ( $summary == 'yes' ) {


				$contentSummary = '';
				$contentSummary .= '<p>';

				//Filter: feedzy_summary_input
				$description = $item->get_description();
				$description = apply_filters( 'feedzy_summary_input', $description, $item->get_content(), $feedURL );

				if ( is_numeric( $summarylength ) && strlen( $description ) > $summarylength ) {

					$contentSummary .= preg_replace( '/\s+?(\S+)?$/', '', substr( $description, 0, $summarylength ) ) . ' […]';
				
				} else {

					$contentSummary .= $description . ' […]';
				}

				$contentSummary .= '</p>';

				//Filter: feedzy_summary_output
				$content .= apply_filters( 'feedzy_summary_output', $contentSummary, $item->get_permalink(), $feedURL );

			}
			
			$content .= '</div>';
			$content .= '</div>';
			
		} //endContinue
		
	} //endforeach

	$content .= '</div>';
	return apply_filters( 'feedzy_global_output', $content, $feedURL );
	
}//end of feedzy_rss
add_shortcode( 'feedzy-rss', 'feedzy_rss' );


/***************************************************************
 * Filter feed description input
 ***************************************************************/
function feedzy_summary_input_filter( $description, $content, $feedURL ) {
	$description = trim( strip_tags( $description ) );
	$description = trim( chop( $description, '[&hellip;]' ) );
 
    return $description;
}
add_filter('feedzy_summary_input', 'feedzy_summary_input_filter', 9, 3);	