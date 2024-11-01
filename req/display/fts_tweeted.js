jQuery(document).ready(function($){

	$('#tweetedoptions').submit(function() {
		$('.reqfielderror').html("");						   
		var errorcount = false;
		var tweetdetails = $('#urltweet').val();
		if (tweetdetails == 'auto' || tweetdetails == 'manual'){
			var tweetuser = jQuery.trim( $('#tweet_user').val() );
			var tweetpass = jQuery.trim( $('#tweet_pass').val() );
			if(tweetuser == "" || tweetpass == ""){
				$('.reqfielderror').append('Please fill in your Twitter Username and Password<br />');
				errorcount = true;
			}				
		}						   
		if (errorcount){
			$('.reqfielderror').fadeIn(400);
			return false;
		} else {
			$('.reqfielderror').hide();
			return true;
		}
	});
	$('.aserv-des').hide();
	$('.aserv').click(function(){
		$(this).next('.aserv-des').toggle(300);
		return false;
	});
	$('#urltweet').each(function(){
		function lc(){	
			var target = $(this).val();
			if (target == 'manual' || target == 'auto'){
				$('#tweetdetails').fadeIn(300);
			} else {
				$('#tweetdetails').fadeOut(300);	
			}
		}
		$(this).change(lc);
		$(this).keypress(lc);
	});
});