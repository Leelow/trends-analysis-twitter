<?php

	////////////////////////////////////////////////////////////////////
	//																  //
	//                Démarre une campagne existante                  //
	//																  //
	////////////////////////////////////////////////////////////////////

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_SYSTEM                 . 'campaign.php';
	
	if(count($argv) != 2) {

		echo 'Usage : start_campaign.php id_campagne' . "\n";
		exit();
		
	}
	
	// Id de la campagne
	$id = $argv[1];

	// On recupère la campagne
	$campaign = new Campaign($id);
	$campaign->start();
	
?>