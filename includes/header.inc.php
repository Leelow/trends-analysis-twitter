<?php

	require_once DIR_INCLUDES . 'secure.inc.php';
	require_once DIR_SYSTEM   . 'campaign.php';
	require_once DIR_SYSTEM   . 'campaignStyle.php';

    remember();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Trends Analysis Twitter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link href="assets/logo_mid_color.png" rel="shortcut icon" type="image/png">
	
	<!-- Necéssaires pour la carte et le graphique -->
	<script src="js/jquery.min.js" type="text/javascript"></script>
	
    <link rel="stylesheet" href="css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="css/bootswatch.min.css">
    <link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="css/bootstrap-custom.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="./js/html5shiv.js"></script>
      <script src="./js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
   <div class="container">
	<div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
         <a href="index.php" class="navbar-brand"><img src="assets/logo_mid_color.png" style="float:left;margin-right:5px;height:20px;with:auto"></img>Trends Analysis Twitter</a>
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
          <ul class="nav navbar-nav">
		    <?php menuStartedCampaign() ?>
		    <li>
			  <a href="create.php">+ Nouvelle campagne</a>
			</li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="themes">Campagnes programmées<span class="caret"></span></a>
              <ul class="dropdown-menu" aria-labelledby="themes">
				<?php menuScheduledCampaign() ?>          
              </ul>
            </li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="themes">Campagnes terminées<span class="caret"></span></a>
              <ul class="dropdown-menu" aria-labelledby="themes">
				<?php menuEndedOrCancelledCampaign() ?>          
              </ul>
            </li>
            <?php menuState() ?>
          </ul>
        </div>
      </div>
    </div>