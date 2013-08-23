<?php
// Definiert Extraktoren

interface iExtractor {
	function is_able($url);
	function extract($source);
}


class ExFlickr implements iExtractor {
	function is_able($url) {
		return strpos($url, 'flickr.com/') !== false;
	}
	
	function extract($source) {
	
	}
}
$extractor[] = new ExFlickr();


class ExDeviantArt implements iExtractor {
	function is_able($url) {
		return strpos($url, 'deviantart.com/') !== false;
	}
	
	function extract($source) {
		$data = array();
		
		// Titel extrahieren
		preg_match('/<title>(.*?)<\/title>/', $source, $matches);
		$data['title'] = $matches[1];
		
		// Bild extrahieren
		preg_match('/<meta name="og:image" content="(.*?)">/', $source, $matches);
		$data['image'] = $matches[1];
		
		return $data;
	}
}
$extractor[] = new ExDeviantArt();

class Ex500px implements iExtractor {
	function is_able($url) {
		return strpos($url, '500px.com/') !== false;
	}
	
	function extract($source) {
	
	}
}
$extractor[] = new Ex500px();


