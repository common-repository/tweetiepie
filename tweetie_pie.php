<?php
/*
Plugin Name: TweetiePie
Plugin URI: http://ericlightbody.com/tweetiepie
Description: Outputs twitter updates.  Can be used with direct function call or as a widget.  Requires <a href="http://wordpress.org/extend/plugins/simplepie-plugin-for-wordpress/" title="Simplepie Wordpress Plugin">simplie-pie wordpress plugin</a> to retrieve and to cache.
Version: 1.0.4
Author: Eric Lightbody
Author URI: http://www.ericlightbody.com/
*/

/*  Copyright 2009 Eric Lightbody  (email : eric@ericlightbody.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
Arguments:
	$username - Your twitter username
	$count - Maximum number of latest tweets to display (default 1)	
	$show_time - Show timestamp of tweet
	$link_time - Link to tweet from timestamp
	$before_all_tweets - Text to append before tweets. (default <ul class="tweetie-pie">)
	$after_all_tweets -  Text to append after tweets. (default </ul>)
	$before_tweet - Text to append before individual tweet ( default <li class="tweet"> )
	$after_tweet - Text to apppend after individual tweet ( default </li>)
	$between_tweets - Text to separate tweets. (default '')
*/

function gettweet_tp(
	$username = '',
	$count=1,
	$show_time=true,
	$link_time=true,
	$before_all_tweets = '<ul class="tweetie-pie">',
	$after_all_tweets = '</ul>',
	$before_tweet='<li class="tweet">',
	$after_tweet='</li>',
	$between_tweets='') {

	if ( !class_exists('SimplePie') && !function_exists('fetch_feed') ) //check if simplepie is in the wordpress core or if plugin is installed
	{
		echo 'This plugin requires the simplepie core.  You can get this by upgrading to the latest version of WordPress, or, if you are on an older version of 2.8, you can use the <a href="http://wordpress.org/extend/plugins/simplepie-core">SimplePie Core plugin</a>.  Please download, install, and activate it, or upgrade the plugin if you\'re not using the latest version.';
	}
	else
	{
		if (!SIMPLEPIE_BUILD >= 20080102221556) // SimplePie 1.1
		{
			echo 'This plugin requires a newer version of the <a href="http://wordpress.org/extend/plugins/simplepie-core">SimplePie Core</a> plugin to enable important functionality. Please upgrade the plugin to the latest version.';
		}
		else //simplepie is loaded and at least a somewhat-recent version
		{
			if ( function_exists('fetch_feed') ) //if simplepie is part of the core
			{
				$feed = fetch_feed('http://search.twitter.com/search.atom?q=+from%3A' . $username);
			}
			else //if plugin is being used
			{
				$feed = new SimplePie();
				$feed->set_feed_url('http://search.twitter.com/search.atom?q=+from%3A' . $username);
			}
			$feed = new SimplePie();
			$feed->set_feed_url('http://search.twitter.com/search.atom?q=+from%3A' . $username);
			$feed->enable_cache(true);
			$feed->set_cache_duration(60*15);
			$feed->set_cache_location( dirname(__FILE__) . '/cache' ); //set cache to be within this plugin's directory
			$feed->init();	
			
			$tweet_group = $before_all_tweets;
			$itemCounter = 0;
			foreach ( $feed->get_items() as $item )
			{
				$itemCounter++;
				$tweet_group .= $before_tweet;
				$tweet = $item->get_description();
				$tweet = preg_replace("/(^" . $username .": )(.{1,})/i", "$2", $tweet); //remove username from beginning of tweet
				$tweet = add_hyperlinks_tp($tweet);
				$tweet_group .= $tweet;
				
				if ( $show_time == true ) {
					$tweet_stamp = strtotime( str_replace(",", "", $item->get_date() ) ); //remove comma for php 4 and convert to time
					$tweet_link = '';

					if ( ( abs( time() - $tweet_stamp) ) < 86400 )
						$h_time = sprintf( __('%s ago'), human_time_diff( $tweet_stamp ) );
					else
						$h_time = date(__('Y/m/d'), $tweet_stamp);
					
					if ( $link_time == true )
						$tweet_link = '<a class="tweet_link" href="' . $item->get_link() . '">';
					
					$tweet_group .= ' <span class="tweetiepie_timestamp">' . $tweet_link . '<abbr title="' . date(__('Y/m/d H:i:s'), $tweet_stamp) . '">' . $h_time . '</abbr>';
					if ( $link_time == true ) 
						$tweet_group .= "</a>";
					$tweet_group .= '</span>';
				}
				
				$tweet_group .= $after_tweet;
				if ( $item != end($feed->get_items())) {
					$tweet_group .= $between_tweets;
				}
				if ($itemCounter >= $count) {
					break;
				}
			} //end foreach
			$tweet_group .= $after_all_tweets;
		}
	}	
	echo $tweet_group;
}

//linkify the twitter string
function add_hyperlinks_tp( $text ) { 
	$text = preg_replace("/((^@)|([^A-Za-z0-9@]@))([A-Za-z0-9_]{1,})/", "$1<a class=\"twitter_user\" href=\"http://www.twitter.com/$4\">$4</a>", $text); //add the username link
 	$text = preg_replace("/((^#)|([^A-Za-z0-9#]#))([A-Za-z0-9_]{1,})/", "$1<a class=\"twitter_hash\" href=\"http://search.twitter.com/search?q=$4\">$4</a>", $text);  //add the hash tag links
	if ( function_exists ( 'make_clickable' ) ) { //make_clickable exists since 0.71
		$text = make_clickable($text); }
	return $text;
}


class TweetiePieWidget extends WP_Widget
{

    function TweetiePieWidget(){
	    $widget_ops = array('classname' => 'widget_tweetie_pie', 'description' => __( "Display your twitter updates") );
	    $this->WP_Widget('tweetiepie', __('Tweetie Pie'), $widget_ops);
    }

	/* Display the tweetie pie widget */
    function widget($args, $instance){
      extract($args);
      $title = apply_filters('widget_title', $instance['title']);
      $userName = $instance['userName'];
      $numTweets = empty($instance['numTweets']) ? 1 : $instance['numTweets'];
	  $showDate = $instance['showDate'];

      # Before the widget
      echo $before_widget;

      # The title
      if ( $title )
      	echo $before_title . $title . $after_title;

	  gettweet_tp($userName, $numTweets, $showDate);

      # After the widget
      echo $after_widget;
  }

 	/* Save widget settings */
    function update($new_instance, $old_instance){
      $instance = $old_instance;
      $instance['title'] = strip_tags(stripslashes($new_instance['title']));
      $instance['userName'] = strip_tags(stripslashes($new_instance['userName']));
      $instance['numTweets'] = strip_tags(stripslashes($new_instance['numTweets']));
      $instance['showDate'] = $new_instance['showDate'];

    return $instance;
  }

	/*edit form */
    function form($instance){
		$instance = wp_parse_args( (array) $instance, array('title'=>'Twitter Updates', 'userName'=>'', 'numTweets'=>'1', 'showDate'=>'') );

		$title = htmlspecialchars($instance['title']);
		$userName = htmlspecialchars($instance['userName']);
		$numTweets = htmlspecialchars($instance['numTweets']);
		$showDate = $instance['showDate'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('title');  ?>" value="<?php echo $title ?>" />
		</p>
		<p>
			<label for="<?php  echo $this->get_field_id('userName'); ?>">Twitter Username:</label>
			<input id="<?php echo $this->get_field_id('userName'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('userName'); ?>" value="<?php echo $userName; ?>" />
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('numTweets'); ?>">Number of tweets to display:</label>
			<input id="<?php echo $this->get_field_id('numTweets'); ?>" style="width: 50px;" type="text" name="<?php echo $this->get_field_name('numTweets');  ?>" value="<?php echo $numTweets ?>" />	
		</p>
		<p>
			<input  id="<?php echo $this->get_field_id('showDate'); ?>" name="<?php echo $this->get_field_name('showDate'); ?>" type="checkbox" <?php if ($showDate) echo 'checked'; ?> class="checkbox" />
			<label for="<?php echo $this->get_field_id('showDate'); ?>">Show date</label>
		</p>
 <?php }

}// END class


function TweetiePieInit() {
	register_widget('TweetiePieWidget');
}
add_action('widgets_init', 'TweetiePieInit');


