
<?php
include("auth.php"); //include auth.php file on all secure pages

if (! empty($_POST)) {
	$documentId = $_POST['documentId'];
} elseif (! empty($_GET)) {
	$documentId = $_GET['documentId'];
} else {
	$documentId = NULL;
}

if (! empty($_POST)) {
	$segmentId = $_POST['segmentId'];
} elseif (! empty($_GET)) {
	$segmentId = $_GET['segmentId'];
} else {
	$segmentId = NULL;
}

if (empty($documentId) || empty($segmentId)) {
	die('No document ID or segment ID.');
}

if (! empty($_POST)) {
	$segmentHistoryId = $_POST['segmentHistoryId'];
} elseif (! empty($_GET)) {
	$segmentHistoryId = $_GET['segmentHistoryId'];
} else {
	$segmentHistoryId = NULL;
}

if (! empty($_POST)) {
	$isOriginal = $_POST['isOriginal'];
} elseif (! empty($_GET)) {
	$isOriginal = $_GET['isOriginal'];
} else {
	$isOriginal = '1';
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>History for <?php echo($segmentId); ?> of #<?php echo($documentId); ?> document</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<h2>Status for <?php echo($segmentId); ?> of #<?php echo($documentId); ?> document</h2>

<?php

if (! empty($segmentHistoryId)) {

	if ('1' == $isOriginal) {
		$newContent = "`SegmentHistoryTable`.`OriginalContent`";
	} else {
		$newContent = "`SegmentHistoryTable`.`Content`";
	}

	$sql = "INSERT INTO `SegmentHistoryTable`
					(`SegmentHistoryId`, `SegmentId`, `EditorId`, `OriginalContent`, `Content`, `CreateTime`)
			SELECT NULL, SegmentHistoryTable.SegmentId, $userId, SegmentTable.Content, $newContent, NULL
			FROM SegmentHistoryTable
				LEFT JOIN SegmentTable
					ON SegmentHistoryTable.SegmentId = SegmentTable.SegmentId
			WHERE SegmentHistoryTable.SegmentHistoryId = $segmentHistoryId AND SegmentHistoryTable.SegmentId = $segmentId";

	$result = mysqli_query($con, $sql) or die(mysqli_error($con));

	$sql = "UPDATE `SegmentTable`, `SegmentHistoryTable`
		SET `SegmentTable`.`EditorId` = '$userId',
			`SegmentTable`.`Content` = $newContent,
			`SegmentTable`.`UpdateTime` = CURRENT_TIMESTAMP
		WHERE `SegmentHistoryTable`.`SegmentHistoryId` = $segmentHistoryId
			AND `SegmentTable`.`SegmentId` = $segmentId
			AND `SegmentTable`.`SegmentId` = `SegmentHistoryTable`.`SegmentId`
			AND `SegmentTable`.`Content` != $newContent";

	$result = mysqli_query($con, $sql) or die(mysqli_error($con));
}

?>
<?php

$sql = "SELECT SegmentTable.SegmentName, SegmentTable.Content, UserTable.UserName, SegmentTable.UpdateTime
	FROM SegmentTable
		LEFT JOIN DocumentTable
			ON DocumentTable.DocumentId = SegmentTable.DocumentId
		LEFT JOIN UserTable
			ON UserTable.UserId = SegmentTable.EditorId
	WHERE SegmentTable.SegmentId = $segmentId
		AND SegmentTable.DocumentId = $documentId
		AND (DocumentTable.OwnerId = $userId OR DocumentTable.EditorIds LIKE '%|$userId|%')";

$result = mysqli_query($con, $sql) or die(mysqli_error($con));

$row = mysqli_fetch_row($result);

$segmentName = $row[0];
$segmentContent = base64_decode($row[1]);
$username = $row[2];
$updatetime = $row[3];

?>
<table class="tb">
<thead>
<tr>
<th>Segment Name</th>
<th>Content</th>
<th>User Name</th>
<th>Update Time</th>
</tr>
</thead>
<tbody>
<tr>
<td><?php echo($segmentName); ?></td>
<td><?php echo($segmentContent); ?></td>
<td class="comment"><?php echo($username); ?></td>
<td><?php echo($updatetime); ?></td>
</tr>
</tbody>
</table>



<hr/>
<h2>History</h2>
<table class="tb">
<thead>
<tr>
<th>ID</th>
<th>Original Content</th>
<th>New Content</th>
<th>Editor</th>
<th>Create Time</th>
<th>Restore to Original Content</th>
<th>Restore to New Content</th>
</tr>

</thead>

<?php

$sql = "SELECT SegmentHistoryTable.SegmentHistoryId, UserTable.UserName,
	SegmentHistoryTable.OriginalContent, SegmentHistoryTable.Content,
		SegmentHistoryTable.CreateTime
	FROM SegmentHistoryTable
		LEFT JOIN UserTable
			ON SegmentHistoryTable.EditorId = UserTable.UserId
	WHERE SegmentHistoryTable.SegmentId = $segmentId";

$result = mysqli_query($con, $sql) or die(mysqli_error($con));

while (NULL != ($row = mysqli_fetch_row($result))) {

	$historyId = $row[0];
	$username = $row[1];
	$originalContent = base64_decode($row[2]);
	$content = base64_decode($row[3]);
	$createtime = $row[4];


	if ($originalContent != $segmentContent) {
		$restoreToOriginal = "<a href='segment-history.php?documentId=$documentId&segmentId=$segmentId&&segmentHistoryId=$historyId&isOriginal=1'>Restore to Original Content</a>";
	} else {
		$restoreToOriginal = NULL;
	}

	if ($content != $segmentContent) {
		$restoreToNew = "<a href='segment-history.php?documentId=$documentId&segmentId=$segmentId&&segmentHistoryId=$historyId&isOriginal=0'>Restore to New Content</a>";
	} else {
		$restoreToNew = NULL;
	}
?>
<tbody>
<tr>
<th><?php echo($historyId); ?></th>
<td><?php echo($originalContent); ?></td>
<td><?php echo($content); ?></td>
<td class="comment"><?php echo($username); ?></td>
<td><?php echo($createtime); ?></td>
<td class="linker"><?php echo($restoreToOriginal); ?></td>
<td class="linker"><?php echo($restoreToNew); ?></td>
</tr>

<?php
}
?>
</tbody>
</table>

<hr/>

<a href='view.php?documentId=<?php echo($documentId); ?>'>View</a>
<br/>
<a href="index.php">HOME</a>
<br/>
<a href="logout.php">Logout</a>

</body>
</html>

