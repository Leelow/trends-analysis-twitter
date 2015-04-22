<?php

    require_once dirname(dirname(__FILE__)) . '/config.php';
    require_once DIR_SYSTEM                 . 'campaign.php';

    // Entête du fichier CSV
    $entete = 'Nom;Moyenne tweets/minute;Clean;Newman-Girvan;TF-IDF;SUGR' . "\n";

    // On recupère la liste des id des campagnes terminées/annulées
    $list = CampaignBdd::getEndedOrCancelledCampaign();

    $csv = '';

    // Pour chacune des campagnes, on extrait la moyenne de nombre de tweets traités par minute
    foreach($list as $campaign) {

        $campaign = new Campaign($campaign['id']);

        // On recupère le nom de la campagne
        $line = $campaign->name . ';';

        // On recupère le temps d'exécution réel et le nombre total de tweets pour effectuer une moyenne de tweets traités par minute
        $total = $campaign->getTotalTweets();
        $step = $campaign->step;
        $line .= round($total / $step) . ';';

        $time = $campaign->bdd->getExecTimeAlg();

        $line .= $time['clean'] . ';' . $time['ng'] . ';' . $time['tf_idf'] . ';' . $time['sugr'];

        $csv .= $line . "\n";

    }

    header('Content-Encoding: UTF-8');
    header('Content-type: text/csv; charset=UTF-8');
    header('Content-Disposition:attachment;filename="stats_execution_time.csv"');

    echo $entete . $csv;

?>