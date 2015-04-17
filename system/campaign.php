<?php

	require_once DIR_SYSTEM . 'campaignException.php';
	require_once DIR_SYSTEM . 'campaignBdd.php';
	require_once DIR_SYSTEM . 'campaignExport.php';
	require_once DIR_SYSTEM . 'ghettoQueueCollector.php';
	
	
	class Campaign {
	
		public $id;							// Identifiant unique de la campagne
		public $name;						// Nom de la campagne
		public $state;						// Statut de la campagne
		public $begin;						// Timestamp du début de la campagne
		public $step;						// Minute courante
		public $length;						// Durée de la campagne (en minutes)
		public $keywords;					// Hashtag ou mots-clés associés à la recherche
		
		public $bdd;						// Gestion de la connection avec la base de données
		public $export;						// Gestion de l'export des données aux formats désirés
	
		private $CAMPAIGN_ROOT;				// Dossier de la campagne courante
		private $CAMPAIGN_TWEETS;			// Dossier des tweets de la campagne courante
		private $CAMPAIGN_CLEAN_TWEETS;		// Dossier des tweets nettoyés de la campagne courante
		private $CAMPAIGN_NEWMAN_GIRVAN;	// Dossier des fichiers générés par l'algorithme de Newman Girvan
		private $CAMPAIGN_TF_IDF;			// Dossier des fichiers générés par l'algorithme tf-idf
		private $CAMPAIGN_SUGAR;			// Dossier des fichiers html de tweets
	
		function __construct() {

			// Constructeur d'une nouvelle campagne à partir de $name, $begin, $length, $keywords
			if(func_num_args() == 4) {
		
				$id = NULL;
		
				// On recupère les attributs
				$attributes = func_get_args();		
				$this->name     = $attributes[0];
				$this->begin    = $attributes[1];
				$this->length   = $attributes[2];
				$this->keywords = $attributes[3];
				
				// Par défaut, une campagne est en mode planifiée
				$this->state    = 'SCHEDULED';
				
				// Intervalle courant
				$this->step = 0;
				
				// Si le dossier des campagnes n'existe pas			
				if(!file_exists(DIR_CAMPAIGN))
					mkdir(DIR_CAMPAIGN);
				
				// On initalise la campagne dans la base de données
				$this->bdd = new CampaignBdd($this);
				$this->bdd->initialize_campaign();
				
				// On vérifie que l'intervalle est disponible, si ce n'est pas le cas on supprime la campagne
				$comment = array();
				if(!$this->isAvailable($comment)) {
					$this->delete();
					throw new CampaignException('Cette plage horaire est deja occupee.', 1, $comment); // Code indiquant que la campagne est déjà occupée
				}
				
				// On initialise la gestion de l'export des données
				$this->export = new CampaignExport($this);
				
				// On définit les constantes
				$this->CAMPAIGN_ROOT			= DIR_CAMPAIGN . $this->id . '/';
				$this->CAMPAIGN_TWEETS			= $this->CAMPAIGN_ROOT . 'tweets/';
				$this->CAMPAIGN_CLEAN_TWEETS	= $this->CAMPAIGN_ROOT . 'tweets_clean/';
				$this->CAMPAIGN_NEWMAN_GIRVAN	= $this->CAMPAIGN_ROOT . 'newman_girvan/';
				$this->CAMPAIGN_TF_IDF			= $this->CAMPAIGN_ROOT . 'tf_idf/';
				$this->CAMPAIGN_SUGAR			= $this->CAMPAIGN_ROOT . 'sugar/';
				
				// On créer les diffèrents dossiers pour la campagne
				mkdir($this->CAMPAIGN_ROOT);
				mkdir($this->CAMPAIGN_TWEETS);
				mkdir($this->CAMPAIGN_CLEAN_TWEETS);
				mkdir($this->CAMPAIGN_NEWMAN_GIRVAN);
				mkdir($this->CAMPAIGN_TF_IDF);
				mkdir($this->CAMPAIGN_SUGAR);
			
			// Constructeur d'une campagne à partir de son id	
			} else if(func_num_args() == 1) {

				$this->id = func_get_args()[0];

				// On recupère l'ensemble des paramètres de la campagne depuis la base de données
				$this->bdd = new CampaignBdd($this);
				$this->bdd->getCampaignFromBdd();
				
				// On définit les constantes
				$this->CAMPAIGN_ROOT   			= DIR_CAMPAIGN . $this->id . '_' . $this->name . '/';
				$this->CAMPAIGN_TWEETS 			= $this->CAMPAIGN_ROOT . 'tweets/';
				$this->CAMPAIGN_CLEAN_TWEETS 	= $this->CAMPAIGN_ROOT . 'tweets_clean/';
				$this->CAMPAIGN_NEWMAN_GIRVAN 	= $this->CAMPAIGN_ROOT . 'newman_girvan/';
				$this->CAMPAIGN_TF_IDF 		  	= $this->CAMPAIGN_ROOT . 'tf_idf/';
				$this->CAMPAIGN_SUGAR			= $this->CAMPAIGN_ROOT . 'sugar/';
				
				// On initialise la gestion de l'export des données
				$this->export = new CampaignExport($this);
			
			} else {
			
				throw new CampaignException('Erreur d\'initialisation d\'une campagne.');
			
			}
		
		}

        // Retourne le dossier de sauvegarde des tweets de la campagne (sans de "/")
        public function getTweetsDir() {
            return $this->CAMPAIGN_ROOT . 'tweets';
        }

		// Planifie une campagne
		public function scheduled() {
			// On extrait les données necessaires du timestamp pour la commande cron
			$cron_date    = intval(date('i', $this->begin)) . ' ' . date('G j n', $this->begin) . ' *';
			$cron_command = ' php "' . PATH_SCRIPT_START_CAMPAIGN . '" ' . $this->id;
			
			if (PHP_OS !== 'WINNT') {
				exec('(crontab -l ; echo "' . $cron_date . ' ' . $cron_command . '") | sort - | uniq - | crontab -');
			}
			
		}
	
		// Démarre une campagne
		public function start() {
			$this->bdd->setState('STARTED');
			
			$sc = new GhettoQueueCollector(OAUTH_TOKEN, OAUTH_SECRET, $this);
			$sc->setTrack($this->keywords);
			$sc->consume();		
		}
		
		// Supprime la campagne
		public function delete() {
		
			$this->bdd->delete();
		
			if (PHP_OS === 'WINNT') {
			
				exec('rd /s /q "' . $this->CAMPAIGN_ROOT . '"');
				
			} else {
			
				exec('rm -rf "' . $this->CAMPAIGN_ROOT . '"');
				
			}
		
		}
		
		// Met à jour l'avancement de la campagne
		public function maj() {
			$this->bdd->setStep($this->step);
		}
	
		// Permet d'indiquer que la campagne est annulée
		public function cancel() {
			$this->state = 'CANCELLED';
			$this->bdd->setState('CANCELLED');
		}
		
		// Permet d'indiquer que la campagne est terminée
		public function ended() {
			$this->state = 'ENDED';
			$this->bdd->setState('ENDED');
		}
	
		// Ajoute des coordonnées à la liste à partir d'un tableau de coordonnées
		public function addGeoLoc($coo) {
			$this->bdd->add_geoloc($coo['name'], $coo['message'], $coo['longitude'], $coo['latitude']);		
		}
	
		// Ajoute des utilisateurs à la liste d'utilisateurs
		public function addUser($user) {
			$this->bdd->add_user($user['id'], $user['followers_count'], $user['favourites_count']);
		}
	
		// Retourne l'estimation du poids des données collectées correctement formaté
		public function getSize() {
			$size = $this->bdd->getTotalTweetsSize();
			for ($i = 0; $size > 1024; $i++)
			$size /= 1024;
		
			$units = explode(' ', 'o Ko Mo Go');
			
			if($units[$i] == 'Go')
				$size = round($size, 1);
			else
				$size = round($size);
			
			$endIndex = strpos($size, '.') + 3;

			return substr($size, 0, $endIndex) . ' ' . $units[$i];				
		}
			
		// Affiche la liste des hashtags ou mots-clés associés à la campagne
		public function getKeywords() {
			return $this->bdd->getKeywords();		
		}
		
		// Affiche la liste des hashtags ou mots-clés associés à la campagne
		public function getTotalTwittos() {
			return $this->bdd->getTotalTwittos();		
		}
		
		// Enregistre le fichier de tweets dans la liste	
		public function registerFileTweets($file, $size, $count) {
			$this->bdd->add_file_tweets($file, $size, $count);
		}
		
		// Retourne le total de tweets de la campagne
		public function getTotalTweets() {
			return $this->bdd->getTotalTweets();			
		}
		
		// Retourne le pourcentage d'avancement de la campagne [0-100]
		public function getPercentage() {
			return round(($this->step / $this->length) * 100, 0);
		}
		
		// Indique si la campagne est terminée (terminée ou annulée)
		public function endedOrCancelled() {
			return ((time() - ($this->begin + $this->step * 60)) > 65);		
		}
		
		// Vérifie que le l'intervalle de temps [$begin_current ; $begin_current + $length_current * 60] est disponible
		public function isAvailable(&$comment = NULL) {
			$available = true;
			$begin_current = $this->begin;
			$end_current   = $begin_current + $this->length * 60;	
			foreach($this->bdd->getIntervalCampaign() as $interval) {
				$begin = $interval['UNIX_TIMESTAMP(begin)'];
				$end   = $interval['UNIX_TIMESTAMP(begin)'] + $interval['length'] * 60;
				if(($begin         <= $begin_current and $begin_current <= $end)         or // Si le début de la campagne chevauche une autre campagne
				   ($begin         <= $end_current   and $end_current   <= $end)         or // Si la fin de la campagne chevauche une autre campagne
				   ($begin_current <= $begin         and $begin         <= $end_current) or // Si le début d'une des campagnes chevauche le début de la campagne
				   ($begin_current <= $end           and $end           <= $end_current)) { // Si la fin d'une des campagnes chevauche la fin de la campagne
					$available = false;
					$comment = array('name'   => $interval['name'],
									 'begin'  => date('Y-m-d H:i:s', $begin),
									 'end'    => date('Y-m-d H:i:s', $end));
				}
			}
			return $available;
		}
		
		// Recupère les n tweets les plus récents si possibles
		public function getRecentTweetsId($n) {
			$file = $this->bdd->getLastTweetsFile();
			if($file == '')
				return array();
			else {
				// On recupère les n derniers tweets du fichier
				$tweets_id = array();
				$file = $this->CAMPAIGN_TWEETS . $file;
				$data = file($file);
				$length = count($data);
				for($i = 1; $i <= $length; $i++) {
					// Si on n'atteint pas la fin du fichier
					$line_tweet = $length - $i;
					if($line_tweet < 0 or count($tweets_id) == $n)
						break;
					else {
						$tweet = json_decode($data[$line_tweet], true);
						if(!isset($tweet['retweeted_status'])) {
							// On recupère les id
							array_push($tweets_id, $tweet['id_str']);							
						}
					}
				}
				return $tweets_id;
			}
		
		}
		
		// Retourne l'id et le nom de la campagne démarée
		public function getStartedCampaign() {
			return $this->bdd->getStartedCampaign();
		}
		
		// Execute le script de nettoyage en background
		public function execClean() {
			$input   = $this->CAMPAIGN_TWEETS . $this->bdd->getLastTweetsFile();
			$output  = $this->CAMPAIGN_CLEAN_TWEETS . basename($this->bdd->getLastTweetsFile());
			$command = 'php "' . CLEAN_PROCESS_SCRIPT . '" "' . $this->id . '" "' . $input . '" "' . $output . '"';

			if (PHP_OS === 'WINNT') {
				$command = str_replace('\\', '\\\\', str_replace('/', '\\', $command));
				$command = substr($command, 4);
				$WshShell = new COM("WScript.Shell");
				$oExec = $WshShell->Run(PATH_PHP_EXE . ' -f ' . $command, 0, false);
			} else {
				shell_exec($command . ' > /dev/null 2>/dev/null &');
			}
		}
		
		// Execute le script de Newman-Girvan en background
		public function execNewmanGirvan() {
			$input   = $this->CAMPAIGN_CLEAN_TWEETS . $this->bdd->getLastTweetsFile();
			$output  = $this->CAMPAIGN_NEWMAN_GIRVAN . basename($this->bdd->getLastTweetsFile());
			$command = 'php "' . NEWMAN_GIRVAN_SCRIPT . '" "' . $this->id . '" "' . $input . '" "' . $output . '"';
			if (PHP_OS === 'WINNT') {
				$command = str_replace('\\', '\\\\', str_replace('/', '\\', $command));
				$command = substr($command, 4);
				$WshShell = new COM("WScript.Shell");
				$oExec = $WshShell->Run(PATH_PHP_EXE . ' -f ' . $command, 0, false);
			} else {
				shell_exec($command . ' > /dev/null 2>/dev/null &');
			}
		}
		
		// Execute le script TF-IDF en background
		public function execTfIdf() {
			$input   = $this->CAMPAIGN_CLEAN_TWEETS . $this->bdd->getLastTweetsFile();
			$output  = $this->CAMPAIGN_TF_IDF . basename($this->bdd->getLastTweetsFile());
			$command = 'php "' . TF_IDF_SCRIPT . '" "' . $this->id . '" "' . $input . '" "' . $output . '"';

			if (PHP_OS === 'WINNT') {
				$command = str_replace('\\', '\\\\', str_replace('/', '\\', $command));
				$command = substr($command, 4);
				$WshShell = new COM("WScript.Shell");
				$oExec = $WshShell->Run(PATH_PHP_EXE . ' -f ' . $command, 0, false);
			} else {
				shell_exec($command . ' > /dev/null 2>/dev/null &');
			}
		}
		
		// Execute le script SUGAR en background
		public function execSugar() {
			$input   = $this->CAMPAIGN_CLEAN_TWEETS . $this->bdd->getLastTweetsFile();
			$output  = $this->CAMPAIGN_SUGAR . $this->step . '.html';
			$command = 'php "' . SUGAR_SCRIPT . '" "' . $this->id . '" "' . $input . '" "' . $output . '"';

			if (PHP_OS === 'WINNT') {
				$command = str_replace('\\', '\\\\', str_replace('/', '\\', $command));
				$command = substr($command, 4);
				$WshShell = new COM("WScript.Shell");
				$oExec = $WshShell->Run(PATH_PHP_EXE . ' -f ' . $command, 0, false);
			} else {
				shell_exec($command . ' > /dev/null 2>/dev/null &');
			}
		}
		
		// Recupère un tableau contenant le nom de l'ensemble des fichiers html de tweets
		public function getListTweets() {
			$tweets = array();
			for($i = 0; $i <= $this->step; $i++)
				array_push($tweets, $this->CAMPAIGN_SUGAR . $i . '.html');
			return $tweets;
		}
	
		// **************************** EXPORT **************************** //
		
		public function exportMaps() {
			return $this->export->exportMaps();
		}
		
		public function exportData() {
			return $this->export->exportData();
		}
	
	}

?>