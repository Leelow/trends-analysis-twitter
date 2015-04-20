<?php

	require_once 'config.php';
	require_once DIR_INCLUDES . 'header.inc.php';
	
?>
	<br>
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
        <div id="progress-footer" class="navbar navbar-default navbar-fixed-bottom">
			<div class="container">
				<!-- Date de début -->
				<div class="col-lg-1">
					<h6 id="begin" class="label-progressbar"></h6>
				</div>
				<!-- Progressbar -->
				<div class="col-lg-6" id="col-progressbar">
				  <div class="progress progress-striped active">
					<div id="progressbar" class="progress-bar progress-bar-success" style="width:100%">
					  <!-- Avancement de la campagne -->
					  <span id="percentage_campaign"></span>
					</div>
				  </div>
				</div>
				<!-- Date de fin -->
				<div class="col-lg-1">
					<h6 id="end" class="label-progressbar"></h6>
				</div>
				<!-- Bouton d'arrêt -->
				<div class="col-lg-2">
				  <button id="cancel_button" type="button" class="btn btn-warning col-lg-12">Arreter la campagne</button>	
				</div>	
				<!-- Bouton de suppression -->
				<div class="col-lg-2">
				  <button id="delete_button_started" type="button" class="btn btn-danger col-lg-12">Supprimer la campagne</button>
				</div>
            </div>
        </div>
	
<?php

	require_once DIR_INCLUDES . 'footer.inc.php';;
	
?>