<?php

	require_once dirname(dirname(__FILE__)) . '/config.php';
	require_once DIR_SYSTEM                 . 'campaignBdd.php';

	// Menu qui affiche le lien vers la campagne en cours
	function menuStartedCampaign() {
		$startedCampagin = CampaignBdd::getStartedCampaign();
		echo '<li>' . "\n";
		echo '	  <a href="view.php?id=' . $startedCampagin['id'] . '"><strong>' . $startedCampagin['name'] . '</strong></a>' . "\n";
		echo '</li>' . "\n";
	}
	
	// Menu qui affiche les campagnes programmées
	function menuScheduledCampaign() {
		$scheduledCampagin = CampaignBdd::getScheduledCampaign();	
		if(count($scheduledCampagin) > 0) {		
			foreach($scheduledCampagin as $campaign)
				echo '<li><a href="view.php?id=' . $campaign['id'] . '">' . $campaign['name'] . '</a></li>' . "\n";	
		} else {
			echo '<li><a href="#">Aucune campagne</a></li>' . "\n";
		}
	}

	// Menu qui affiche les campagnes terminées
	function menuEndedOrCancelledCampaign() {
		$endedOrCancelledCampagin = CampaignBdd::getEndedOrCancelledCampaign();	
		if(count($endedOrCancelledCampagin) > 0) {		
			foreach($endedOrCancelledCampagin as $campaign)
				echo '<li><a href="view.php?id=' . $campaign['id'] . '">' . $campaign['name'] . '</a></li>' . "\n";	
		} else {
			echo '<li><a href="#">Aucune campagne</a></li>' . "\n";
		}
	}

    // Affichage en fonction de l'état (connecté ou non)
    function menuState() {
        echo '<li>' . "\n";
        if(isset($_SESSION['connect']) && $_SESSION['connect'])
            echo '<a href="signout.php">Se déconnecter</a>' . "\n";
        else
            echo '<a href="login.php">Se connecter</a>' . "\n";
        echo '</li>' . "\n";
    }

?>