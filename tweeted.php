<?php
/*
Plugin Name: Simply Tweeted
Plugin URI: http://fusedthought.com/downloads/simply-tweeted-wordpress-plugin/
Description: A no-frills, twitter updater (title and url) upon post/page publishing. It can be used as a standalone plugin or integrated with the <a href="http://fusedthought.com/downloads/url-shortener-wordpress-plugin/">URL Shortener Plugin</a> (if installed).
Author: Gerald Yeo
Author URI: http://fusedthought.com
Version: 1.0
*/
global $fts_urlfx;
require_once( dirname(__FILE__) . '/req/class.tweeted.1.0.php');
if (!function_exists('fts_active')){function fts_active($plugin) {return in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );}}
function fts_tweeted_init(){
	global $fts_urlfx;
	//name - db
	register_setting('fts_tweeted','fts_urlfx');
	$fts_urlfx = get_option('fts_urlfx');	
}
if ( is_admin() ){
	add_action('admin_init', 'fts_tweeted_init', 9);
}

if (!function_exists('fts_tweet')){
	function fts_tweet($post_ID, $url){
		global $fts_urlfx;
		$got_tweet = get_post_meta($post_ID, 'tweet', true);
		if (!$got_tweet){
			$user = $fts_urlfx['tweet_user'];
			$pass = $fts_urlfx['tweet_pass'];
		
			$tweet = new tweeted();
			$tweet->user = $user;
			$tweet->pass = $pass;
			$tweet->msg = get_the_title($post_ID);
			$tweet->url = $url;
			$result = $tweet->updatestatus();
			//$result = 'http://localhost/'; //test
			//$save = $result; //test
			if($result){
				$data = new SimpleXMLElement($result);	
				$save = $data->id;
				$save = 'http://twitter.com/'.$user.'/status/'.$save;
				update_post_meta($post_ID, 'tweet', $save);
			}
		}	
	}
}

if (!function_exists('fts_tweetit')){
	function fts_tweetit($post){
		$postid = $post->ID;
		$got_shorturl = get_post_meta($postid, 'shorturl', true);
		if ($got_shorturl){ $url = $got_shorturl; } else {$url = get_permalink($postid);}
		if ($_POST['post-tweet']){fts_tweet($postid, $url);}
	}
}
if(fts_active('url-shortener/fts-shortenurl.php')){
	$urlshort = get_option('fts_urlfx');
	if ($urlshort['urlserviceenable'] == 'yes'){
		add_action('publish_to_publish', 'fts_tweetit');
	}else{
		require_once( dirname(__FILE__) . '/req/options.php');
	}
} else{
	require_once( dirname(__FILE__) . '/req/options.php');
}
?>