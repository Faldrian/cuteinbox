<?php

// Liste aller Models, damit man die Bearbeitung irgendwelcher Tabellen ausschließen kann.
$modellist = array('Post');


class Post extends Model {
	static function update($vars) {
		$entry = Model::factory('Post')->find_one($vars['id']);
		
		$entry->body = $vars['body'];
		$entry->comment = $vars['comment'];
		$entry->save();
		
		return $entry;
	}
}

