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
        // On recupère les 10 campagnes terminées/annulées les plus récentes
		$endedOrCancelledCampaign = CampaignBdd::getEndedOrCancelledCampaignLimit();
        $count_display = count($endedOrCancelledCampaign);
        if($count_display > 0) {
            foreach($endedOrCancelledCampaign as $campaign)
                echo '<li><a href="view.php?id=' . $campaign['id'] . '">' . $campaign['name'] . '</a></li>' . "\n";
            echo '<li><a href="list_ended_cancelled.php"><b>Voir l\'intégralité des campagnes terminées</b></a></li>' . "\n";
        } else {
			echo '<li><a href="#">Aucune campagne</a></li>' . "\n";
		}
	}

    // Liste de l'intégralité des campagnes terminées
    function listEndedOrCancelledCampaign() {
        $endedOrCancelledCampaign = CampaignBdd::getEndedOrCancelledCampaign();
        $count_display = count($endedOrCancelledCampaign);
        if($count_display > 0) {
            echo '<ul class="list-group">' . "\n";
            foreach($endedOrCancelledCampaign as $campaign) {
                echo '<li class="list-group-item"><span class="badge">14</span><a href="view.php?id=' . $campaign['id'] . '">' . $campaign['name'] . '</a></li>' . "\n";
            }
            echo '</ul>' . "\n";
        } else {

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