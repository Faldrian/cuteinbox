<?php

error_reporting  (E_ALL);
ini_set ('display_errors', true);

// Extraktoren
$extractor = array();
require('extractors.php');

function debug($key, $val) {
	echo "<b>$key</b>: $val<br />\n";
}

/**
 * Hier wird getestet, wie die jeweiligen Extraktoren auf die Scripts reagieren.
 */

$url = $_GET['url'];

debug('URL', $url);

$data = array();
foreach($extractor as $ex) {
	if($ex->is_able($url)) {
		// Ausgeben, wer sich zuständig fühlt.
		debug('Extraktor', get_class($ex));
	
		$source = file_get_contents($url);
		
		if($source === false) {
			debug('Status', 'Konnte nicht gelesen werden.');
		}
		
		debug('source-length', strlen($source));
		
		$data = $ex->extract($source);
		
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
}
