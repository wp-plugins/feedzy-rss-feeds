=== FEEDZY RSS Feeds ===
Contributors: briKou
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7Z6YVM63739Y8
Tags: RSS, SimplePie, shortcode, feed, thumbnail, image, rss feeds, aggregator, tinyMCE, WYSIWYG, MCE, UI, flux, plugin, WordPress
Requires at least: 3.7
Tested up to: 4.1
Stable tag: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

 
FEEDZY RSS Feeds is a small & lightweight plugin. Fast and easy to use, it aggregates RSS feeds into your site with shortcodes & widgets.

== Description ==

FEEDZY RSS Feeds is a small and lightweight RSS aggregator plugin. Fast and very easy to use, it allows you to aggregate multiple RSS feeds into your WordPress site through fully customizable shortcodes & widgets.

The plugin uses the SimplePie php CLASS natively included in WordPress. SimplePie is a RSS parser that can read the information contained in a feed, process it, and finally display it.

FEEDZY RSS Feeds therefore supports any additional library and uses only the bare minimum to ensure good performance (minimalistic CSS + cache). 

You may use this plugin in your widgets and your pages and reuse the shortcode + widget several times within the same page.

By activating this plugin, your cover picture will be inserted into your RSS feeds. By doing so, you'll make it will easier for external sites to retrieve images from your feeds.


[CHECK OUT THE DEMO](http://b-website.com/feedzy-rss-feeds-wordpress-plugin-using-simplepie "Try It!")


**Please ask for help or report bugs if anything goes wrong. It is the best way to make the community benefit!**


 = Shortcode Parameters =

* feeds
* max
* feed_title
* target
* title
* meta
* summary
* summarylength
* thumb
* default
* size
* keywords_title

**Plugin is now using the TinyMCE API to improve UI and makes it easy to insert shortcodes!**


= Basic example =

`[feedzy-rss feeds="http://b-website.com/feed"]`


= Advanced example =

`[feedzy-rss feeds="http://b-website.com/feed" max="2" feed_title="yes" target="_blank" title="50" meta="yes" summary="yes" summarylength="300" thumb="yes" size="100" default="http://your-site/default-image.jpg" keywords_title="WordPress"]`


= Available Hooks =

* feedzy_thumb_output
* feedzy_title_output
* feedzy_meta_output
* feedzy_summary_output
* feedzy_global_output


[FULL DOCUMENTATION AND EXAMPLES](http://b-website.com/feedzy-rss-feeds-wordpress-plugin-using-simplepie "Documentation & examples")
 
= Languages =

* English
* French
* Serbian [Borisa Djuraskovic](http://www.webhostinghub.com/ "Borisa Djuraskovic")

Become a translator and send me your translation! [Contact-me](http://b-website.com/contact "Contact")


== Installation ==

1. Upload and activate the plugin (or install it through the WP admin console)
2. Insert shortcode ! ;-)

== Frequently Asked Questions ==

= Is it responsive friendly? =

Yes it is.


== Screenshots ==

1. Simple example
2. Inserting a shortcode in the WYSIWYG
3. Widget admin
4. Widget render


== Changelog ==

= 2.0 =
* Widget added
* Translation update
* Better plugin file structure
* Improve image fetching with multiple enclosures
* Tested on WP 4.1 with success!

= 1.7.1 =
* Fix typo in PHP which cause issue on fetching images

= 1.7 =
* Minor Template and CSS changes
* New hook: feedzy_thumb_output
* New hook: feedzy_title_output
* New hook: feedzy_meta_output
* New hook: feedzy_summary_output
* New hook: feedzy_global_output
* readme.txt update

= 1.6 =
* Minor CSS fix
* Add actions: add_action('rss_item', 'feedzy_include_thumbnail_RSS'); & add_action('rss2_item', 'feedzy_include_thumbnail_RSS')

= 1.5.4 =
* Plugin meta translation
* Remove unnecessary spaces

= 1.5.3 =
* TinyMCE UI translation
* Better fetching image
* Space between items is calculated based on thumbs size

= 1.5.2 =
* Plugin meta update

= 1.5.1 =
* New logo
* Minor CSS fixes

= 1.5 =
* New param added to filter item with keywords
* Default thumb added
* Fix minor php issue
* Rename files of the plugin
* New logo + screenshot (assets)

= 1.4 =
* Add "default" parameter to fill image container if no image is fetch or if it is offline
* Add more control over numeric format in max, size, title & summarylength parameters

= 1.03 =
* Shortcode can now be displayed everywhere in the page (CSS is loaded via global var)

= 1.02 =
* Error on svn tag

= 1.01 =
* Minor CSS fix.
* Minor PHP changes.
* Readme.txt updated

= 1.0 =
* First release.

== Upgrade Notice ==

= 1.5 =
* IMPORTANT: You have to reactivate the plugin after its update!

= 1.0 =
* First release.