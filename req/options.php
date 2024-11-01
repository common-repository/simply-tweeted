<?php
function fts_tweet_sa($post){
	global $fts_urlfx;
	$manual = $_POST['posttweet'];
	$postid = $post->ID;
	$got_shorturl = get_post_meta($postid, 'shorturl', true);
	if ($got_shorturl){ $url = $got_shorturl; } else {$url = get_permalink($postid);}

	if ($fts_urlfx['tweet'] == 'auto'){
		fts_tweet($postid, $url);
	}elseif($fts_urlfx['tweet'] == 'manual' && $manual == 'Enabled'){
		fts_tweet($postid, $url);	
	} else{}
}

function fts_tweeted_css(){
	$plugin_url = WP_PLUGIN_URL.'/'.plugin_basename( dirname(dirname(__FILE__)) );
	wp_enqueue_style('fts_tweeted_css', $plugin_url.'/req/display/fts_tweeted.css');
	wp_enqueue_script('fts_tweeted_js', $plugin_url.'/req/display/fts_tweeted.js',array('jquery'),1.0);
}
function draw_fts_tweeted_page(){
	global $fts_urlfx;
	?>
	<div class="wrap">		

		<h2>Simply Tweeted<span class="pluginbyline">by <a href="http://fusedthought.com">Gerald Yeo</a> (Fusedthought.com)</span></h2>

		<p>A no-frills, twitter updater (title and url) upon post/page publishing.</p>
		<form id="tweetedoptions" method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<?php settings_fields('fts_tweeted'); ?>
			<h4 class="sectheaders">Options</h4>
	
			<fieldset>
				<div class="nl">
					<label>Post to Twitter:</label>
					<select name="fts_tweeted[tweet]" id="urltweet" >
						<option value="disable" <?php selected( 'disable', $fts_urlfx['tweet'] ); ?>>Disable &nbsp;</option>							
						<option value="manual" <?php selected( 'manual', $fts_urlfx['tweet'] ); ?>>Manual &nbsp;</option>
						<option value="auto" <?php selected( 'auto', $fts_urlfx['tweet'] ); ?>>Auto &nbsp;</option>				
					</select> <a class="aserv" href="#">[?]</a>
					<div class="aserv-des none">
						<p><strong>Disable</strong>: Entire Twitter module will be disabled</p>
						<p><strong>Manual</strong>: Option to post updates to twitter will be made available</p>
						<p><strong>Auto</strong>: Automatically post your Title and the Short URL to Twitter upon post publishing.</p>
						<p>(Format of update: "Title, URL")</p>
					</div>
					<div id="tweetdetails" class="<?php if ($fts_urlfx['tweet'] == 'disable'){ echo "ehideit";} else {echo "eshowit";} ?>">
						<label>Twitter Username (Required)</label> 
						<input type="text" id="tweet_user" name="fts_tweeted[tweet_user]" value="<?php echo $fts_urlfx['tweet_user']; ?>" />
						<label>Twitter Password (Required)</label> 
						<input  type="password" id="tweet_pass" name="fts_tweeted[tweet_pass]" value="<?php echo $fts_urlfx['tweet_pass']; ?>" />
					</div>
				</div>
			</fieldset>
			<h4 class="sectheaders">Save Your Settings</h4>
			<div class="reqfielderror"></div>
			<p class="submit">
				<input type="submit" id="submit-button" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>	
					
		</form>	
	</div>
<?php }

//Start Admin Page
function fts_tweeted_posts_metabox($post){
	global $fts_urlfx;
	$postid = $post->ID;
	$got_tweet = get_post_meta($postid, 'tweet', true);
	if ($got_tweet){?>
		<a href="<?php echo $got_tweet;?>"><?php echo $got_tweet;?></a>
	<?php }elseif ($fts_urlfx['tweet'] == 'auto'){?>
		Post to Twitter: <strong>Auto</strong>
	<?php } elseif ($fts_urlfx['tweet'] == 'manual'){?>
		Post to Twitter: <input type="submit" class="button" name="post-tweet" value="Click to Post" />
	<?php } ?>
		
	<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
		<?php 
		if ($got_tweet){?>
			$('#misc-publishing-actions').append('<div class="misc-pub-section">Post to Twitter: <a href="<?php echo $got_tweet;?>"><strong>Published</strong></a></div>');
		<?php }elseif ($fts_urlfx['tweet'] == 'auto'){?>
			$('#misc-publishing-actions').append('<div class="misc-pub-section">Post to Twitter: <strong>Auto</strong></div>');
		<?php } elseif ($fts_urlfx['tweet'] == 'manual'){?>
			$('#misc-publishing-actions').append('<div style="font-weight: bold; padding: 1em; margin-top: 2em;background: #D2FFCF;">Post to Twitter: <input id="posttweet" type="checkbox" name="posttweet" value="Enabled" /></div>');
		<?php } ?>
	});//global	
	/* ]]> */
	</script>
	
<?php			
}

function fts_tweeted_posts_addons($post){add_meta_box('ftstweeted', 'Tweeted', 'fts_tweeted_posts_metabox', 'post', 'side', 'default');}
function fts_tweeted_page_addons($post){add_meta_box('ftstweeted', 'Tweeted', 'fts_tweeted_posts_metabox', 'page', 'side', 'default');}
function fts_tweeted_add_page() {
	$plugin_page = add_options_page('Tweeted', 'Tweeted', 'administrator', 'tweeted', 'draw_fts_tweeted_page');
	add_action( 'load-'.$plugin_page, 'fts_tweeted_css' );
}

if ( is_admin() ){ 
	add_action('load-post.php', 'fts_tweeted_posts_addons');
	add_action('load-post-new.php', 'fts_tweeted_posts_addons');
	add_action('load-page.php', 'fts_tweeted_page_addons');
	add_action('load-page-new.php', 'fts_tweeted_page_addons');
	add_action('admin_menu', 'fts_tweeted_add_page');
	add_action('publish_to_publish', 'fts_tweet_sa', 20);
	add_action('draft_to_publish', 'fts_tweet_sa', 20); 
	add_action('private_to_publish', 'fts_tweet_sa', 20); 
	add_action('future_to_publish', 'fts_tweet_sa', 20); 
	add_action('pending_to_publish', 'fts_tweet_sa', 20);  
	add_action('new_to_publish', 'fts_tweet_sa', 20); 
}

?>