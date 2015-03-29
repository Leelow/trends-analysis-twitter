<?php

	////////////////////////////////////////////////////////////////////
	//																  //
	//          Supprimme toutes les données d'une campagne           //
	//																  //
	////////////////////////////////////////////////////////////////////

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_SYSTEM                 . 'campaign.php');
	
	if(count($argv) != 2) {

		echo 'Usage : delete_campaign.php id_campagne' . "\n";
		exit();
		
	}
	
	$campaign = new Campaign($argv[1]);
	$campaign->delete();
	
	echo 'La campagne "' . $campaign->name . '" a bien ete supprimee.' . "\n";

?>