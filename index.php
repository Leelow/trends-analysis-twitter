<?php

	require_once 'config.php';
	require_once DIR_INCLUDES . 'header.inc.php';
	
?>

	<br>
	<br>

	<div class="row">
		<div class="bs-component">
		  <div class="alert alert-dismissible alert-info">
			<button type="button" class="close" data-dismiss="alert">×</button>
			Vous être actuellement en mode invité. Vous pouvez vous connecter <strong><a href="login.php" class="alert-link">ici</a></strong>.
		  </div>
		</div>
	</div>

	<br>
	<div class="row">
		<h2>Panneau de contrôle</h2>
	</div>
	<div class="row">
		<div class="col-lg-6">
			<div class="bs-component">
			  <div class="modal">
				<div class="modal-dialog">
				  <div class="modal-content">
					<div class="modal-header">
					  <h4 class="modal-title">Campagnes à venir</h4>
					</div>
					<div class="modal-body">
					  <p><?php displayScheduledCampaign() ?></p>
					</div>
				  </div>
				</div>
			  </div>
		  </div>
		</div>
		<div class="col-lg-6">
			<div class="bs-component">
			  <div class="modal">
				<div class="modal-dialog">
				  <div class="modal-content">
					<div class="modal-header">
					  <h4 class="modal-title">Informations</h4>
					</div>
					<div class="modal-body">
					<?php
					
						// Date de la première campagne
						$first_date = date('d/m/Y', campaignBdd::getFirstDateCampaign());
						
						// Nombre de campagnes effectuées
						$number_campaign = campaignBdd::getNumberEndedOrCancelledCampaign();
						$print_campagnes = ($number_campaign > 1) ? 'campagnes' : 'campagne';
						
						// Nombre total de tweets récupérés
						$number_tweets = number_format(campaignBdd::getTotalTweetsEndedOrCancelledCampaign(), 0, ',', '.');
						
						// Poids total des campagnes effectuées
						$size_tweets = campaignBdd::getTotalTweetsSizeEndedOrCancelledCampaign();
					
					?>				
					  <p>Depuis le <b><?php echo $first_date ?></b>, <b><?php echo $number_tweets ?></b> de tweets ont été récoltés sur <b><?php echo $number_campaign  . ' ' . $print_campagnes ?></b>. Cela représente environ <b><?php echo $size_tweets ?></b>.</p>
					</div>
				  </div>
				</div>
			  </div>
		    </div>
		</div>
    </div>

		<!-- Progress footer -->
        <div class="navbar navbar-default navbar-fixed-bottom">
			<div class="container text-center">				
				<p>Copyright &copy; TaT 2015</p>
			</div>
        </div>
	
<?php

	require_once DIR_INCLUDES . 'footer.inc.php';;
	
?>