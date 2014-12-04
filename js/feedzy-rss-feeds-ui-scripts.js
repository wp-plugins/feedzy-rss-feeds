/**
 * Plugin Name: FEEDZY RSS Feeds by b*web
 * Plugin URI: http://b-website.com/
 * Author: Brice CAPOBIANCO
 */
jQuery(document).ready(function($) {
	
	function feedzyMediaLibrary(){
		$('i.mce-i-feedzy-icon').live('click', function(){
				setTimeout(function() {
				$('.mce-feedzy-media').after( "<span class='mce-feedzy-media-button'>+</span>" );
			}, 100);
		});
		
		$('.mce-feedzy-media-button').live('click', function(){
			var $this = $(this);
			 var wireframe;
			 if (wireframe) {
				 wireframe.open();
				 return;
			 }
	
			 wireframe = wp.media.frames.wireframe = wp.media({
				 /*title: 'Media Library Title',
				 button: {
					 text: 'Media Library Button Title'
				 },*/
				 multiple: false
			 });
	
			 wireframe.on('select', function() {
				attachment = wireframe.state().get('selection').first().toJSON();
				$this.parent().find('.mce-feedzy-media').val(attachment.url);
			 });
	
			 wireframe.open();
		});
	};
	feedzyMediaLibrary();
							
});