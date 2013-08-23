#!/usr/bin/php
<?php

if(count($argv) != 3) {
	die("Usage: ./firefox_bookmarks.php [JSON-File] [Foldername]
	[JSON-File] is an exported Bookmark-File from Firefox
	[Foldername] is the name of the folder that contains the bookmarks. Case-Sensitive!
");
}

$bookmarkfile = $argv[1];
$foldername = $argv[2];

$contents = file_get_contents($bookmarkfile);
$structure = json_decode($contents);

/**
 * Rekursively search for the $foldername in $structure.
 * Can't use array_walk_recursive, because there are objects involved... I believe.
 */
function searchFolder($structure, $foldername) {
	if(property_exists($structure, 'children')) {
		// Gefunden, abbrechen
		if($structure->title == $foldername) {
			return $structure;
		}
		
		// Kinder durchsuchen!
		foreach($structure->children as $child) {
			$result = searchFolder($child, $foldername);
			if($result !== false) { // Hier wurde was gefunden! Zurückgeben
				return $result;
			}
		}
	}
	return false; // In diesem Teilbaum wurde nichts gefunden
}

$bookmarkfolder = searchFolder($structure, $foldername);

// Extract URLS from the Bookmarks
foreach($bookmarkfolder->children as $item) {
	echo $item->uri . "\n";
}

?>

