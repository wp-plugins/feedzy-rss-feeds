/**
 * Plugin Name: FEEDZY RSS Feeds by b*web
 * Plugin URI: http://b-website.com/
 * Author: Brice CAPOBIANCO
 */
(function() {
	tinymce.PluginManager.add('feedzy_mce_button', function( editor, url ) {
		editor.addButton( 'feedzy_mce_button', {
			icon: 'feedzy-icon',
			onclick: function() {
				editor.windowManager.open( {
					title: 'Insert FEEDZY RSS Feeds Shortcode',
					body: [
						{
							type: 'textbox',
							name: 'feeds',
							label: 'The feed(s) URL (comma-separated list)',
							value: ''
						},
						{
							type: 'textbox',
							name: 'maximum',
							label: 'Number of items to display.',
							value: ''
						},
						{
							type: 'listbox',
							name: 'feed_title',
							label: 'Should we display the RSS title ?',
							'values': [
								{text: 'Do not specify', value: ''},
								{text: 'No', value: 'no'},
								{text: 'Yes', value: 'yes'},
							]
						},
						{
							type: 'listbox',
							name: 'target',
							label: 'Links may be opened in the same window or a new tab.',
							'values': [
								{text: 'Do not specify', value: ''},
								{text: '_blank', value: '_blank'},
								{text: '_self', value: '_self'},
								{text: '_parent', value: '_parent'},
								{text: '_top', value: '_top'},
								{text: 'framename', value: 'framename'}
							]
						},
						{
							type: 'textbox',
							name: 'title',
							label: 'Trim the title of the item after X characters.',
							value: ''
						},
						{
							type: 'listbox',
							name: 'meta',
							label: 'Should we display the date of publication and the author name?',
							'values': [
								{text: 'Do not specify', value: ''},
								{text: 'No', value: 'no'},
								{text: 'Yes', value: 'yes'},
							]
						},
						{
							type: 'listbox',
							name: 'summary',
							label: 'Should we display a description (abstract) of the retrieved item?',
							'values': [
								{text: 'Do not specify', value: ''},
								{text: 'No', value: 'no'},
								{text: 'Yes', value: 'yes'},
							]
						},
						{
							type: 'textbox',
							name: 'summarylength',
							label: 'Crop description (summary) of the element after X characters.',
							value: ''
						},
						{
							type: 'listbox',
							name: 'thumb',
							label: 'Should we display the first image of the content if it is available?',
							'values': [
								{text: 'Do not specify', value: ''},
								{text: 'No', value: 'no'},
								{text: 'Yes', value: 'yes'},
							]
						},
						{
							type: 'textbox',
							name: 'defaultimg',
							label: 'Default thumbnail URL if no image is found. ',
							value: ''
						},
						{
							type: 'textbox',
							name: 'size',
							label: 'Thumblails dimension. Do not include "px". Eg: 150',
							value: ''
						},
						{
							type: 'textbox',
							name: 'keywords_title',
							label: 'Only display item if title contains specific keyword(s) (comma-separated list/case sensitive).',
							value: ''
						}
					],
					onsubmit: function( e ) {
						if(e.data.feeds != ''){
							e.data.feeds = 'feeds="' + e.data.feeds + '" ';
						} else {
							e.data.feeds = 'feeds="http://b-website.com/feed" ';
						}
						if(e.data.maximum != ''){
							e.data.maximum = 'max="' + e.data.maximum + '" ';
						}
						if(e.data.feed_title != ''){
							e.data.feed_title = 'feed_title="' + e.data.feed_title + '" ';
						}
						if(e.data.target != ''){
							e.data.target = 'target="' + e.data.target + '" ';
						}
						if(e.data.title != ''){
							e.data.title = 'title="' + e.data.title + '" ';
						}
						if(e.data.meta != ''){
							e.data.meta = 'meta="' + e.data.meta + '" ';
						}
						if(e.data.summary != ''){
							e.data.summary = 'summary="' + e.data.summary + '" ';
						}
						if(e.data.summarylength != ''){
							e.data.summarylength = 'summarylength="' + e.data.summarylength + '" ';
						}
						if(e.data.thumb != ''){
							e.data.thumb = 'thumb="' + e.data.thumb + '" ';
						}
						if(e.data.defaultimg != ''){
							e.data.defaultimg = 'default="' + e.data.defaultimg + '" ';
						}
						if(e.data.size != ''){
							e.data.size = 'size="' + e.data.size + '" ';
						}
						if(e.data.keywords_title != ''){
							e.data.keywords_title = 'keywords_title="' + e.data.keywords_title + '" ';
						}
						editor.insertContent( 
							'[feedzy-rss '
								+ e.data.feeds
								+ e.data.maximum
								+ e.data.feed_title
								+ e.data.target
								+ e.data.title
								+ e.data.meta
								+ e.data.summary
								+ e.data.summarylength
								+ e.data.thumb
								+ e.data.defaultimg
								+ e.data.size
								+ e.data.keywords_title
							+ ']'
						);
					}
				});
			}
		});
	});
})();