<?php

	////////////////////////////////////////////////////////////////////
	//																  //
	//     Classe gérant l'export des données au format souhaité      //
	//																  //
	//																  //
	//																  //
	//																  //
	////////////////////////////////////////////////////////////////////

	require_once dirname(__DIR__) . '/config.php';
	require_once DIR_LIB . 'codebird/codebird.php';
	
	class CampaignExport {
	
		private $campaign;
	
		// Constructeur
		function __construct($campaign) {
			$this->campaign = $campaign;
		}
		
		// Retourne les données nécessaires à l'affichage d'une carte à l'aide de l'API Maps de Google (JSON)
		public function exportMaps() {
			$convert = array();
			foreach($this->campaign->bdd->getGeoLocs() as $row)
				array_push($convert, array($row['name'], $row['message'], $row['longitude'], $row['latitude']));			
			return json_encode($convert, JSON_PRETTY_PRINT);	 
		}
		
		// Retourne les données nécessaires à l'affichage d'un graphique du nombre de tweets à l'aide de Google Charts (JSON)
		public function chartsTweetsPerInterval() {  
			$data = array();
			foreach($this->campaign->bdd->getTweetsPerInterval() as $couple)
				array_push($data, array('c' => array(array('v' => date('H:i', $couple['UNIX_TIMESTAMP(date)'])), array('v' => $couple['count']))));

			$json_tab = array('cols' => array(array('label' => '', 'type' => 'string'),
											  array('label' => 'Nombre de tweets', 'type' => 'number')),
							  'rows' => $data);
			
			return json_encode($json_tab, JSON_PRETTY_PRINT);		
		}
		
		// Retourne les données nécessaires à l'affichage d'un graphique de l'algorithme de Newman Girvan à l'aide de Google Charts (JSON)
		public function chartsPolarityNewmanGirvan() {  
			$bddData = $this->campaign->bdd->getDataNewmanGirvan();
			self::normData($bddData);
			self::centerNewmanGirvanData($bddData);
		
			$data = array();
			$i = 0;
			foreach($bddData as $couple) {
				array_push($data, array('c' => array(array('v' => $i, 'f' => date('H:i', $couple['UNIX_TIMESTAMP(date)'])), array('v' => $couple['value']))));
				$i++;
			}
			
			$json_tab = array('cols' => array(array('label' => '', 'type' => 'number'),
											  array('label' => 'Newman Girvan', 'type' => 'number')),
							  'rows' => $data);

			return json_encode($json_tab, JSON_PRETTY_PRINT);		
		}
		
		// Retourne les données nécessaires à l'affichage d'un graphique de l'algorithme TF-IDF à l'aide de Google Charts (JSON)
		public function chartsPolarityTfIdf() {
			$bddData = $this->campaign->bdd->getDataTfIdf();
			self::normData($bddData);
		
			$data = array();
			$i = 0;
			foreach($bddData as $couple) {				
				array_push($data, array('c' => array(array('v' => $i, 'f' => date('H:i', $couple['UNIX_TIMESTAMP(date)'])), array('v' => $couple['value']))));
				$i++;
			}
			
			$json_tab = array('cols' => array(array('label' => '', 'type' => 'number'),
											  array('label' => 'TF IDF', 'type' => 'number')),
							  'rows' => $data);
			
			return json_encode($json_tab, JSON_PRETTY_PRINT);		
		}
		
		// Retourne au format json, le tableau des codes html des tweets issus de l'algorithme SUGAR
		public function tweetsSugar() {
			$tweets = array();
			foreach($this->campaign->getListTweets() as $file_tweets) {
				if(file_exists($file_tweets))
					array_push($tweets, file_get_contents($file_tweets));
			}
				
			return json_encode($tweets, JSON_PRETTY_PRINT);
			//print_r($this->campaign->getListTweets());
			/*$bddData = $this->campaign->bdd->getDataSugar();
			$json_tab = array();
			foreach($bddData as $couple)
				array_push($json_tab, array('step' => $couple['step'],
											'html' => $couple['html']));
			return json_encode($json_tab, JSON_PRETTY_PRINT);*/
		}
		
		// Retourne les données nécessaires à l'affichage du graphique SUGAR
		public function chartsPolaritySugar() {
		
			// On recupère les données des algorithmes, normalisées et centrées
			$bddDataNG = $this->campaign->bdd->getDataNewmanGirvan();
			self::normData($bddDataNG);
			self::centerNewmanGirvanData($bddDataNG);
			$count_NG = count($bddDataNG);
			
			$bddDataTF = $this->campaign->bdd->getDataTfIdf();
			self::normData($bddDataTF);
			$count_TF = count($bddDataTF);
			
			//echo $count_NG. " " .$count_TF . "\n";
			
			// On moyenne les valeurs quand c'est possible, sinon on prend la valeur disponible
			$dataMerged = array();
			for($i = 0; $i < max($count_NG, $count_TF); $i++) {
			
				// On moyenne les valeurs
				if($i < $count_NG and $i < $count_TF) {
					array_push($dataMerged, array('UNIX_TIMESTAMP(date)' => $bddDataTF[$i]['UNIX_TIMESTAMP(date)'],
												  'value'				 => round(($bddDataTF[$i]['value'] + $bddDataNG[$i]['value']) / 2, 2)));
				} else if($i < $count_NG) {
					array_push($dataMerged, array('UNIX_TIMESTAMP(date)' => $bddDataNG[$i]['UNIX_TIMESTAMP(date)'],
												  'value'				 => $bddDataNG[$i]['value']));
				} else {
					array_push($dataMerged, array('UNIX_TIMESTAMP(date)' => $bddDataTF[$i]['UNIX_TIMESTAMP(date)'],
												  'value'				 => $bddDataTF[$i]['value']));
				}
			}
			
			$i = 0;
			$data = array();
			foreach($dataMerged as $couple) {
				array_push($data, array('c' => array(array('v' => $i, 'f' => date('H:i', $couple['UNIX_TIMESTAMP(date)'])), array('v' => $couple['value']))));
				$i++;
			}
			
			$json_tab = array('cols' => array(array('label' => '', 'type' => 'number'),
											  array('label' => 'Moyenne TF-IDF/Newman Girvan', 'type' => 'number')),
							  'rows' => $data);
			
			return json_encode($json_tab, JSON_PRETTY_PRINT);	
			
		}
	
		// Centre les les données de Newman Girvan
		private static function centerNewmanGirvanData(&$data) {
		
			for($i = 0; $i < count($data); $i++)
				$data[$i]['value'] = ($data[$i]['value'] * 2) - 1;
		
		}
	
		// Normalisation des valeurs issues des algo NG et TF-IDF
		private static function normData(&$data) {
		
			// On determine la valeur maximale en absolu, elle correspond au terme de normalisation
			$norm = 1;
			foreach($data as $couple) {
				$abs = abs($couple['value']);
				if($abs > $norm)
					$norm = $abs;
			}
			
			// On normalise chacun des termes
			for($i = 0; $i < count($data); $i++)
				$data[$i]['value'] = round($data[$i]['value'] / $norm, 2);
		}
	
	
		// Retourne des données diverses
		public function exportData() {
			$json_tab = array('size'          => $this->campaign->getSize(),
			                  'keywords'      => $this->campaign->getKeywords(),
							  'total_tweets'  => $this->campaign->getTotalTweets(),
							  //'total_twittos' => $this->campaign->getTotalTwittos(),
							  'percentage'    => $this->campaign->getPercentage(),
							  'state'  		  => $this->campaign->state,
							  'begin'         => date('d/m H:i', $this->campaign->begin),
							  'step'		  => $this->campaign->step,
							  'end'           => date('d/m H:i', $this->campaign->begin + $this->campaign->length * 60),
							  'time_now'      => date('H:i', time()));
			return json_encode($json_tab, JSON_PRETTY_PRINT);		
		}
		
		// Recupère le code html d'un tweet à partir de son id (sans le widget)
		public function exportTweetHTML($id) {
			// On utilise le framework Codebird
			\Codebird\Codebird::setConsumerKey(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
			$cb = \Codebird\Codebird::getInstance();
			$cb->setToken(OAUTH_TOKEN, OAUTH_SECRET);
			$api = 'statuses_oembed';
			$params = array(
				'id' 			=> $id,
				'hide_media' 	=> false,
				'hide_thread' 	=> true,
				'lang'			=> 'fr',
				'omit_script' 	=> true
			);
			$data = (array) $cb->$api($params);
			return $data['html'];
		}
		
		// Recupère le code html de plusieurs tweets à partir de leurs id
		public function exportTweetsHTML($ids) {
			// On utilise le framework Codebird
			\Codebird\Codebird::setConsumerKey(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
			$cb = \Codebird\Codebird::getInstance();
			$cb->setToken(OAUTH_TOKEN, OAUTH_SECRET);
			$api = 'statuses_oembed';
			$datas = array();
			foreach($ids as $id) {
				$params = array(
					'id' 			=> $id,
					'hide_media' 	=> true,
					'hide_thread' 	=> true,
					'lang'			=> 'fr',
					'omit_script' 	=> true
				);
				$data = (array) $cb->$api($params);
				array_push($datas, $data['html']);
			}
			
			return $datas;
		}
	
	}

?>