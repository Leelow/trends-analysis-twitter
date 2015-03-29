<?php

	////////////////////////////////////////////////////////////////////
	//																  //
	//           Créer une nouvelle campagne et la démarre            //
	//																  //
	////////////////////////////////////////////////////////////////////

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_SYSTEM                 . 'campaign.php';
	
	if(count($argv) <= 4) {

		echo 'Usage : new_started_campaign.php nom_campagne begin duree_campagne mot1 mot2 ...' . "\n";
		exit();
		
	}
	
	// Nom de la campagne
	$name = $argv[1];
	
	// Début de la campagne
	$begin = $argv[2];
	
	if($begin == 'now')
		$begin = time();
	
	// Durée de la campagne
	$length = $argv[3];
	if (!is_numeric($length) || $length <= 0) {
	
		echo 'Erreur : duree_campagne doit etre superieur a 0.' . "\n";
		exit();
		
	}
	$length = round($length, 0, PHP_ROUND_HALF_UP);
	
	// Mot(s)-clé(s) ou hashtag(s) de la campagne
	$n = count($argv);
	$keywords = array($n - 4);
	for($i = 4; $i < $n; $i++)
		$keywords[$i-4] = $argv[$i];
		
	// On créer et démarre la campagne
	$campaign = new Campaign($name, $begin, $length, $keywords);
	$campaign->start();
	
?>