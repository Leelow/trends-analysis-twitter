<?php

/**
 * Utilisation de la bibliothèque Open Source Phirehose : https://github.com/fennb/phirehose
 * pour collecter les tweets associés à des mot-clés ou des hashtags
 *
 * Ce script est fortement inspiré du script suivant : https://github.com/fennb/phirehose/blob/master/example/ghetto-queue-collect.php
 *
 * L'idée est de collecter en temps réel les tweets correspondants aux critères établis.
 * A intervalle régulier (environ 1 minute), on les enregistre. Cela permet d'éviter
 * de les enregistrer en temps réel et ainsi de ralentir le processus, ce qui peut provoquer
 * un ban de l'application dont on utilise les clés ainsi que de l'ip.
 */

require_once dirname(__DIR__) . '/config.php';
require_once DIR_LIB 		  . 'phirehose/Phirehose.php';
require_once DIR_LIB 	  	  . 'phirehose/OauthPhirehose.php';
 
class GhettoQueueCollector extends OauthPhirehose {

	private $number_tweets_filtered = 0; 	// Nombre de tweets récupérés dans l'intervalle courant
	private $campaign;						// Campagne courante

	/**
	* Constantes spécifiques à cette classe héritée
	*/
	const QUEUE_FILE_PREFIX = '';			// Préfixe de chaque fichier contenant les tweets
	const QUEUE_FILE_ACTIVE = '.current';	// Préxixe du fichier courant contenant les tweets

	/**
	* Attributs spécifiques à cette classe héritée
	*/
	protected $queueDir;
	protected $rotateInterval;
	protected $streamFile;
	protected $statusStream;
	protected $lastRotated;

	/**
	* Redéfinition du constructeur de la classe
	*
	* @param string $username OAUTH_TOKEN de l'application
	* @param string $password OAUTH_SECRET de l'application
	* @param Campaign $campaign Campagne associée à la collecte
	*/
	public function __construct($username, $password, $campaign) {

		$this->campaign = $campaign;
	  
		// Emplacement où seront sauvegardés les tweets filtrés
		$this->queueDir = $this->campaign->getTweetsDir();
		
		// Intervalle d'enregistrement des fichiers (fixé à 60, à toucher avec précautions). Il ne faut jamais descendre en dessous de 10 secondes
		$this->rotateInterval = 59;
		
		// On affiche les informations sur la campagne
		$str_keywords = '';
		foreach($this->campaign->getKeywords() as $keyword)
			$str_keywords .= $keyword . ', ';
		$str_keywords = '{' . substr($str_keywords, 0, strlen($str_keywords) - 2) . '}';
		echo 'Debut de la campagne "' . $this->campaign->name . '" le ' . date('d-M-Y \a H:i:s', $this->campaign->begin) . ' concernant ' . $str_keywords . ' pour une duree de ' . $this->campaign->length . ' minutes.' . "\n" . "\n";
		
		// On appelle le constructeur parent
		return parent::__construct($username, $password, Phirehose::METHOD_FILTER, self::FORMAT_JSON, 'en');
		
	}

	/**
	* Empile les tweets arrivants
	*
	* @param string $status Tweet au format json
	*/
	public function enqueueStatus($status) {

		$data = json_decode($status, true);
		
			// On vérifie la validité du tweet
			if (is_array($data) && isset($data['user']['screen_name'])) {

				// Ecrit le tweet dans le flux du fichier (doit utlisé getStream() pour des raisons de performances)
				fputs($this->getStream(), $status . "\n");

				// On incrèmente le nombre de tweets filtrés de l'intervalle courant
				$this->number_tweets_filtered++;
				
				// On ajoute l'utilisateur à la liste courante des utilisateurs ayant twittés		   
				/*$this->campaign->addUser(array('id' 				=> $data['user']['id'],
											   'followers_count' 	=> $data['user']['followers_count'],
											   'favourites_count'	=> $data['user']['favourites_count']));*/
					
				// On ajoute les coordonnées à la liste si elles sont disponibles
				if(!is_null($data['geo']))
					$this->campaign->addGeoLoc(array('name' 		=> $data['user']['name'],
													 'message'		=> $data['text'],
													 'longitude'	=> $data['geo']['coordinates'][0],
													 'latitude'		=> $data['geo']['coordinates'][1]));
			}
			

		// Il faut remarquer qu'un fichier ne sera généré si aucun tweet n'est arrivé dans l'intervalle de temps courant
		$now = time();
		if (($now - $this->lastRotated) > $this->rotateInterval) {
		
			// Effectue la rotation des fichiers de tweets
			$this->lastRotated = $now;
			$this->rotateStreamFile();
			
		}

	}

	/**
	* Retourne le flux sur le fichier de tweets courant
	*
	* @return resource flux sur le fichier de tweets courant
	*/
	private function getStream() {
	
		// Si le flux existe déjà et est valide, on le retourne
		if (is_resource($this->statusStream))
			return $this->statusStream;

		// Construit le stream et l'indique dans les logs
		$this->streamFile = $this->queueDir . '/' . self::QUEUE_FILE_ACTIVE;
		$this->log('Ouverture du nouveau flux : ' . $this->streamFile);
		$this->statusStream = fopen($this->streamFile, 'a'); // Ajoute si existant (crash recovery)

		// Si c'est le premier flux, alors c'est aussi le précédent
		if ($this->lastRotated == NULL)
			$this->lastRotated = time();

		// On retourne le stream sur le fichier
		return $this->statusStream;

	}

	/**
	* Effectue la rotation des fichiers
	*/
	private function rotateStreamFile() {

		// On ferme le flux courant
		fclose($this->statusStream);

		// On génère un nom unique pour le fichier de tweets
		$queueFile = $this->queueDir . '/' . self::QUEUE_FILE_PREFIX . date('d_m_Y H_i_s') . '.json';
		
		// On effectue la rotation
		rename($this->streamFile, $queueFile);

		// On enregistre le fichier de tweets
		$this->campaign->registerFileTweets(basename($queueFile), filesize($queueFile), $this->number_tweets_filtered);
		
		// On affiche le nombre de tweets filtrés récoltés
		if ($this->number_tweets_filtered > 0) 
			echo $this->number_tweets_filtered . ' tweets ont ete collectes.' . "\n";
		else
			echo 'Aucun tweet n a ete collecte.' . "\n";
		
		// On exécute les diffèrents algortihmes ici
		$this->campaign->execClean(); // L'algorithme TF-IDF est executé une fois Newman Girvan terminé
		
		$this->campaign->step++;
		$this->number_tweets_filtered = 0;

		// On sauvegarde les nouvelles données de la campagne
		$this->campaign->maj();
		
		// On vérifie que tout s'est bien déroulé
		if (!file_exists($queueFile))
			throw new Exception('Erreur de la rotation avec le fichier : ' . $queueFile);

		// Tout s'est bien déroulé - le prochain appel de getStream() va créer un nouveau fichier
		$this->log('Rotation du flux actif effectuée : ' . $queueFile);
			
		// On arrête la campagne si on a atteint la durée demandée
		if($this->campaign->step >= $this->campaign->length) {
		
			// On indique que la campagne est terminée
			$this->campaign->ended();
		
			$name         = $this->campaign->name;
			$total        = $this->campaign->getTotalTweets();
			$str_keywords = '';
				foreach($this->campaign->getKeywords() as $keyword)
					$str_keywords .= $keyword . ', ';
				$str_keywords = '{' . substr($str_keywords, 0, strlen($str_keywords) - 2) . '}';
			$length       = $this->campaign->length;
			$date		  = date('d-M-Y \a H:i:s', time());

			if($total == 0) {
			
				$this->log('Campagne "' . $name . '" terminée le ' . $date . ', aucun tweet concernant ' . $str_keywords. ' n\'a été recolté en ' . $length . ' minutes.');
				echo "\n" . 'Campagne "' . $name . '" terminee le ' . $date . ', aucun tweet concernant ' . $str_keywords . ' n a ete recolte en ' . $length . ' minutes.' . "\n";
				
			} else {
			
				$this->log('Campagne "' . $name . '" terminée le ' . $date . ', ' . $total . ' tweets concernant ' . $str_keywords . ' ont été récoltés en ' . $length . ' minutes.');
				echo "\n" . 'Campagne "' . $name . '" terminee le ' . $date . ', ' . $total . ' tweets concernant ' . $str_keywords . ' ont ete recoltes en ' . $length . ' minutes.' . "\n";
		
			}
			
			exit();
		
		// On s'arrête ici si la campagne a été annulée
		} else if($this->campaign->state == 'CANCELLED') {
			exit();
		}
	
	}
	
}

?>