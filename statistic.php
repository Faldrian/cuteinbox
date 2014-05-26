<?php

$link = mysql_connect("db.coding4coffee.org", "faldi_cuteinbox", "xzbjme6PfM3xKHuX");
mysql_select_db("faldi_cuteinbox", $link);

mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $link);

// Tag --> Anzahl
$tags = array();

$res = mysql_query("SELECT comment FROM post");
$number_of_posts = mysql_num_rows($res);
while($row = mysql_fetch_row($res)) {
	preg_match_all('/#[\w_\düäöÜÄÖ]+/', $row[0], $matches);
	foreach($matches[0] as $val) {
		if(isset($tags[$val])) {
			$tags[$val]++;
		} else {
			$tags[$val] = 1;
		}
	}
}

arsort($tags);

?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8"/>
<link href="statistic.css" rel="stylesheet"/>
</head>
<body>
<h4>Tags nach Häufigkeit</h4>
<p>Gesamte Posts: <?=$number_of_posts?></p>
<table>
	<thead>
		<tr>
			<td>Tag</td>
			<td>Vorkommen</td>
		</tr>
	</thead>
	<tbody>
<?
foreach($tags as $key => $val) {
	echo "<tr><td>$key</td><td>$val</td></tr>";
}
?>
	</tbody>
</table>
</body>
</html>
