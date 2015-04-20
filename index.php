<?php

	require_once 'config.php';
	require_once DIR_INCLUDES . 'header.inc.php';
	
?>
	<br>
	<br>
	<div class="row">
	  <div class="col-lg-6">
		<h2>Quelques informations !</h2>
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
				<div class="modal-footer">
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  <button type="button" class="btn btn-primary">Save changes</button>
				</div>
			  </div>
			</div>
		  </div>
		<div style="display: none;" id="source-button" class="btn btn-primary btn-xs">&lt; &gt;</div></div>
	  </div>
	  <div class="col-lg-6">
		<h2>Popovers</h2>
		<div class="bs-component">
		  <button title="" data-original-title="" type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Left</button>

		  <button title="" data-original-title="" type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Top</button>

		  <button title="" data-original-title="" type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Vivamus
		  sagittis lacus vel augue laoreet rutrum faucibus.">Bottom</button>

		  <button title="" data-original-title="" type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Right</button>
		</div>
		<h2>Tooltips</h2>
		<div class="bs-component">
		  <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="" data-original-title="Tooltip on left">Left</button>

		  <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="" data-original-title="Tooltip on top">Top</button>

		  <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Tooltip on bottom">Bottom</button>

		  <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="right" title="" data-original-title="Tooltip on right">Right</button>
		</div>
	  </div>
    </div>

<?php

	require_once DIR_INCLUDES . 'footer.inc.php';;
	
?>