<?php

function getFirstLine($content) {

	$offset = 0;

	while (true) {

		$pos = strpos($content, "\n", $offset);

		if ($pos === false) {
			return $content;
		}

		$len = $pos - $offset;

		if (0 === $pos) {
			$offset += 1;
			continue;
		}

		$line = trim(substr($content, $offset, $len));

		if (empty($line)) {
			$offset = $pos + 1;
			continue;
		}

		return $line;
	}

	return false;
}

include("auth.php"); //include auth.php file on all secure pages

if (! empty($_POST)) {
	$documentId = $_POST['documentId'];
} elseif (! empty($_GET)) {
	$documentId = $_GET['documentId'];
} else {
	$documentId = NULL;
}

if (! empty($_POST)) {
	$action = $_POST['action'];
} elseif (! empty($_GET)) {
	$action = $_GET['action'];
} else {
	$action = NULL;
}

if (! empty($documentId)) {

	if ('trash' == $action) {

		$sql = "UPDATE `DocumentTable` SET `Trashed` = 1 WHERE `OwnerId` = '$userId' AND `DocumentId` = $documentId";
		$result = mysqli_query($con, $sql) or die(mysql_error());
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>Home of <?php echo $_COOKIE['ID_your_site']; ?></title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div>
<p>Welcome <?php echo $_COOKIE['ID_your_site']; ?>!</p>

<h2>Your owned documents:</h2>

<table class="tb">
<thead>
<tr>
<th>ID</th>
<th>Title</th>
<th>View</th>
<th>Edit</th>
<th>Modify Template</th>
<th>Trash</th>
</tr>
</thead>
<tbody>

<?php

$sql = "SELECT `DocumentId`, `Content` FROM `DocumentTable` WHERE `Trashed` = 0 AND `OwnerId` = '$userId'";
$result = mysqli_query($con, $sql) or die(mysql_error());

while (NULL != ($row = mysqli_fetch_row($result))) {

	$documentId = $row[0];
	$content = getFirstLine(base64_decode($row[1]));

	echo("<tr>\n");

	echo("<th>$documentId</th>");
	echo("<td><pre>$content</pre></td>");
	echo("<td class='linker'><a href='view.php?documentId=$documentId'>View</a></td>");
	echo("<td class='linker'><a href='document.php?documentId=$documentId'>Edit</a></td>");
	echo("<td class='linker'><a href='template.php?documentId=$documentId'>Modify Template</a></td>");
	echo("<td class='linker' class='tab'><a href='index.php?documentId=$documentId&action=trash'>Trash</a></td>");

	echo("</tr>\n");
}

?>
<tbody>
</table>
<br/>
<h2>Your participated documents:</h2>

<table class="tb">
<thead>
<tr>
<th>ID</th>
<th>Owner</th>
<th>Title</th>
<th>View</th>
<th>Edit</th>
</tr>
</thead>
<tbody>

<?php

$sql = "SELECT `DocumentId`, `UserName`, `Content`
	FROM `DocumentTable`
		LEFT JOIN `UserTable`
			ON `UserTable`.`UserId` = `DocumentTable`.`OwnerId`
	WHERE `Trashed` = 0 AND `EditorIds` LIKE '%|$userId|%'";

$result = mysqli_query($con, $sql) or die(mysql_error());

while (NULL != ($row = mysqli_fetch_row($result))) {

	$documentId = $row[0];
	$content = getFirstLine(base64_decode($row[2]));

	echo("<tr>\n");

	echo("<th>$documentId</th>");
	echo("<td>".$row[1]."</td>");
	echo("<td><pre>$content</pre></td>");
	echo("<td class='linker'><a href='view.php?documentId=$documentId'>View</a></td>");
	echo("<td class='linker'><a href='document.php?documentId=$documentId'>Edit</a></td>");

	echo("</tr>\n");
}

?>
<tbody>
</table>
<br/>

<a href="template.php">New document</a>
<br/>
<a href="trash.php">Trash</a>
<br/>
<a href="logout.php">Logout</a>

</div>
</body>
</html>

