<?php

	require_once 'config.php';
	require_once DIR_INCLUDES . 'header.inc.php';
	
?>
	<br>
	<br>
	<div class="row">
	  <div class="col-lg-5">
		<h2>Panneau de contrôle</h2>
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
	  <div class="col-lg-5">
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

<?php

	require_once DIR_INCLUDES . 'footer.inc.php';;
	
?>