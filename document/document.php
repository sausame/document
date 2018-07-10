<?php
function toInputs($deleted=0, $content=NULL, $editor=NULL, $segid=NULL, $docid=NULL) {

	if ($deleted) {
		$box = 'delete-text-box';
	} else {
		$box = 'text-box';
	}

	$line = "<span class='edit-box'>
		<span class='$box' data-is-deleted='$deleted' data-content-id='$segid'>$content</span>   <span class='focus-text'>$editor</span>   <a href='segment-history.php?documentId=$docid&segmentId=$segid'>History</a>
		</span>
		<br/>";

	return $line;
}

?>
<?php
include("auth.php"); //include auth.php file on all secure pages

if (! empty($_POST)) {
	$documentId = $_POST['documentId'];
	$datas = $_POST['datas'];
} elseif (! empty($_GET)) {
	$documentId = $_GET['documentId'];
	$datas = $_GET['datas'];
}

$updated = false;

if (! empty($datas)) {

	$obj = json_decode($datas, true);

	$num = count($obj);

	for ($i = 0; $i < $num; $i ++) {

		$subObj = $obj[$i];

		$name = $subObj[0];
		$segid = $subObj[1];
		$currentSegment = $subObj[2];
		$currentDeleted = $subObj[3];
		$segment = $subObj[4];
		$deleted = $subObj[5];

		if (empty($segid)) {

			if (! empty($segment)) {

				$segment = base64_encode($segment);

				// Insert
				$sql = "INSERT INTO `SegmentTable`(`SegmentId`, `DocumentId`, `SegmentName`, `EditorId`, `Content`, `Deleted`, `UpdateTime`, `CreateTime`) VALUES (NULL, '$documentId', '$name', $userId, '$segment', $deleted, NULL, NULL)";

				$result = mysqli_query($con, $sql) or die(mysqli_error($con));

				if ($result) {
					$updated = true;
				}

			}
		} else {
			if ($currentSegment != $segment || $deleted != $currentDeleted) {

				// Update
				$segment = base64_encode($segment);

				$sql = "UPDATE `SegmentTable`
					SET `EditorId` = '$userId',
						`Content`= '$segment',
						`Deleted`= $deleted,
						`UpdateTime` = CURRENT_TIMESTAMP
					WHERE `SegmentId` = $segid
						AND (`Content` != '$segment'
						OR `Deleted` != $deleted)";

				$result = mysqli_query($con, $sql) or die(mysqli_error($con));

				if ($result && mysqli_affected_rows($con) > 0) {

					$updated = true;

					$currentSegment = base64_encode($currentSegment);

					$sql = "INSERT INTO `SegmentHistoryTable`(`SegmentHistoryId`, `SegmentId`, `EditorId`, `OriginalContent`, `Content`, `CreateTime`) VALUES (NULL, $segid, '$userId', '$currentSegment', '$segment', NULL)";

					$result = mysqli_query($con, $sql) or die(mysqli_error($con));
				}
			}
		}
	}
}

if ($updated) {

	// Update document table.

	$sql = "SELECT `EditorIds` FROM `DocumentTable` WHERE `DocumentId` = $documentId AND (`EditorIds` LIKE '%|$userId|%' OR `OwnerId` = $userId)";
	$result = mysqli_query($con, $sql) or die(mysqli_error($con));

	$row = mysqli_fetch_row($result);

	if (empty($row)) {
	
		$sql = "UPDATE `DocumentTable` SET `EditorIds` = CONCAT(`EditorIds`, '|".$userId."|'), `UpdateTime` = CURRENT_TIMESTAMP WHERE `DocumentId` = $documentId";
		$result = mysqli_query($con, $sql) or die(mysqli_error($con));
	}

	header("Location: ".basename(__FILE__)."?documentId=$documentId");
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>Modification for #<?php echo($documentId); ?> document</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<h2>Modification for #<?php echo($documentId); ?> document</h2>

<?php

$sql = "SELECT `SegmentId`, `SegmentName`, `EditorId`, `UserTable`.`UserName`, `Content`, `Deleted`
	FROM `SegmentTable`
		LEFT JOIN `UserTable`
			ON `UserTable`.`UserId` = `SegmentTable`.`EditorId`
	WHERE `DocumentId` = '$documentId' ORDER BY `SegmentName`";

$result = mysqli_query($con, $sql) or die(mysqli_error($con));

$segmentNames = array();
$segmentContents = array();

while (NULL != ($row = mysqli_fetch_row($result))) {

	$segContent = base64_decode($row[4]);

	$line =	toInputs($row[5], $segContent, $row[3], $row[0], $documentId);

	$found = false;

	for ($i = 0; $i < count($segmentNames); $i ++) {
		if ($segmentNames[$i] == $row[1]) {
			$found = true;
			$segmentContents[$i] .= $line;
			break;
		}
	}

	if (! $found) {
		array_push($segmentNames, $row[1]);
		array_push($segmentContents, $line);
	}
}

$sql = "SELECT `Content` FROM `DocumentTable` WHERE `DocumentId` = $documentId";
$result = mysqli_query($con, $sql) or die(mysqli_error($con));

$row = mysqli_fetch_row($result);
$content = base64_decode($row[0]);

$re = '/{{{[^}]+}}}/m';

preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);

foreach ($matches as $match) {

	$name = $match[0];

	$segmentContent = "<span class='div-box' data-name='$name'>\n";

	for ($i = 0; $i < count($segmentNames); $i ++) {
		if ($segmentNames[$i] == $name) {
			$segmentContent .= $segmentContents[$i];
			break;
		}
	}

	$segmentContent .= "<button class='addBtn'>Add</button>\n</span>\n";

	$content = str_replace($name, $segmentContent, $content);
}

$content = str_replace("\n", "<br/>\n", $content);

?>

<div>
<?php echo($content); ?>
<form name="document" action="" method="post">
<input type="hidden" name="documentId" value="<?php echo($documentId); ?>" />
<input id='datas' type="hidden" name="datas" value='' />
<input id='submit' type="submit" name="submit" value="Submit" />
</form>

<br/>

<hr/>

<a href='view.php?documentId=<?php echo($documentId); ?>'>View</a>
<br/>
<a href="index.php">HOME</a>
<br/>
<a href="logout.php">Logout</a>

</div>

<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="js/segment.js"></script>
</body>
</html>

