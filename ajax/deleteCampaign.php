<?php

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_INCLUDES               . 'secure.inc.php';
	require_once DIR_SYSTEM                 . 'campaign.php';

    onlyConnected();

    file_put_contents("F:\Utilisateurs\Leo\Desktop\Nouveau document texte.txt", time());
    exit();

	if(isset($_GET['id']) and (!empty($_GET['id']))) {
		try {
			$campaign = new Campaign($_GET['id']);
			$campaign->delete();
			echo json_encode(array('response' => 'SUCCESS'));
		} catch(Exception $e) {
			echo json_encode(array('response' => 'ERROR'));
		}
	} else
		echo json_encode(array('response' => 'ERROR'));

?>