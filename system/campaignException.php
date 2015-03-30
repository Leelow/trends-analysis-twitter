<?php
	
	// Classe d'exception personnalisée

	class CampaignException extends Exception {
	 
		public $code;
		public $comment;
	 
		public function __construct($msg = NULL, $code = 0, $comment = NULL) {

			parent::__construct($msg, $code);
			$this->comment = $comment;
			$this->addLog();
			
		} 
		 
		private function addLog() {

			// Si le fichier de logs n'existe pas, on le créer
			if(!file_exists(LOG_FILE))
				file_put_contents(LOG_FILE, '');
		
			// Ajoute un log dans le fichier d'erreurs 
			file_put_contents (LOG_FILE, '[' . date('d-M-Y H:i:s', time()) . '] : ' .  $this->getMessage() . "\n", FILE_APPEND);
			
		} 
		 
		public function showError() { 

			return '<div style="color:red">'.$this->getMessage().'</div>'; 
			
		} 
	}