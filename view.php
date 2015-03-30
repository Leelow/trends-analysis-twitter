<?php

	require_once 'config.php';
	require_once DIR_INCLUDES . 'header.inc.php';
	
	// On s'assure que l'on recupère bien une campagne valide
	if(isset($_GET['id']) and !empty(isset($_GET['id']))) {
		try {
			$campaign = new Campaign($_GET['id']);
		} catch(Exception $e) {
			header('Location: index.php'); 
		}
	} else {
		header('Location: index.php'); 
	}
	
?>
	
    <script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>
	
	<script src="js/view/visibility.js" type="text/javascript"></script>
	<script src="js/view/interaction.js" type="text/javascript"></script>
	<script type="text/javascript">
	
	function getId()    { return $('#campaign_id').val();    }	
	function getName()  { return $('#campaign_name').val();  }	
	function getState() { return $('#campaign_state').val(); }	
	function getStep()  { return $('#campaign_step').val();  }
	
	$(document).ready(function () {

		var view_div     = $('#view');
		var view_waiting = $('#view_waiting');
		
		// Mise à jour automatique des données
		Data();

		// Si la campagne a débuté depuis au moins deux minutes, on affiche les données
		if(getStep() >= 2) {
			Interaction();			// Interaction
			Map_tweets(); 			// Carte de localisation
			Graph_tweets_count(); 	// Graphique de comptage des tweets
			Graph_algo_polarity(); 	// Graphique des algorithmes de polarité
			Graph_algo_sugar();		// Graphique de l'algorithme SUGAR
	
			// Affichage si la campagne n'est pas terminée
			if(getState() == 'STARTED') {
				showProgressFooter();
			// Affichage si la campagne est terminée ou annulée
			} else {
				showEndedCancelledZone();
			}
			
			view_div.fadeIn();
		}
		// Sinon, on affiche une alerte et on reactualisera la page toutes les minutes jusqu'à ce que la campagne soit lancée
		else {
			view_waiting.fadeIn();
			setTimeout(function() {
				document.location = document.location;
			}, 15000);
		}	
		
	});
	</script>

	  <input id="campaign_id" type="hidden" value="<?php echo $campaign->id ?>">
	  <input id="campaign_name" type="hidden" value="<?php echo $campaign->name ?>">
	  <input id="campaign_state" type="hidden" value="<?php echo $campaign->state ?>">
	  <input id="campaign_step" type="hidden" value="<?php echo $campaign->step ?>">
	
	  <div id="view_waiting" style="display:none">
        <div class="page-header">
          <div class="row">
            <div class="col-lg-12">
              <h1><?php echo $campaign->name . ' - ' ?><span class="title_state"></span></h1>
			  <h3><?php echo '(' . date('d/m/Y G:i', $campaign->begin) . ' - ' . date('d/m/Y G:i', $campaign->begin + $campaign->length * 60) . ')' ?></h3>
			</div>
          </div>
        </div>
		<br>
		<br>
		<br>
		<br>
		<br>
        <div class="row">
          <div class="col-lg-12 text-center">
            <h3>Les données s'afficheront quand elles seront disponibles.</h3>
            <h6>Cliquez <a onclick="delete_link();" href="#">ici</a> si vous souhaitez supprimer cette campagne.</h6>
		  </div>
        </div>
	  </div>
	
      <div id="view" style="display:none">
        <div class="page-header">
          <div class="row">
            <div class="col-lg-12">
              <h1><?php echo $campaign->name . ' - ' ?><span class="title_state"></span></h1>
			  <h3><?php echo '(' . date('d/m/Y G:i', $campaign->begin) . ' - ' . date('d/m/Y G:i', $campaign->begin + $campaign->length * 60) . ')' ?></h3>
			</div>
          </div>
        </div>

		<div class="row">
		
		  <!-- Mots clés -->
		  <div class="col-lg-6">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title" id='title_keywords'>Chargement ...</h3>
                </div>
                <div class="panel-body" id="keywords">
                </div>
              </div>
            </div>
          </div>
		
		  <div class="col-lg-6">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Quelques chiffres</h3>
                </div>
                <div class="panel-body">
                Volume des données téléchargées : <span id="size">Chargement ...</span><br>
				Total de tweets récupérés : <span id="total_tweets">Chargement ...</span><br>
				<!-- Nombre de twittos uniques : <span id="total_twittos">Chargement ...</span><br> -->
                </div>
              </div>
            </div>
          </div>
		
		</div>
        <div class="row">
		
		  <!-- Carte de localisation -->
          <div class="col-lg-6">	   
			<h2><b>Localisation des tweets</b></h2>
			<div id="map" style="width: 550px; height: 400px;"></div>
 			<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
			<script src="js/map/markerCluster.js" type="text/javascript"></script>	
			<script src="js/map/map_tweets.js" type="text/javascript"></script>
          </div>
		  
		  <!-- Graphique des tweets -->
          <div class="col-lg-6">
			<h2><b>Tweets récoltés</b></h2>
			<div id="graph_tweets_count" style="width: 600px; height: 450px"></div>
			<script src="js/graph/graph_tweets_count.js" type="text/javascript"></script>
          </div>

        </div>
		
 		<div class="row">
		
			<!-- Timeline -->
<!-- 			<script src="js/timeline/jquery.getscripts.js" type="text/javascript"></script>
			<script src="js/timeline/widgets_twitter_animation.js" type="text/javascript"></script>
			<div class="col-lg-6">		
		      <div id="tweets-div-0">
				<center><div id="tweets-0"></div></center>
			  </div>
			</div>
			<div class="col-lg-6">		
		      <div id="tweets-div-1">
				<div id="tweets-1"></div>
			  </div>
			</div> -->
		
		</div>

        <div class="row">
		
		  <!-- Graphique des algorithmes de polarité -->
          <div class="col-lg-12">	   
			<h2><b>Algorithmes de polarité</b></h2>
			<div id="graph_algo_polarity" style="height: 500px"></div>
			<script src="js/graph/graph_algo_polarity.js" type="text/javascript"></script>
          </div>

        </div>
		
        <div class="row">
		
		  <!-- Graphique de l'agorithme SUGAR -->
          <div class="col-lg-9" id="sugar-graph">	   
			<h2><b>Synthèse des données</b></h2>
			<div id="graph_algo_sugar" style="height: 500px"></div>
			<script src="js/sugar/jquery.getscripts.js" type="text/javascript"></script>
			<script src="js/sugar/widgets_twitter_animation.js" type="text/javascript"></script>
			<script src="js/graph/graph_algo_sugar.js" type="text/javascript"></script>
          </div>
          <div class="col-lg-3" id="sugar-tweet">
		  <div id="tweet_sugar" style="min-height: 100%;min-height: 100vh;display: flex;align-items: center;"></div>
          </div>

        </div>
	
		<!-- Progress footer -->
        <div id="progress-footer" class="navbar navbar-default navbar-fixed-bottom" style="display:none">
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
	
        <div id="ended_cancelled_zone" class="row" style="display:none" >
		
		  <!-- Bouton de suppression lorsque la campagne est terminée -->
		  <div class="col-lg-2 pull-right">
		    <button id="delete_button_ended_cancelled" type="button" class="btn btn-danger col-lg-12">Supprimer la campagne</button>
		  </div>

        </div>
	
		<!-- Script de mise à jour de l'affichage des données -->
		<script src="js/data_view/data.js" type="text/javascript"></script>
	
<?php require_once DIR_INCLUDES . 'footer.inc.php' ?>