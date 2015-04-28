$(document).ready(function () {
	
	// On recupère les tweets actuels, on les met en attente
	prepareTweets()
	
	// On execute le script de twitter pour la mise en forme des tweets
	execScriptWidgetTwitter();
	
	// On patiente puis on execute la suite
	setTimeout(function () {
	
		display_tweet(0);
	
	}, 5000);
	
	/*setTimeout(function () {
	
		display_tweet(2);
	
	}, 5000);*/
	
	
	/*if(getState() == 'STARTED')
		displayTweets(getId());*/
	
});

var active  = -1;
var size    = 0;

function prepareTweets() {

	$.ajax({
		url: 		'ajax/getTweetsSugar.php?id=' + getId(),
		dataType: 	'text',
		success: 	function(json_data) {
		
						var divTweetsSugar = $('#tweet_sugar');
						tweets = $.parseJSON(json_data);
						
						size = 0;
						
						jQuery.each(tweets, function(step, html_code){
							divTweetsSugar.append('<div id="tweets-sugar-' + step + '"></div>');
							$('#tweets-sugar-' + step).html(html_code).hide();
							size++;
						});
						
						
					}
	});

}

function execScriptWidgetTwitter() {
	$.getScripts({
		urls: ['js/sugar/widgets_twitter.js'],
		cache: true,
		async: false
	});	
}

function display_tweet(step) {

	if(step != active && step < size) {

		//console.log(active);
	
		if(active == -1) {
			$('#tweets-sugar-' + step).fadeIn();	
			active = step;			
		} else {
	
			// On cache le tweet actif
			$('#tweets-sugar-' + active).fadeOut('fast', function() {
			
				active = step;
			
				// On affiche le tweet souhaité
				$('#tweets-sugar-' + step).fadeIn();		
			
			});
			
		}
		
	}

}

/*
var i = 0;
var path_script_twitter = 'js/timeline/widgets_twitter.js';
var tweets = []

// Recupère les 8 tweets grâce à l'API
function getTweetsAPI(id, cb) {
//console.log(i);
	if(i == 0) {
		$.ajax({
			url: 		'/ajax/getLastTweets.php?id=' + id,
			dataType: 	'text',
			success: 	function(json_data) {
							i = 8;
							tweets = $.parseJSON(json_data);
							tweets['html_tweets'].pop();
							tweets['html_tweets'].pop();
							cb($.parseJSON(json_data));
						}
		});
	} else {
		cb(tweets);
		i -= 2;
	}
}

// Execute le script d'affichage des tweets
function execScriptWidgetTwitter() {
	$.getScripts({
		urls: [path_script_twitter],
		cache: true,
		async: false
	});	
}

// Remplace le contenu d'un div par celui d'un autre avec un effet de fondu
function replaceDivFading(before, after, speed) {
	before.fadeOut(speed, function() {
		after.fadeIn(speed);	
	});	
}

function displayNewTweet(new_tweet) {


}

function displayTweets(id) {

	var divTweets0 = $('#tweets-div-0');
	var divTweets1 = $('#tweets-div-1');
	//var div0      = $('#tweets-0');
	//var divtmp0   = $('#tweets-tmp-0');

	// On recupère les tweets
	getTweetsAPI(id, function(tweets) {
		
			divTweets0.append('<div id="tweets-tmp-0"></div>');
			$('#tweets-tmp-0').html(tweets['html_tweets'].pop()).hide();
		
			divTweets1.append('<div id="tweets-tmp-1"></div>');
			$('#tweets-tmp-1').html(tweets['html_tweets'].pop()).hide();
		
			// On execute le script de mise en forme des tweets
			execScriptWidgetTwitter();
			
			// On attend que la mise en forme soit faite
			setTimeout(function () {
				
				$('#tweets-0').fadeOut('slow', function() {
					$('#tweets-0').remove();
					$('#tweets-tmp-0').fadeIn('slow');
					$('#tweets-tmp-0').attr('id', 'tweets-0');
					//before.html(after.contents()).hide();
					//after.fadeIn(speed);
					//displayTweets(id);
					});
					
				$('#tweets-1').fadeOut('slow', function() {
					$('#tweets-1').remove();
					$('#tweets-tmp-1').fadeIn('slow');
					$('#tweets-tmp-1').attr('id', 'tweets-1');
					//before.html(after.contents()).hide();
					//after.fadeIn(speed);
					//displayTweets(id);
					});
					
					setTimeout(function () {
					
						displayTweets(id);
					
					}, 7000);
			
			}, 2000);
	
	});

};*/