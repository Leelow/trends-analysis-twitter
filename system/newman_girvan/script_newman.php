<?php

	//////////////////////////////////////////////////////////////////////
	//																	//
	//            	Ce script est chargé du lancement de				//
	//			  	l'algorithme de Newman-Girvan. Il at-				//
	//				tend la fin de l'exécution pour en-					//
	//				registrer les résultats dans la BDD.				//
	//																	//
	//////////////////////////////////////////////////////////////////////

	require_once dirname(dirname(__DIR__)) . '/config.php';
	require_once DIR_SYSTEM 			   . 'campaign.php';
	require_once DIR_SYSTEM 			   . 'campaignBdd.php';

	if(count($argv) != 4) {
		echo 'Usage : script_newman.php id_campagne input output' . "\n";
		exit();
	}
	
	$id  	= $argv[1];
	$input  = $argv[2];
	$output = $argv[3];
	
	// On compte le nombre de tweets à analyser
	$tweets = count(file($input));
	
	// On adapte le nombre de mots du dictionnaire en fonciton de nombre de tweets
	// L'objectif et de truver un compromis entre le temps d'execution et la précision
	$number_of_words = round(200 - ($tweets / 30));
	
	// On exécute l'algorithme en affichant le maximum de données
	exec('java -jar ' . NEWMAN_GIRVAN_JAR . ' -w ' . '"' . $input . '" "' . $output . '" "' . NEWMAN_GIRVAN_DIC . '" "' . $number_of_words . '"');
	
	// ****** On récupère les données produites pour les enregistrer dans la base de données ****** //
	
	$array_file = json_decode(file_get_contents($output), true);
	$value  = $array_file['value'];
	$time   = $array_file['time'];
	
	$path_parts = pathinfo($output);
	$words_file_path = $path_parts['dirname'] . '/' . $path_parts['filename'] . '.array.json';
	$json_array = json_decode(substr(file_get_contents($words_file_path), 8), true);

	$words = array();
	for($i = 0; $i < 10; $i++)
		array_push($words, $json_array[$i]['w']);
	
	$campaign = new Campaign($id);
	$campaign->bdd->insertNewmanGirvan($value, $time, $tweets, $words);
	
?>