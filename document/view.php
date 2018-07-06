<?php
include("auth.php"); //include auth.php file on all secure pages

if (! empty($_POST)) {
	$documentId = $_POST['documentId'];
} elseif (! empty($_GET)) {
	$documentId = $_GET['documentId'];
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>View for #<?php echo($documentId); ?> document</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<h2>View for #<?php echo($documentId); ?> document</h2>
<hr/>
<div id='main'></div>
<hr/>

<a href='document.php?documentId=<?php echo($documentId); ?>'>Edit</a>
<br/>
<a href="index.php">HOME</a>
<br/>
<a href="logout.php">Logout</a>

<script src="https://cdn.rawgit.com/showdownjs/showdown/1.8.6/dist/showdown.min.js"></script>
<script>
function onSucceed(text) {

  var converter = new showdown.Converter();
  var html = converter.makeHtml(text);

  document.getElementById('main').innerHTML = html;
}

function onError(documentId) {

  var content = '<h3>Failed to get document #' + documentId + '.</h3>';
  document.getElementById('main').innerHTML = content;
}

function sendRequest(documentId) {

  clearInterval(timer);

  var xhr = new XMLHttpRequest();

  xhr.onload = function() {

    console.log(xhr.status);
    console.log(xhr.responseText);

    var content = '';

    if (200 === xhr.status) {
      onSucceed(xhr.responseText);
    } else {
      onError(documentId);
    }
  };

  var payload = 'documentId=' + documentId;

  xhr.open('POST', 'content.php', true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send(payload);
}

var timer = setInterval(function () { sendRequest("<?php echo($documentId); ?>"); }, 1000);

</script>

</body>
</html>

