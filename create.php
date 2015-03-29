<?php
	
	include_once 'config.php';
	include_once DIR_INCLUDES . 'header.inc.php';

?>
	
      <!-- Forms
      ================================================== -->
      <div style="margin-top:5%">

	    <div class="row">
            <div class="bs-component col-lg-8 col-lg-offset-2">
              <div class="alert alert-dismissible alert-success" style="display:none" id="message_box">
				<div id="message"></div>
              </div>
            </div>		
		</div>
        <div class="row">
		  <div class="col-lg-2"></div>
          <div class="col-lg-8">
            <div class="well bs-component">
              <form class="form-horizontal">
                <fieldset>
                  <legend>Création d'une nouvelle campagne</legend>
                  <div class="form-group" id="input_name">
                    <label for="inputText" class="col-lg-3 control-label">Nom de la campagne</label>
                    <div class="col-lg-9">
                      <input type="text" class="form-control" id="campaign_name" placeholder="Nom de la campagne">
                    </div>
                  </div>

					<script type="text/javascript" src="js/calendar/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
					<script type="text/javascript" src="js/calendar/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
					
					<div class="form-group" id="input_begin">
						<label for="dtp_input3" class="col-md-3 control-label">Date de début</label>
							<div class="col-lg-9">
							<div class="input-group date form_time col-md-5" data-date="" data-date-format="yyyy-mm-dd hh:ii" data-link-field="dtp_input3" data-link-format="yyyy-mm-dd hh:ii">
								<input class="form-control" id="campaign_begin" type="text" placeholder="<?php echo date('Y-m-d H:i', time() + 2 * 60) ?>" value="" readonly >
								<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
							</div>
							
						</div>
					</div>
                  <div class="form-group" id="input_length">
                    <label for="inputText" class="col-lg-3 control-label">Durée (en minutes)</label>
                    <div class="col-lg-9">
                      <input type="text" class="form-control" id="campaign_length" placeholder="Durée (en minutes)">
                    </div>
                  </div>
					
                  <div class="form-group" id="input_keywords">
                    <label for="textArea" class="col-lg-3 control-label">Mots-clés/hashtags</label>
                    <div class="col-lg-9">
                      <textarea class="form-control" rows="3" id="campaign_keywords" placeholder="#hashtag"></textarea>
                      <span class="help-block" id="help">Veillez à séparer les mots-clés/hashtags en revenant à la ligne.</span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-4">
					  <button id="cancel" type="button" class="btn btn-danger col-lg-2" onclick="document.location='index.php';">Annuler</button>
					  <div class="col-lg-1"></div>
                      <button id="create" type="button" class="btn btn-success col-lg-2">Créer</button>
                    </div>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
		<div class="col-lg-2"></div>
      </div>	
	<script src="js/create/create.js" type="text/javascript"></script>
<?php

	include_once DIR_INCLUDES . 'footer.inc.php';

?>