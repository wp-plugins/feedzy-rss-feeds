<?php
/***************************************************************
 * Enqueue feedzy CSS
 ***************************************************************/
function feedzy_register_custom_style() {
	wp_register_style( 'feedzy-style', plugins_url('css/feedzy-rss-feeds.css', __FILE__ ), NULL, NULL );
}
function feedzy_print_custom_style() {
	global $feedzyStyle;
	if ( !$feedzyStyle )
		return;

	wp_print_styles( 'feedzy-style' );
}
add_action( 'init', 'feedzy_register_custom_style' );
add_action( 'wp_footer', 'feedzy_print_custom_style' );


/***************************************************************
 * Filter feed description input
 ***************************************************************/
function feedzy_summary_input_filter( $description, $content, $feedURL ) {
	$description = trim( strip_tags( $description ) );
	$description = trim( chop( $description, '[&hellip;]' ) );
 
    return $description;
}
add_filter('feedzy_summary_input', 'feedzy_summary_input_filter', 9, 3);	


/***************************************************************
 * Check if keywords are in title
 ***************************************************************/
function feedzy_feed_item_keywords_title( $continue, $keywords_title, $item, $feedURL ){
	if ( !empty( $keywords_title ) ) {
		$continue = false;
		foreach ( $keywords_title as $keyword ) {
			if ( strpos( $item->get_title(), $keyword ) !== false ) {
				$continue = true;
			}
		}
	}
	return $continue;
}
add_filter('feedzy_item_keyword', 'feedzy_feed_item_keywords_title', 9, 4); 


/***************************************************************
 * Insert cover picture to main rss feed content
 ***************************************************************/
function feedzy_insert_thumbnail_RSS( $content ) {
	 global $post;
	 if ( has_post_thumbnail( $post->ID ) ){
		  $content = '' . get_the_post_thumbnail( $post->ID, 'thumbnail' ) . '' . $content;
	 }
	 return $content;
}
add_filter( 'the_excerpt_rss', 'feedzy_insert_thumbnail_RSS' );
add_filter( 'the_content_feed', 'feedzy_insert_thumbnail_RSS' );


/***************************************************************
 * Include cover picture (medium) to rss feed enclosure 
 * and media:content
 ***************************************************************/
function feedzy_include_thumbnail_RSS (){
	 global $post;
	 
	 if ( has_post_thumbnail( $post->ID ) ){
		 
		$postThumbnailId = get_post_thumbnail_id( $post->ID );
		$attachmentMeta = wp_get_attachment_metadata( $postThumbnailId );
		$imageUrl = wp_get_attachment_image_src( $postThumbnailId, 'medium' );
		
		echo '<enclosure url="' . $imageUrl[0] . '" length="' . filesize( get_attached_file( $postThumbnailId ) ) . '" type="image/jpg" />';				
		echo '<media:content url="' . $imageUrl[0] . '" width="' . $attachmentMeta['sizes']['medium']['width'] . '" height="' . $attachmentMeta['sizes']['medium']['height'] . '" medium="image" type="' . $attachmentMeta['sizes']['medium']['mime-type'] . '" />';
	
	}
}
//add_action('rss_item', 'feedzy_include_thumbnail_RSS');
//add_action('rss2_item', 'feedzy_include_thumbnail_RSS');