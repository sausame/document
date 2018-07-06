<?php

function toViews($deleted=false, $content=NULL, $editor=NULL) {

	if ($deleted) return NULL;

	return "$content\n";
}

include("auth.php"); //include auth.php file on all secure pages

if (! empty($_POST)) {
	$documentId = $_POST['documentId'];
} elseif (! empty($_GET)) {
	$documentId = $_GET['documentId'];
}

$sql = "SELECT `SegmentName`, `UserTable`.`UserName`, `Content`, `Deleted`
	FROM `SegmentTable`
		LEFT JOIN `UserTable`
			ON `UserTable`.`UserId` = `SegmentTable`.`EditorId`
	WHERE `DocumentId` = '$documentId' ORDER BY `SegmentName`";

$result = mysqli_query($con, $sql) or die(mysql_error());

$segmentNames = array();
$segmentContents = array();

while (NULL != ($row = mysqli_fetch_row($result))) {

	$name = $row[0];
	$segContent = base64_decode($row[2]);

	$line =	toViews($row[3], $segContent, $row[1]);

	$found = false;

	for ($i = 0; $i < count($segmentNames); $i ++) {
		if ($segmentNames[$i] == $name) {
			$found = true;
			$segmentContents[$i] .= $line;
			break;
		}
	}

	if (! $found) {
		array_push($segmentNames, $name);
		array_push($segmentContents, $line);
	}
}

$sql = "SELECT `Content` FROM `DocumentTable` WHERE `DocumentId` = $documentId";
$result = mysqli_query($con, $sql) or die(mysql_error());

$row = mysqli_fetch_row($result);
$content = base64_decode($row[0]);

$re = '/{{{[^}]+}}}/m';

preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);

foreach ($matches as $match) {

	$segmentContent = "";
	$name = $match[0];

	for ($i = 0; $i < count($segmentNames); $i ++) {
		if ($segmentNames[$i] == $name) {
			$segmentContent = $segmentContents[$i];
			break;
		}
	}

	$content = str_replace($name, $segmentContent, $content);
}

echo($content);

?>
