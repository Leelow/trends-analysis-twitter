<?php

	//////////////////////////////////////////////////////////////////////
	//																	//
	//            	Ce script est chargé du lancement de				//
	//			  	l'algorithme de TF-IDF. Il attend la				//
	//				fin de l'exécution pour enregistrer 				//
	//				les résultats dans la BDD.							//
	//																	//
	//////////////////////////////////////////////////////////////////////

	require_once dirname(dirname(__DIR__)) . '/config.php';
	require_once DIR_SYSTEM 			   . 'campaign.php';
	require_once DIR_SYSTEM 			   . 'campaignBdd.php';

	if(count($argv) != 4) {
		echo 'Usage : script_tf_idf.php id_campagne input output' . "\n";
		exit();
	}
	
	$id  	= $argv[1];
	$input  = $argv[2];
	$output = $argv[3];

	// On exécute l'algorithme en affichant le maximum de données
	exec('java -jar "' . TF_IDF_JAR . '" ' . '"' . $input . '" "' . $output . '" "' . TF_IDF_DIC_NEG . '" "' . TF_IDF_DIC_POS . '"');
	
	// ****** On récupère les données produites pour les enregistrer dans la base de données ****** //

	$array_file = json_decode(file_get_contents($output), true);
	$value  = $array_file['result'];
	$time   = $array_file['time'];
	$tweets = count(file($input));
	
	arsort($array_file['results'], SORT_NUMERIC);
	$words = array_keys(array_slice($array_file['results'], 0, 10));
	
	print_r($words);
	
	$campaign = new Campaign($id);
	$campaign->bdd->insertTfIdf($value, $time, $tweets, $words);
	
	// On peut exécuter l'algorithme SUGAR
	$campaign->execSugar();
	
?>