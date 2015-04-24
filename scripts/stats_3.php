<?php

    require_once dirname(dirname(__FILE__)) . '/config.php';
    require_once DIR_SYSTEM                 . 'campaign.php';

    $size = 3000;

    // Exclusion
    $exclusion = [41, 56, 57, 62, 95];

    // Tranches
    $step = [10, 20, 40, 50, 60, 80, 100, 200, 250, 400, 500, 600, 800, 1000, 1250, 1500, 1750, 2000, 2250, 2500, 3000];
    $tol = 0.05;


    // Entête du fichier CSV
    $entete1 = ';+/- 5%;0.05;;;;;' . "\n";
    $entete2 = 'Tranche;Borne inf;Borne sup;Clean;Newman-Girvan;TF-IDF;SUGR;Total' . "\n";
    $entete = $entete1 . $entete2;


    // Tableau qui va contenir les données brutes
    $time_merge = array($size);
    for($i = 0; $i < $size; $i++)
        $time_merge[$i] = array();

    // Pour chacune des campagnes, on recupère l'ensemble des statistiques de temps d'exécution
    foreach(CampaignBdd::getEndedOrCancelledCampaign() as $campaign) {

        if(!in_array($campaign['id'], $exclusion)) {

            $campaign = new Campaign($campaign['id']);

            // On recupère les temps d'eéxécution des tweets de la campagne
            $time = $campaign->bdd->getExecTimeTotalAlg();

            // On fusionne les données
            for ($i = 0; $i < count($time['clean']); $i++) {
                $tweets = $time['clean'][$i]['tweets'];

                $temp = array();

                $temp['clean']  = ($time['clean']   != null) ? $time['clean'][$i]['time']   : '';
                $temp['ng']     = ($time['ng']      != null) ? $time['ng'][$i]['time']      : '';
                $temp['tf_idf'] = ($time['tf_idf']  != null) ? $time['tf_idf'][$i]['time']  : '';
                $temp['sugr']   = ($time['sugr']    != null) ? $time['sugr'][$i]['time']    : '';

                $temp['total'] = (($time['clean'][$i] != null) ? $time['clean'][$i]['time'] : 0) +
                    (($time['ng'][$i] != null) ? $time['ng'][$i]['time'] : 0) +
                    (($time['tf_idf'][$i] != null) ? $time['tf_idf'][$i]['time'] : 0) +
                    (($time['sugr'][$i] != null) ? $time['sugr'][$i]['time'] : 0);

                array_push($time_merge[$tweets], $temp);
            }

        }

    }

    $data_final = array();

    // On parcourt les tranches prédéfinies avec une tolérance de +/- la tolerance et on moyenne les données
    foreach($step as $tranche) {

        // On calcule les bornes infèrieurs et supérieures
        $borne_inf = floor($tranche - $tranche * $tol);
        $borne_sup = ceil($tranche + $tranche * $tol);

        $count = 0;
        $clean = $ng = $tf_idf = $sugr = $total = 0;
        for($i = max(0, $borne_inf); $i < min(3000, $borne_sup); $i++) {
            if(count($time_merge[$i]) > 1) {
                for($j = 0; $j < count($time_merge[$i]); $j++) {
                    $count++;
                    $clean  += $time_merge[$i][$j]['clean'];
                    $ng     += $time_merge[$i][$j]['ng'];
                    $tf_idf += $time_merge[$i][$j]['tf_idf'];
                    $sugr   += $time_merge[$i][$j]['sugr'];
                    $total  += $time_merge[$i][$j]['total'];
                }
            }
        }
        $count = max(1, $count);
        array_push($data_final, array('tranche' => $tranche,
            'borne_inf' => $borne_inf,
            'borne_sup' => $borne_sup,
            'clean' => $clean / $count,
            'ng' => $ng / $count,
            'tf_idf' => $tf_idf / $count,
            'sugr' => $sugr / $count,
            'total' => $total / $count));
    }

    $csv = '';
    // On imprime les données pour créer un CSV
    foreach($data_final as $tab){

        // On remplace les . par des virgules
        $clean  = str_replace('.', ',', $tab['clean']);
        $ng     = str_replace('.', ',', $tab['ng']);
        $tf_idf = str_replace('.', ',', $tab['tf_idf']);
        $sugr   = str_replace('.', ',', $tab['sugr']);
        $total  = str_replace('.', ',', $tab['total']);

        $csv .= $tab['tranche'] . ';' . $tab['borne_inf'] . ';' . $tab['borne_sup'] . ';' . $clean . ';' . $ng . ';' . $tf_idf . ';' . $sugr . ';' . $total . "\n";

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