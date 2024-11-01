=== TweetiePie ===
Contributors: elightbo
Donate link: https://www.paypal.com/us/cgi-bin/webscr?cmd=_flow&SESSION=zWriwjSwuVmzCp3soYGx7lxFV81GHVS6LCBm0PLpEYSuGeuzXpM_ZSM3SbG&dispatch=5885d80a13c0db1ffc45dc241d84e9538c532da79baccf7c1009429e47706c4e
Tags: twitter, sidebar, widget
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 1.0.4

Outputs latest twitter updates and linkifies hash tags and @replies.  Properly caches.

== Description ==

This twitter plugin outputs your twitter updates to your WordPress blog. It linkifies hash tags and @replies while properly caching.  It is called TweetiePie because it uses the [SimplePie WordPress plugin](http://wordpress.org/extend/plugins/simplepie-plugin-for-wordpress/ "SimplePie") to pull the tweets from twitter and cache them.  Caching is necessary to minimize the times that your blog has to talk to twitter.  This will speed up your site and [keep twitter happy](http://apiwiki.twitter.com/Rate-limiting "Rate limiting on twitter").  

This plugin requires WordPress 2.8 or greater.  If using an earlier 2.8 version it also requires the [SimplePie WordPress plugin](http://wordpress.org/extend/plugins/simplepie-plugin-for-wordpress/ "SimplePie"). 


== Installation ==

1. Upload the tweetie-pie folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Please make sure that the cache directory has the proper permissions ( typically 775 ).  If you don't notice any files in the cache directory after installing your plugin and viewing the blog, most likely your permissions aren't set properly on this directory.

== Frequently Asked Questions ==

= Do I need SimplePie? =

Not if you are using a newer version of WordPress.  Later 2.8 versions of WordPress include SimplePie so a plugin is not required.  If you are using an older version (why?) then download and install the SimplePie core plugin.

= How do I use the direct function call? =

A direct function call looks something like this:

`<?php gettweet_tp("elightbo", 2, true, true)?>`

This will show two of elightbo's tweets with a time-stamp that links to the tweet on twitter's website.  The most important thing to keep in mind when using the function call is the order of arguments.  Here is the order:

1. Your twitter username
1. Maximum number of latest tweets to display (default 1)	
1. Show time-stamp of tweet
1. Link to tweet from time-stamp
1. Text to append before tweets. (default `<ul class="tweetie-pie">`)
1. Text to append after tweets. (default `</ul>`)
1. Text to append before individual tweet ( default `<li class="tweet">` )
1. Text to append after individual tweet ( default `</li>`)
1. Text to separate tweets. (default '')

The defaults are used if nothing is supplied to the function.  So in this example my tweets would be displayed in an unordered list.

== Screenshots ==

1. Widget page showing TweetiePie
2. TweetiePie in action

== Changelog ==

= 1.0 =
* TweetiePie Launch
= 1.0.2 =
* Changed cache duration from 1 minute to 15 minutes since twitter's API allows for 150 requests per hour.
= 1.0.3 = 
* Fixed bug which was occasionally showing blank tweets.
= 1.0.4 =
* PHP 5.2+ Compatibility
* Works without SimplePie core plugin for newer versions of WordPress