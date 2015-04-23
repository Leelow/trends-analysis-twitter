<?php

    require_once dirname(dirname(__FILE__)) . '/config.php';
    require_once DIR_SYSTEM                 . 'campaign.php';

    $size = 3000;

    // Entête du fichier CSV
    $entete = 'Nb tweets;Min;Quartile 1;Médiane;Moyenne;Quartile 3;Max' . "\n";

    // Tableau qui va contenir les données brutes
    $time_merge = Array($size);
    for($i = 0; $i < $size; $i++)
        $time_merge[$i] = Array();

    // Pour chacune des campagnes, on recupère l'ensemble des statistiques de temps d'exécution
    foreach(CampaignBdd::getEndedOrCancelledCampaign() as $campaign) {

        $campaign = new Campaign($campaign['id']);

        // On recupère les temps d'eéxécution des tweets de la campagne
        $time = $campaign->bdd->getExecTimeTotalAlg();

        // On fusionne les données
        for($i = 0; $i < count($time['clean']); $i++) {
            $tweets = $time['clean'][$i]['tweets'];

            $temp = array();

            $temp['clean']  = ($time['clean']   != null) ? $time['clean'][$i]['time']   : '';
            $temp['ng']     = ($time['ng']      != null) ? $time['ng'][$i]['time']      : '';
            $temp['tf_idf'] = ($time['tf_idf']  != null) ? $time['tf_idf'][$i]['time']  : '';
            $temp['sugr']   = ($time['sugr']    != null) ? $time['sugr'][$i]['time']    : '';

            $temp['total']  = (($time['clean'][$i]   != null) ? $time['clean'][$i]['time']   : 0) +
                              (($time['ng'][$i]      != null) ? $time['ng'][$i]['time']      : 0) +
                              (($time['tf_idf'][$i]  != null) ? $time['tf_idf'][$i]['time']  : 0) +
                              (($time['sugr'][$i]    != null) ? $time['sugr'][$i]['time']    : 0);

            array_push($time_merge[$tweets], $temp);
        }

    }

    // On fusionne les données disponibles pour chaque campagne en un unique tableau
    /*$data = array($size);

    for($i = 0; $i < $size; $i++) {
        if()
    }*/


    /*$data = $entete . $csv;

    header ( 'HTTP/1.1 200 OK' );
    header ( 'Date: ' . date ( 'D M j G:i:s T Y' ) );
    header ( 'Last-Modified: ' . date ( 'D M j G:i:s T Y' ) );
    header ( 'Content-Type: application/vnd.ms-excel') ;
    header ( 'Content-Disposition: attachment;filename=export.csv' );
    print chr(255) . chr(254) . mb_convert_encoding($data, 'UTF-16LE', 'UTF-8');
    exit;*/

    print_r($time_merge);

?>