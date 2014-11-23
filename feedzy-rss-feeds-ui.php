<?php
/***************************************************************
 * Hooks custom TinyMCE button function
 ***************************************************************/ 
function feedzy_add_mce_button() {

	if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) )
	return;
	
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		
		add_filter( 'mce_external_plugins', 'feedzy_add_tinymce_plugin' );
		add_filter( 'mce_buttons', 'feedzy_register_mce_button' );
		
		// Load stylesheet for tinyMCE button only
		wp_enqueue_style( 'feedzy-rss-feeds', plugin_dir_url( __FILE__ ) . 'css/feedzy-rss-feeds.css', array(), NULL, NULL);
		
	}
	
}
add_action('admin_head', 'feedzy_add_mce_button');


/***************************************************************
 * Load custom js options - TinyMCE API
 ***************************************************************/ 
function feedzy_add_tinymce_plugin( $plugin_array ) {
	$plugin_array['feedzy_mce_button'] = plugin_dir_url( __FILE__ ) . '/js/feedzy-rss-feeds-ui.js';
	return $plugin_array;
}


/***************************************************************
 * Register new button in the editor
 ***************************************************************/ 
function feedzy_register_mce_button( $buttons ) {
	array_push( $buttons, 'feedzy_mce_button' );
	return $buttons;
}