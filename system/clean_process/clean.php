<?php

function clean($input, $output) {

	// Timestamp en ms de départ
	$time_start = microtime(true);

	// Ouverture des fichiers
	$handle_input = fopen($input, 'r');
	$handle_output = fopen($output, 'w');
	
	// Si les fichiers sont bien ouverts
	if ($handle_input && $handle_output) {
		
		// On parcourt toutes ls lignes jusqu'à la fin du fichier
		while (!feof($handle_input)) {

			// On recupère la ligne courante
			$buffer = fgets($handle_input);
			
			// On s'assure que la ligne est valide (= non vide)
			if(strlen($buffer) > 5) {
			
				// Traitement de la ligne courante
				$tweet = json_decode($buffer, true);
				
				// On enlève les caractères inutiles
				$tweet['text'] = strtr($tweet['text'], '²&~#"\'{([|`\@°)]+=}¨^£$¤%µ*><?,.;/:§!*', '                                       ');
				
				// On supprime certains caractères/mots
				$tweet['text'] = str_replace('RT', '', $tweet['text']);
				$tweet['text'] = str_replace("\n", '', $tweet['text']);
				
				// Supprimer les lettres identiques qui se suivent ($nb = nombre de lettres à garder)
				$nb = 2;
				$tweet['text'] = preg_replace('#(\w)\1{'.$nb.',}#', str_repeat('\1', $nb), $tweet['text']);
				
				// On enlève les blancs de début et de fin
				$tweet['text'] = trim($tweet['text']);
				
				$encode = json_encode($tweet);
				if(strlen($encode) > 5)
					fwrite($handle_output, $encode . "\n");
			
			}
		}
		// On ferme les fichiers
		fclose($handle_input);		
		fclose($handle_output);
		
		return microtime(true) - $time_start;
		
	}

}	
	
?>