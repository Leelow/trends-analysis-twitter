<?php

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_SYSTEM                 . 'campaign.php';
	require_once DIR_SYSTEM                 . 'campaignBdd.php';
	require_once DIR_SYSTEM                 . 'campaignStyle.php';

	$c = new Campaign(33);
	$c->bdd->maj();

?>
