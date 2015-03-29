<?php

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_INCLUDES               . 'secure.inc.php';
	require_once DIR_SYSTEM                 . 'campaign.php';

	// Nombre de tweets à récupérer/afficher
	$number_tweets = 8;
	
	// Délai minimum (en secondes) entre chaque génération de code html de tweets (pour éviter la surcharge de l'api).
	$delay_tweets = 50;
	
	if(isset($_GET['id']) and (!empty($_GET['id']))) {
		$campaign = new Campaign($_GET['id']);
		$tweets_cache_file = CAMPAIGN_DIR . $campaign->id . '_' . $campaign->name . '/tweets_html.cache';
		// On utilise l'API si : les tweets n'ont jamais été générés ou sont trop anciens, et uniquement si la campagne n'est pas terminée
		if((!file_exists($tweets_cache_file) or filemtime($tweets_cache_file) <= (time() - $delay_tweets)) and true) {
			$tweets_id = $campaign->getRecentTweetsId($number_tweets);
			$cache = $campaign->export->exportTweetsHTML($tweets_id);
			file_put_contents($tweets_cache_file, json_encode($cache, JSON_PRETTY_PRINT));
		}
		$html_tweets = json_decode(file_get_contents($tweets_cache_file));
			
		echo json_encode(array('response' => 'SUCCESS', 'html_tweets' => $html_tweets), JSON_PRETTY_PRINT);
	} else
		echo json_encode(array('response' => 'ERROR'), JSON_PRETTY_PRINT);

?>