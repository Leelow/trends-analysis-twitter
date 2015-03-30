<?php

	////////////////////////////////////////////////////////////////////
	//																  //
	//        Script d'installation des diffèrents composants         //
	//																  //
	////////////////////////////////////////////////////////////////////

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_SYSTEM                 . 'campaignBdd.php';
	
	CampaignBdd::installation();
	
	echo 'Installation effectue.' . "\n";

?>