=== FEEDZY RSS Feeds ===
Contributors: briKou
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7Z6YVM63739Y8
Tags: RSS, SimplePie, shortcode, feed, thumbnail, image, rss feeds, aggregator
Requires at least: 3.7
Tested up to: 4.0
Stable tag: 1.5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

 
FEEDZY RSS Feeds is a small and lightweight plugin. Fast and easy to use, it aggregates RSS feeds into your WordPress site through simple shortcodes.

== Description ==

FEEDZY RSS Feeds is a small and lightweight RSS aggregator plugin. Fast and very easy to use, it allows you to aggregate multiple RSS feeds into your WordPress site through fully customizable shortcodes. 

The plugin uses the SimplePie CLASS php natively included in WordPress. SimplePie is a RSS parser that can read the information contained in a feed, process it, and finally display it.

FEEDZY RSS Feeds therefore supports any additional library and uses only the bare minimum to ensure good performance (minimalistic CSS + cache). 

You may use this plugin in your widgets and your pages and reuse the shortcode several times within the same page.

By activating this plugin, your cover picture will be inserted into your RSS feeds. By doing so, you'll make it will easier for external sites to retrieve images from your feeds.

Plugin is now using the TinyMCE API to improve UI and makes it easy to insert shortcodes!


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


= Basic example =

`[feedzy-rss feeds="http://b-website.com/feed"]`


= Advanced example =

`[feedzy-rss feeds="http://b-website.com/feed" max="2" feed_title="yes" target="_blank" title="50" meta="yes" summary="yes" summarylength="300" thumb="yes" size="100" default="http://your-site/default-image.jpg" keywords_title="WordPress"]`



[FULL DOCUMENTATION AND EXAMPLES](http://b-website.com/feedzy-rss-feeds-wordpress-plugin-using-simplepie "Documentation & examples")
 


== Installation ==

1. Upload and activate the plugin (or install it through the WP admin console)
2. Insert shortcode ! ;-)

== Frequently Asked Questions ==

= Is it responsive friendly? =

Yes it is.


== Screenshots ==

1. Simple example
2. Inserting a shortcode in the WYSIWYG


== Changelog ==


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