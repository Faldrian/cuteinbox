<?php
// Libs
require('lib/idiorm.php');
require('lib/paris.php');

// Models
require('models.php');

// Config
require('config.php');

// Extraktoren
$extractor = array();
require('extractors.php');

// Normaler Backbone-Sync-Ajax-Handler
function ajax_sync() {
	global $modellist;

	$method = $_REQUEST['backbone_method'];
	$model = $_REQUEST['backbone_model'];

	// Sicherstellen, dass nur Models bearbeitet werden, die wir auch unterstützten
	if (!in_array($model, $modellist)) {
		die('Security Error');
	}

	// Convert the incoming json data into an associative array
	if (isset($_REQUEST['content'])) {
		$vars = json_decode($_REQUEST['content'], true);
	}

	switch ($method) {
		
		// This is the read method.
		// Most of the data will be retrieved via the init method and cached in the client's browser, so this will be rarely used
		case 'read':
			if (isset($_REQUEST['content']) && !empty($_REQUEST['content'])) {
				// 'content' ist direkt eine ID, daher wird nicht $vars benutzt
				$result = Model::factory($model)->find_one($_REQUEST['content']);
				$response = $result->as_array();
			} else {
				// Spezialbehandlung für posts... nur die holen, die nicht gelesen sind.
				if($model == 'Post') {
					$results = Model::factory($model)->where_null('posted')->find_many();
				} else {			
					$results = Model::factory($model)->find_many();
				}
				
				$response = array();
				if(count($results) > 0) {
					if(is_array($results[0])) {
						// Array von Daten-Arrays
						$response = $results;
					} else {
						// Array von Models - in einen Array umpacken.
						foreach ($results as $result) {
							$response[] = $result->as_array();
						}
					}
				} // end: If results > 0
			} // end: find_one or find_many

			echo json_encode($response);
			break;
	
			
		case 'create':
			$new_object = Model::factory($model)->create($vars);
	
			echo json_encode($new_object->as_array());
			break;
	
			
		case 'update':
			$update_object = array();
			
			if($model == 'Post') {
				$update_object = Post::update($vars);
			}
			
			echo json_encode($update_object->as_array());
			break;
	
			
		case 'delete':
			$delete_object = Model::factory($model)->find_one($vars['id']);
			$delete_object->delete();
			
			echo json_encode(array('deleted' => true));
			break;
	}

	exit();
}


function ajax_insertmulti() {
	if(!isset($_REQUEST['urls'])) {
		die("Du musst das richtige Formular benutzen.");
	}
	
	$urls = explode("\n", $_REQUEST['urls']);
	
	// Füge alle neuen URLs ein
	foreach($urls as $url) {
		if(trim($url) == '') {
			continue; // Leere URLs überspringen.
		}
		
		// Checken, ob es diese URL schon im System gibt, dann überspringen
		$duplicateCheck = Model::factory('Post')->where('url', $url)->count();
		if($duplicateCheck > 0) {
			continue;
		}
		
		$entry = Model::factory('Post')->create(array(
			'url' => $url,
			'created' => date('Y-m-d H:i:s')
			));
		$entry->save();
	}
	
	// Gib die Anweisungen an den Client aus, wieviel noch zu refreshen ist
	$num_todo = Model::factory('Post')->where_null('image')->count();
	die("$num_todo");
}


function ajax_insertmulti_progess() {
	global $extractor;
	
	// Hole irgendeinen Eintrag, der noch Bearbeitung bedarf
	$entry = Model::factory('Post')->where_null('image')->find_one();
	
	if($entry === false) {
		die("Nichts mehr zu tun.");
	}
	
	$data = array();
	foreach($extractor as $ex) {
		if($ex->is_able($entry->url)) {
			$source = @file_get_contents($entry->url);
			
			if($source === false) {
				$entry->posted = date('Y-m-d H:i:s');
				$entry->title = "Fehlerhaft!";
				break; // Kannst aufhören, das wird hier nichts mehr.
			}
			
			$data = $ex->extract($source);
		}
	}
	
	// Daten einsortieren
	if(empty($data)) {
		$entry->image = "none";
		$entry->comment = "Keine passender Extraktor gefunden!";
	} else {
		$entry->image = $data['image'];
		$entry->title = $data['title'];
	}
	$entry->save();


	// Gib die Anweisungen an den Client aus, wieviel noch zu refreshen ist
	// Außerdem das geänderte Objekt
	$num_todo = Model::factory('Post')->where_null('image')->count();
	die(json_encode(array(
		'num_todo' => $num_todo,
		'entry' => $entry->as_array()
	)));
}


if(isset($_REQUEST['action'])) {
	switch($_REQUEST['action']) {
		case 'sync': // Synct die normale Datenstruktur
			ajax_sync();
			break;
			
		case 'insertmulti': // Fügt viele URLs ein, die man gerade so einfügen wollte
			ajax_insertmulti();
			break;
			
		case 'insertmulti_progress': // tickt weiter, damit die Webseiten abgearbeitet werden können
			ajax_insertmulti_progess();
			break;
	}
}


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
