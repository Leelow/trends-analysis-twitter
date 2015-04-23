<?php

    require_once dirname(dirname(__FILE__)) . '/config.php';
    require_once DIR_SYSTEM                 . 'campaign.php';

    if(!isset($_GET['step']))
        exit();

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
            $data[$i] = array('tweets'      => $i,
                              'min'         => '',
                              'quartile_1'  => '',
                              'mediane'     => '',
                              'moyenne'     => '',
                              'quartile_3'  => '',
                              'max'         => '');
        } else if(count($time_merge[$i]) == 1) {
            $total = str_replace('.', ',', $time_merge[$i][0]['total']);
            $data[$i] = array('tweets'      => $i,
                              'min'         => $total,
                              'quartile_1'  => $total,
                              'mediane'     => $total,
                              'moyenne'     => $total,
                              'quartile_3'  => $total,
                              'max'         => $total);
        } else {
            $totals = array();
            for($j = 0; $j < count($time_merge[$i]); $j++)
                array_push($totals, $time_merge[$i][$j]['total']);
            sort($totals);

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

            $data[$i] = array('tweets'      => $i,
                              'min'         => $min,
                              'quartile_1'  => $quartile_1,
                              'mediane'     => $mediane,
                              'moyenne'     => $moyenne,
                              'quartile_3'  => $quartile_3,
                              'max'         => $max);
        }
    }

    $step = max(0, $_GET['step']);

    $data_step = array($size / $step);

    // On fusionne certaines valeurs pour obtenir des tranches
    for($i = 0; $i < $size; $i += $step) {

        $min = $quartile_1 = $mediane = $moyenne = $quartile_3 = $max = 0;

        for($j = $i; $j < $i + $step; $j++) {

            $min        += $data[$j]['min'];
            $quartile_1 += $data[$j]['quartile_1'];
            $mediane    += $data[$j]['mediane'];
            $moyenne    += $data[$j]['moyenne'];
            $quartile_3 += $data[$j]['quartile_3'];
            $max        += $data[$j]['max'];

        }

        $data_step[$i / $step] = array('tweets'      => $i . ' - ' . ($i + $step),
                                       'min'         => $min         / $step,
                                       'quartile_1'  => $quartile_1  / $step,
                                       'mediane'     => $mediane     / $step,
                                       'moyenne'     => $moyenne     / $step,
                                       'quartile_3'  => $quartile_3  / $step,
                                       'max'         => $max         / $step);

    }

    $csv = '';

    // On imprime les données pour créer un CSV
    for($i = 0; $i < $size / $step; $i++) {

        // On remplace les . par des virgules
        $min        = str_replace('.', ',', $data_step[$i]['min']);
        $quartile_1 = str_replace('.', ',', $data_step[$i]['quartile_1']);
        $mediane    = str_replace('.', ',', $data_step[$i]['mediane']);
        $moyenne    = str_replace('.', ',', $data_step[$i]['moyenne']);
        $quartile_3 = str_replace('.', ',', $data_step[$i]['quartile_3']);
        $max        = str_replace('.', ',', $data_step[$i]['max']);

        $csv .= $data_step[$i]['tweets'] . ';' .  $min . ';' . $quartile_1 . ';' . $mediane . ';' . $moyenne .';' . $quartile_3 . ';' . $max . "\n";
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