<?php

	//////////////////////////////////////////////////////////////////////
	//																	//
	//            	Ce script est chargé de selectionner				//
	//			  	les tweets les plus pertinents pour					//
	//				un intervalle donné.								//
	//																	//
	//////////////////////////////////////////////////////////////////////

	require_once dirname(dirname(__DIR__)) . '/config.php';
	require_once DIR_SYSTEM 			   . 'campaign.php';
	require_once DIR_SYSTEM 			   . 'campaignBdd.php';
	require_once DIR_SYSTEM 			   . 'campaignExport.php';

	if(count($argv) != 4) {
		echo 'Usage : script_sugar.php id_campagne input output' . "\n";
		exit();
	}
	
	$id  	= $argv[1];
	$input  = $argv[2];
	$output = $argv[3];

	$campaign = new Campaign($id);
	
	// ****** On récupère les 10 mots fournis par TF-IDF ****** //
	$words= $campaign->bdd->getWordsTfIdf();

	// ****** On parcourt tous les tweets correspondants à l'instant et on selectionne celui qui a le plus de mots ****** //

	// Timestamp en ms de départ
	$time_start = microtime(true);

	// Ouverture du fichier de tweets
	$handle = fopen($input, 'r');
	
	// Tweet le plus pertinent
	$tweets		 = 0;
	$number_best = 0;
	$length_best = 0;
	$id;
	
	// Si le fichier est bien ouvert
	if ($handle) {
		
		// On parcourt toutes ls lignes jusqu'à la fin du fichier
		while (!feof($handle)) {

			// On recupère la ligne courante
			$buffer = fgets($handle);
			
			// On compte le nombre d'occurence des mots dans le tweet
			$tweet = json_decode($buffer, true);
			$length = strlen($tweet['text']);
			$number = 0;
			$tweets++;
			
			foreach($words as $w)
				$number += substr_count($tweet['text'], $w);
				
			if(($number > $number_best) or ($number == $number_best	and	$length > $length_best)) {
				$number_best = $number;
				$length_best = $length;
				$id = $tweet['id_str'];
			}
			
		}
		
	}
	
	// On recupère le code HTML du tweet généré via l'API Twitter
	$html = $campaign->export->exportTweetHTML($id);
	file_put_contents($output, $html);
	
	$time = microtime(true) - $time_start;
	
	// On insère les resultats dans la bdd
	$campaign->bdd->insertSugar($id, $time, $tweets);
	
?>