<?php

	////////////////////////////////////////////////////////////////////
	//																  //
	// Classe gérant la connection à la base de données des campagnes //
	//																  //
	//																  //
	//																  //
	//																  //
	////////////////////////////////////////////////////////////////////

	class CampaignBdd {

		private $campaign;
		private $bdd;
		
		// Nom des tables
		private $TABLE_CAMPAIGN_TWEETS;
		private $TABLE_CAMPAIGN_GEO;
		private $TABLE_CAMPAIGN_CLEAN;
		private $TABLE_CAMPAIGN_NG;
		private $TABLE_CAMPAIGN_TF_IDF;
		private $TABLE_CAMPAIGN_SUGAR;

		// Constructeur qui prend une campagne pas encore présente dans la bdd (absence d'id)
		public function __construct(&$campaign) {
	
			$this->campaign = $campaign;

			// Connexion à la base de données
			$this->bdd = self::connect();
			$this->installation();
		}

		// Se connecte à la base de données
		private static function connect() {
			return new PDO('mysql:host=' . BDD_HOST . ';dbname=' . BDD_NAME . ';charset=utf8', BDD_LOGIN, BDD_PASSWORD);
		}
		
		// Test l'existence d'une table
		private function exist_table($name) {		
			$query = $this->bdd->query('SHOW TABLES LIKE "' . $name . '";');
			return ($query->rowCount() > 0);
		}
		
		// Initialise la table de liste des campagnes (lors de l'installation)
		public static function installation() {
			// On créer la base de données si nécessaire
			$bdd = new PDO('mysql:host=' . BDD_HOST, BDD_LOGIN, BDD_PASSWORD);
			$bdd->query('CREATE DATABASE IF NOT EXISTS ' . BDD_NAME . ';');
			$bdd = self::connect();
			$bdd->query('CREATE TABLE IF NOT EXISTS campaign_list (' .
							'id       INT(11)                                            AUTO_INCREMENT     COMMENT "Identifiant unique",' .
							'name     VARCHAR(50)                                        CHARACTER SET utf8 COMMENT "Nom de la campagne",' .
							'state    ENUM("SCHEDULED", "STARTED", "ENDED", "CANCELLED") CHARACTER SET utf8 COMMENT "Etat de la campagne",' .
							'begin    TIMESTAMP                                                             COMMENT "Début de la campagne",' . 
							'step     INT(11)                                                               COMMENT "Intervalle courant de la campagne",' . 
							'length   INT(11)                                                               COMMENT "Durée de la campagne (en minutes)",' . 
							'keywords VARCHAR(500)                                       CHARACTER SET utf8 COMMENT "Mots-clés de la campagne sérialisés",' . 
							'PRIMARY KEY (id));');	
		}
		
		// Ajoute une entrée dans la table listant les campagnes et créer les nouvelles tables reservée à cette campagne
		// Met à jour l'id de la campagne pour le faire correspondre à celui de la base de données
		public function initialize_campaign() {
	
			// On installe la table de gestion des campagnes si necessaire
			if (!$this->exist_table('campaign_list'))
				self::installation();
	
			// On enregistre la campagne dans la bdd
			$query = $this->bdd->prepare('INSERT INTO campaign_list (name, state, begin, step, length, keywords) VALUES (?, ?, ?, ?, ?, ?);');
			$query->execute(array($this->campaign->name,
								  $this->campaign->state,
								  date('Y-m-d H:i:s', $this->campaign->begin),
								  $this->campaign->step,
								  $this->campaign->length,
								  serialize($this->campaign->keywords)));
								  
			// On recupère l'id unique de la campagne
			$this->campaign->id = $this->bdd->lastInsertId();
			$this->TABLE_CAMPAIGN_TWEETS = 'campaign_tweets_' . $this->campaign->id;
			$this->TABLE_CAMPAIGN_GEO    = 'campaign_geo_'    . $this->campaign->id;
			$this->TABLE_CAMPAIGN_CLEAN  = 'campaign_clean_'  . $this->campaign->id;
			$this->TABLE_CAMPAIGN_NG	 = 'campaign_ng_'     . $this->campaign->id;
			$this->TABLE_CAMPAIGN_TF_IDF = 'campaign_tfidf_'  . $this->campaign->id;
			$this->TABLE_CAMPAIGN_SUGAR  = 'campaign_sugar_'  . $this->campaign->id;
			
			// On créer les tables qui vont accueillir les données de la campagne
			$this->bdd->query('CREATE TABLE IF NOT EXISTS ' . $this->TABLE_CAMPAIGN_TWEETS . ' (' .
								'id    INT(11)     AUTO_INCREMENT             COMMENT "Identifiant unique",' .
								'date  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP  COMMENT "Date de création du fichier de tweets",' .
								'file  VARCHAR(50) CHARACTER SET utf8         COMMENT "Nom du fichier de tweets",' .
								'size  INT(20)                                COMMENT "Poids en octets du fichier de tweets",' .
								'count INT(5)                                 COMMENT "Nombre de tweets dans le fichier",' . 
								'PRIMARY KEY (id));');
			
			$this->bdd->query('CREATE TABLE IF NOT EXISTS ' . $this->TABLE_CAMPAIGN_GEO . ' (' .
								'id        INT(15)      AUTO_INCREMENT     COMMENT "Identifiant unique",' .
								'name      VARCHAR(50)  CHARACTER SET utf8 COMMENT "Nom du twittos",' .
								'message   VARCHAR(450) CHARACTER SET utf8 COMMENT "Message",' .
								'longitude FLOAT(15)                       COMMENT "Longitude",' .
								'latitude  FLOAT(15)                       COMMENT "Latitude",' .
								'PRIMARY KEY (id));');

			$this->bdd->query('CREATE TABLE IF NOT EXISTS ' . $this->TABLE_CAMPAIGN_CLEAN . ' (' .
								'date   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP COMMENT "Date du nettoyage",' .
								'step   INT(5)    							  COMMENT "Instant",' .
								'time   FLOAT(15)							  COMMENT "Temps d\'exécution",' .
								'tweets INT(5)    							  COMMENT "Nombre de tweets traités",' .
								'PRIMARY KEY (step));');
								
			$this->bdd->query('CREATE TABLE IF NOT EXISTS ' . $this->TABLE_CAMPAIGN_NG . ' (' .
								'date   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP COMMENT "Date de l\'analyse",' .
								'step   INT(5)    							  COMMENT "Instant analysé",' .
								'value  FLOAT(15) 							  COMMENT "Polarité",' .
								'time   FLOAT(15)							  COMMENT "Temps d\'exécution",' .
								'tweets INT(5)    							  COMMENT "Nombre de tweets traités",' .
								'words  VARCHAR(2500) CHARACTER SET utf8 	  COMMENT "Mots prédominants",' .
								'PRIMARY KEY (step));');
								
			$this->bdd->query('CREATE TABLE IF NOT EXISTS ' . $this->TABLE_CAMPAIGN_TF_IDF . ' (' .
								'date   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP COMMENT "Date de l\'analyse",' .
								'step   INT(5)    							  COMMENT "Instant analysé",' .
								'value  FLOAT(15) 							  COMMENT "Polarité",' .
								'time   FLOAT(15)							  COMMENT "Temps d\'exécution",' .
								'tweets INT(5)    							  COMMENT "Nombre de tweets traités",' .
								'words  VARCHAR(2500) CHARACTER SET utf8 	  COMMENT "Mots prédominants",' .
								'PRIMARY KEY (step));');

			$this->bdd->query('CREATE TABLE IF NOT EXISTS ' . $this->TABLE_CAMPAIGN_SUGAR . ' (' .
								'date   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP COMMENT "Date de l\'analyse",' .
								'step   INT(5)    							  COMMENT "Instant analysé",' .
								'id     VARCHAR(25)   CHARACTER SET utf8	  COMMENT "Id du tweet représentatif",' .
								'time   FLOAT(15)							  COMMENT "Temps d\'exécution",' .
								'tweets INT(5)    							  COMMENT "Nombre de tweets traités",' .
								'PRIMARY KEY (step));');
								
		}
		
		// Récupères les informations concernant une campagne dans la base de données à partir de son id
		public function getCampaignFromBdd() {
			$query = $this->bdd->prepare('SELECT name, state, UNIX_TIMESTAMP(begin), step, length, keywords FROM campaign_list WHERE id = ?;');
			$query->execute(array($this->campaign->id));
			if($query->rowCount() == 0)
				throw new Exception('La campagne portant l\'id "' . $this->campaign->id . '" n\'existe pas.');
			$attr = $query->fetch();
			$this->campaign->name     	= $attr['name'];
			$this->campaign->state    	= $attr['state'];
			$this->campaign->begin    	= $attr['UNIX_TIMESTAMP(begin)'];
			$this->campaign->step     	= $attr['step'];
			$this->campaign->length  	= $attr['length'];
			$this->campaign->keywords	= unserialize($attr['keywords']);

			$this->TABLE_CAMPAIGN_TWEETS = 'campaign_tweets_' . $this->campaign->id;
			$this->TABLE_CAMPAIGN_GEO    = 'campaign_geo_'    . $this->campaign->id;
			$this->TABLE_CAMPAIGN_CLEAN  = 'campaign_clean_'  . $this->campaign->id;
			$this->TABLE_CAMPAIGN_NG     = 'campaign_ng_'     . $this->campaign->id;
			$this->TABLE_CAMPAIGN_TF_IDF = 'campaign_tfidf_'  . $this->campaign->id;
			$this->TABLE_CAMPAIGN_SUGAR  = 'campaign_sugar_'  . $this->campaign->id;
		}
		
		// Supprime toutes les traces d'une campagne de la base de données
		public function delete() {
			$query = $this->bdd->prepare('DELETE FROM campaign_list WHERE id = ?;');
			$query->execute(array($this->campaign->id));
			$this->bdd->query('DROP TABLE ' . $this->TABLE_CAMPAIGN_TWEETS . ';');
			$this->bdd->query('DROP TABLE ' . $this->TABLE_CAMPAIGN_GEO    . ';');
			$this->bdd->query('DROP TABLE ' . $this->TABLE_CAMPAIGN_CLEAN  . ';');
			$this->bdd->query('DROP TABLE ' . $this->TABLE_CAMPAIGN_NG     . ';');
			$this->bdd->query('DROP TABLE ' . $this->TABLE_CAMPAIGN_TF_IDF . ';');
			$this->bdd->query('DROP TABLE ' . $this->TABLE_CAMPAIGN_SUGAR  . ';');
		}
		
		// Retourne le nombre total de tweets récoltés
		public function getTotalTweets() {
			$query = $this->bdd->query('SELECT SUM(count) FROM ' . $this->TABLE_CAMPAIGN_TWEETS . ';');
			return $query->fetch()['SUM(count)'];
		}
		
		// Retourne le poids total en octets des fichiers de tweets
		public function getTotalTweetsSize() {
			$query = $this->bdd->query('SELECT SUM(size) FROM ' . $this->TABLE_CAMPAIGN_TWEETS . ';');
			return $query->fetch()['SUM(size)'];	
		}
		
		// Retourne le tableau contenant le nombre de tweets récoltés à chaque intervalle
		public function getTweetsPerInterval() {
			$query = $this->bdd->query('SELECT UNIX_TIMESTAMP(date), count FROM ' . $this->TABLE_CAMPAIGN_TWEETS . ';');
			return $query->fetchAll();	
		}
		
		// Retourne un tableau contenant l'ensemble des coordonées gps
		public function getGeoLocs() {
			$query = $this->bdd->query('SELECT name, message, longitude, latitude FROM ' . $this->TABLE_CAMPAIGN_GEO . ';');
			return $query->fetchAll();	
		}
		
		// Retourne la colonne de la table campaign_list
		private function getColumnCampaignList($elem) {
			$query = $this->bdd->prepare('SELECT ' . $elem . ' FROM campaign_list WHERE id = ?;');
			$query->execute(array($this->campaign->id));
			return $query->fetch()[$elem];
		}
		
		// Retourne le tableau de mots-clés
		public function getKeywords() {
			return unserialize($this->getColumnCampaignList('keywords'));
		}
		
		// Retourne le nom du fichier de tweets le plus récent
		public function getLastTweetsFile() {
			$query = $this->bdd->query('SELECT file FROM ' . $this->TABLE_CAMPAIGN_TWEETS . ' ORDER BY date DESC LIMIT 1;');
			return $query->fetch()['file'];
		}
		
		// Retourne l'état de la campagne
		public function getState() {
			return $this->getColumnCampaignList('state');
		}
		
		// Retourne le timestamp du début de la campagne
		public function getBegin() {
			return $this->getColumnCampaignList('UNIX_TIMESTAMP(begin)');
		}
		
		// Retourne le step de la campagne
		public function getStep() {
			return $this->getColumnCampaignList('step');
		}
		
		// Retourne la durée de la campagne en minutes
		public function getLength() {
			return $this->getColumnCampaignList('length');
		}
		
		// Retourne le nom d'une campagne
		public function getName() {
			return $this->getColumnCampaignList('name');
		}
		
		// Retourne la liste des intervalles de temps de l'ensemble des campagnes programmées ou en cours (exceptée la campagne courante)
		public function getIntervalCampaign() {
			$query = $this->bdd->prepare('SELECT name, UNIX_TIMESTAMP(begin), length FROM campaign_list WHERE state <> "ENDED" and id <> ?;');
			$query->execute(array($this->campaign->id));
			if($query->rowCount() > 0)
				return $query->fetchAll();
			else
				return array();
		}
		
		// **************************** SETTERS **************************** //
		
		// Ajoute une entrée à la table de tweets
		public function add_file_tweets($file, $size, $count) {		
			$query = $this->bdd->prepare('INSERT INTO ' . $this->TABLE_CAMPAIGN_TWEETS . ' (file, size, count) VALUES (?, ?, ?);');
			$query->execute(array($file, $size, $count));		
		}
		
		// Ajoute une coordonnée à la table des geolocalisations
		public function add_geoloc($name, $message, $longitude, $latitude) {
			// On insère l'utilisateur où on update son nombre de tweets s'il existait déjà
			$query = $this->bdd->prepare('INSERT INTO ' . $this->TABLE_CAMPAIGN_GEO . ' (name, message, longitude, latitude) VALUES(?, ?, ?, ?)');	
			$query->execute(array($name, $message, $longitude, $latitude));	
		}
		
		// Change l'état d'une campagne
		public function setState($state) {
			$query = $this->bdd->prepare('UPDATE campaign_list SET state = ?, begin = begin WHERE id = ?;'); // Permet de s'assure qu'il n'y ait pas de maj du timestamp
			$query->execute(array($state, $this->campaign->id));
		}
		
		// Change l'étape d'une campagne
		public function setStep($step) {
			$query = $this->bdd->prepare('UPDATE campaign_list SET step = ?, begin = begin WHERE id = ?;'); // Permet de s'assure qu'il n'y ait pas de maj du timestamp
			$query->execute(array($step, $this->campaign->id));
		}
		
		// **************************** Methodes communes **************************** //
		
		// Retourne si elle(s) existe(nt), le nom et l'id de la campagne en cours
		public static function getStartedCampaign() {
			$bdd = self::connect();
			$query = $bdd->query("SELECT id, name FROM campaign_list WHERE state = 'STARTED';");		
			return $query->fetch();
		}
		
		// Retourne si elle(s) existe(nt), le nom et l'id des campagnes programmées
		public static function getScheduledCampaign() {
			$bdd = self::connect();
			$query = $bdd->query("SELECT id, name FROM campaign_list WHERE state = 'SCHEDULED';");		
			return $query->fetchAll();
		}
		
		// Retourne si elle(s) existe(nt), le nom et l'id des campagnes terminées
		public static function getEndedOrCancelledCampaign() {
			$bdd = self::connect();
			$query = $bdd->query("SELECT id, name FROM campaign_list WHERE state = 'ENDED' or state = 'CANCELLED';");		
			return $query->fetchAll();
		}
		
		// **************************** Algorithme de nettoyage **************************** //
		
		// Insère une entrée dans la table
		public function insertClean($time, $tweets) {
			$step  = $this->campaign->step;
			$query = $this->bdd->prepare('INSERT INTO ' . $this->TABLE_CAMPAIGN_CLEAN . ' (step, time, tweets) VALUES(?, ?, ?);');
			$query->execute(array($step, $time, $tweets));		
		}
		
		// **************************** Newmam Girvan **************************** //
		
		// Insère une entrée dans la table
		public function insertNewmanGirvan($value, $time, $tweets, $words) {
			$step 		 = $this->campaign->step;
			$array_words = serialize($words);
			$query = $this->bdd->prepare('INSERT INTO ' . $this->TABLE_CAMPAIGN_NG . ' (step, value, time, tweets, words) VALUES(?, ?, ?, ?, ?);');
			$query->execute(array($step, $value, $time, $tweets, $array_words));		
		}
		
		// Recupère les valeurs pour l'affichage du graphique de polarité
		public function getDataNewmanGirvan() {
			$query = $this->bdd->query('SELECT UNIX_TIMESTAMP(date), value FROM ' . $this->TABLE_CAMPAIGN_NG . ';');
			return $query->fetchAll();
		}
		
		// Recupère les valeurs pour l'affichage du graphique de performance
		public function getPerformanceNewmanGirvan() {
			$query = $this->bdd->query('SELECT UNIX_TIMESTAMP(date), time, tweets FROM ' . $this->TABLE_CAMPAIGN_NG . ';');
			return $query->fetchAll();
		}
		
		// Recupère les mots correspondant à l'instant donné
		public function getWordsNewmanGirvan() {
			$query = $this->bdd->prepare('SELECT words FROM ' . $this->TABLE_CAMPAIGN_NG . ' WHERE step = ?;');
			$query->execute(array($this->campaign->step));
			return unserialize($query->fetch()['words']);
		}
		
		// ******************************** TF-IDF ******************************** //
		
		// Insère une entrée dans la table
		public function insertTfIdf($value, $time, $tweets, $words) {
			$step 		 = $this->campaign->step;
			$array_words = serialize($words);
			$query = $this->bdd->prepare('INSERT INTO ' . $this->TABLE_CAMPAIGN_TF_IDF . ' (step, value, time, tweets, words) VALUES(?, ?, ?, ?, ?);');
			$query->execute(array($step, $value, $time, $tweets, $array_words));		
		}
		
		// Recupère les valeurs pour l'affichage du graphique de polarité
		public function getDataTfIdf() {
			$query = $this->bdd->query('SELECT UNIX_TIMESTAMP(date), value FROM ' . $this->TABLE_CAMPAIGN_TF_IDF . ';');
			return $query->fetchAll();
		}
		
		// Recupère les valeurs pour l'affichage du graphique de performance
		public function getPerformanceTfIdf() {
			$query = $this->bdd->query('SELECT UNIX_TIMESTAMP(date), time, tweets FROM ' . $this->TABLE_CAMPAIGN_TF_IDF . ';');
			return $query->fetchAll();
		}
		
		// Recupère les mots correspondant à l'instant donné
		public function getWordsTfIdf() {
			$query = $this->bdd->prepare('SELECT words FROM ' . $this->TABLE_CAMPAIGN_TF_IDF . ' WHERE step = ?;');
			$query->execute(array($this->campaign->step));
			return unserialize($query->fetch()['words']);
		}
		
		// ******************************** SUGAR ******************************** //
		
		// Insère une entrée dans la table
		public function insertSugar($id, $time, $tweets) {
			$step  = $this->campaign->step;
			$query = $this->bdd->prepare('INSERT INTO ' . $this->TABLE_CAMPAIGN_SUGAR . ' (step, id, time, tweets) VALUES(?, ?, ?, ?);');
			$query->execute(array($step, $id, $time, $tweets));		
		}
		
	}

?>