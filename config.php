<?php

	////////////////////////////////////////////////////////////////////
	//																  //
	//            Configuration des diffèrents composants			  //
	//																  //
	////////////////////////////////////////////////////////////////////

	require_once dirname(__FILE__) . '/credentials.php';
	
	// ************************** Constantes pour la maintenance ************************** //
	
	define('DIR_BASE',      	dirname(__FILE__) . '/');
	define('DIR_SYSTEM',    	DIR_BASE   . 'system/');
	define('DIR_INCLUDES',    	DIR_BASE   . 'includes/');
	define('DIR_SCRIPTS',     	DIR_BASE   . 'scripts/');
	define('DIR_LIB',     		DIR_SYSTEM . 'lib/');
	define('DIR_CLEAN_PROCESS', DIR_SYSTEM . 'clean_process/');
	define('DIR_NEWMAN_GIRVAN', DIR_SYSTEM . 'newman_girvan/');
	define('DIR_SUGAR', 		DIR_SYSTEM . 'sugar/');
	define('DIR_TF_IDF', 		DIR_SYSTEM . 'tf_idf/');
	define('DIR_CAMPAIGN',		DIR_SYSTEM . 'campaign/');
	
	define('LOG_FILE', 			DIR_SYSTEM . 'log_error.txt');

	// ********************** Base de données des campagnes ********************** //

	define('BDD_NAME', 'trends-analysis-twitter');
	
	// ********************** Emplacement des algorithmes ********************** //
	
	define('CLEAN_PROCESS_SCRIPT',	DIR_CLEAN_PROCESS . 'script_clean.php');	// v 1.0
	define('NEWMAN_GIRVAN_SCRIPT',	DIR_NEWMAN_GIRVAN . 'script_newman.php');
	define('NEWMAN_GIRVAN_JAR',		DIR_NEWMAN_GIRVAN . 'newman_girvan.jar');	// v 2.2
	define('NEWMAN_GIRVAN_DIC',		DIR_NEWMAN_GIRVAN . 'extDAL.txt');
	define('TF_IDF_SCRIPT',			DIR_TF_IDF 		  . 'script_tf_idf.php');
	define('TF_IDF_JAR',			DIR_TF_IDF        . 'tfidf.jar');			// v 1.1
	define('TF_IDF_DIC_NEG',		DIR_TF_IDF        . 'dictionaries/negative-words.txt');
	define('TF_IDF_DIC_POS',		DIR_TF_IDF        . 'dictionaries/positive-words.txt');
	define('SUGAR_SCRIPT',			DIR_SUGAR         . 'script_sugar.php');	// v 1.0

	// ************************ Si utilisé sous windows ************************ //
	
	define('PATH_PHP_EXE', 'F:\Programmes\Wamp\bin\php\php5.5.12\php-win.exe');
	
	// Chemin d'accès du script de demarrage
	define('PATH_SCRIPT_START_CAMPAIGN', DIR_SCRIPTS . 'start_campaign.php');

?>