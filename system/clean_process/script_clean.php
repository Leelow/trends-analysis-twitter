<?php

	//////////////////////////////////////////////////////////////////////
	//																	//
	//            	Ce script est chargé du nettoyage des				//
	//			  	champs 'text' des tweets.							//
	//																	//
	//////////////////////////////////////////////////////////////////////

	require_once dirname(dirname(__DIR__)) . '/config.php';
	require_once DIR_SYSTEM 			   . 'campaign.php';
	require_once DIR_SYSTEM 			   . 'campaignBdd.php';
	require_once 							 'clean.php';

	if(count($argv) != 4) {
		echo 'Usage : script_clean.php id_campagne input output' . "\n";
		exit();
	}
	
	$id  	= $argv[1];
	$input  = $argv[2];
	$output = $argv[3];
	
	// On exécute l'algorithme de nettoyage
	$time = round(clean($input, $output), 2);
	$tweets = count(file($input));

	$campaign = new Campaign($id);
	$campaign->bdd->insertClean($time, $tweets);
	
	// On execute les algortihmes d'analyse
	$campaign->execNewmanGirvan();
	$campaign->execTfIdf();
	
?>