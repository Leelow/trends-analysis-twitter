<?php

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_INCLUDES               . 'secure.inc.php';
	require_once DIR_SYSTEM                 . 'campaign.php';

    /* On retourne un message personnalisé si on est pas connecté */
    if(!$_SESSION['connect']) {
        $json_tab = array('response' => 'ERROR',
                          'type'     => 'state',
                          'msg'      => '');
        echo json_encode($json_tab, JSON_PRETTY_PRINT);
        exit();
    }

	// On vérifie l'existence des données pour la création de la campagne
	if(!isset($_POST['name']) or !isset($_POST['begin']) or !isset($_POST['length']) or !isset($_POST['keywords'])) {
		echo json_encode(array('response' => 'ERROR'), JSON_PRETTY_PRINT);
		exit();	
	}
	
	// On recupère les données pour la création de la campagne
	$name     = $_POST['name'];
	$begin    = strtotime($_POST['begin']);
	$length   = $_POST['length'];
	$keywords = explode("\n", $_POST['keywords']);
	
	// On tente de créer une campagne à partir de ces données
	try {
		$campaign = new Campaign($name, $begin, $length, $keywords);
		$campaign->scheduled();
		
		// On retourne que tout s'est bien déroulé
		echo json_encode(array('response' => 'SUCCESS'), JSON_PRETTY_PRINT);
		
	} catch(CampaignException $e) {
	
		// S'il y a une erreur, on va retourner les données concernant la campagne en conflit avec la nouvelle
		$json_tab = array('response' => 'ERROR',
                          'type'     => 'conflict',
						  'msg'      => $e->comment);
		echo json_encode($json_tab, JSON_PRETTY_PRINT);
	}

?>