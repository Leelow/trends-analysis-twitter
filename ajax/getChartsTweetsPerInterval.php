<?php

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_INCLUDES               . 'secure.inc.php';
	require_once DIR_SYSTEM                 . 'campaign.php';

	if(isset($_GET['id']) and (!empty($_GET['id']))) {
		$campaign = new Campaign($_GET['id']);
		echo $campaign->export->chartsTweetsPerInterval();
	} else
		echo 'Error';

?>