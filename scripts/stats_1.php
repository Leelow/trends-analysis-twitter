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
    $data = array($size);

    for($i = 0; $i < $size; $i++) {
        if(count($time_merge[$i]) == 0) {
            $data[$i] = array($i, '', '', '', '', '', '', '');
        } else if(count($time_merge[$i]) == 1) {
            $total = $time_merge[$i][0]['total'];
            $data[$i] = array($i, $total, $total, $total, $total, $total, $total, $total);
        } else {
            $totals = array();
            for($j = 0; $j < count($time_merge[$i]); $j++)
                array_push($totals, $time_merge[$i][$j]['total']);
            asort($totals);

            // On dispose d'un simple tableau de valeurs triées en ordre croissant, on peut effectuer des mesures statistiques dessus
            //'Nb tweets;Min;Quartile 1;Médiane;Moyenne;Quartile 3;Max'
            $min    = $totals[0];
            $max    = $totals[count($totals) - 1];

            $moyenne = array_sum($totals) / count($totals);

            // Calcul des quartiles
            $quartile_1 = $totals[ceil((count($totals) / 4)) - 1];
            $quartile_3 = $totals[ceil(((count($totals) / 4) * 3)) - 1];

            // Calcul de la medianne
            if(count($totals) % 2 == 1) {
                $mediane = $totals[((count($totals) + 1) / 2 - 1)];
            } else {
                $val1 = $totals[(count($totals) / 2) - 1];
                $val2 = $totals[((count($totals) / 2) + 1) - 1];
                $mediane = ($val1 + $val2) / 2;
            }

            $data[$i] = array($i, $min, $quartile_1, $mediane, $moyenne, $quartile_3, $max);
        }
    }

    $csv = '';

    // On imprime les données pour créer un CSV
    for($i = 0; $i < $size; $i++) {
        $csv .= $data[$i][0] . ';' .  $data[$i][1] . ';' . $data[$i][2] . ';' . $data[$i][3] . ';' . $data[$i][4] .';' . $data[$i][5] . ';' . $data[$i][6] . "\n";
    }

    $data = $entete . $csv;

    header ( 'HTTP/1.1 200 OK' );
    header ( 'Date: ' . date ( 'D M j G:i:s T Y' ) );
    header ( 'Last-Modified: ' . date ( 'D M j G:i:s T Y' ) );
    header ( 'Content-Type: application/vnd.ms-excel') ;
    header ( 'Content-Disposition: attachment;filename=export.csv' );
    print chr(255) . chr(254) . mb_convert_encoding($data, 'UTF-16LE', 'UTF-8');
    exit;

?>