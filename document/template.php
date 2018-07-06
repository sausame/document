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
	$content = $_POST['content'];
} elseif (! empty($_GET)) {
	$content = $_GET['content'];
} else {
	$content = NULL;
}

if (NULL != $content) {

	$content = base64_encode($content);

	if (NULL != $documentId) {
		$sql = "UPDATE `DocumentTable` SET `Content`='$content', `UpdateTime` = CURRENT_TIMESTAMP WHERE `DocumentId` = $documentId";
		$result = mysqli_query($con, $sql) or die(mysql_error());

		$message = "#$documentId document is updated.";

	} else {
		$sql = "INSERT INTO `DocumentTable`(`DocumentId`, `OwnerId`, `EditorIds`, `Content`, `Trashed`, `UpdateTime`, `CreateTime`) VALUES (NULL, $userId, '', '$content', 0, NULL, NULL)";
		$result = mysqli_query($con, $sql) or die(mysql_error());

		if ($result) {
			$sql = "SELECT `DocumentId` FROM `DocumentTable` WHERE `OwnerId` = $userId ORDER BY `DocumentId` DESC LIMIT 1";
			$result = mysqli_query($con, $sql) or die(mysql_error());

			$row = mysqli_fetch_row($result);
			$documentId = $row[0];

			$message = "#$documentId document is created.";
		}
	}

} else if (NULL != $documentId) {
	$sql = "SELECT `Content` FROM `DocumentTable` WHERE `DocumentId` = $documentId";
	$result = mysqli_query($con, $sql) or die(mysql_error());

	$row = mysqli_fetch_row($result);
	$content = $row[0];
}

if (! empty($content)) {
	$content = base64_decode($content);
} else {
	$content = file_get_contents('../template/sample.md');
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>Template of #<?php echo($documentId); ?> document</title>
<link rel="stylesheet" href="css/style.css" />
<style>
#content
{
  width:100%;
  height:100%;
}
</style>
</head>
<body>
<p>Template of #<?php echo($documentId); ?> Document</p>
<p><?php echo($message); ?></p>
<div>
<form name="document" action="" method="post">
<textarea cols="50" id="content" rows="30" name="content" required><?php echo($content); ?></textarea>
<input type="hidden" name="documentId" value="<?php echo($documentId); ?>" />
<input type="submit" name="submit" value="Update" />
</form>

<br/>

<a href="index.php">HOME</a>
<br/>
<a href="logout.php">Logout</a>

</div>
</body>
</html>

