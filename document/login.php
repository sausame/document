<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>Login</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?php
	require('db.php');

	if (isset($_COOKIE['ID_your_site'])) { //if there is, it logs you in and directes you to the members page

		$username = $_COOKIE['ID_your_site'];
		$password = $_COOKIE['Key_your_site'];

		$query = "SELECT * FROM `UserTable` WHERE Username = '$username' and Password = '$password'";
		$result = mysqli_query($con, $query) or die(mysql_error());
		$rows = mysqli_num_rows($result);
		if(0 == $rows) {
			$past = time() - 1800;

			//this makes the time in the past to destroy the cookie
			setcookie('ID_your_site', gone, $past, '/');
			setcookie('Key_your_site', gone, $past, '/');

			header("Location: login.php");
			exit();
		}
		header("Location: index.php"); // Redirect user to index.php
		exit();
	}

	// If form submitted, query values from the database.
	if (isset($_POST['username'])){

		$username = stripslashes($_REQUEST['username']); // removes backslashes
		$username = mysqli_real_escape_string($con, $username); //escapes special characters in a string
		$password = stripslashes($_REQUEST['password']);
		$password = mysqli_real_escape_string($con, $password);

		// Checking is user existing in the database or not
		$query = "SELECT Password FROM `UserTable` WHERE Username = '$username'";
		$result = mysqli_query($con,$query) or die(mysql_error());
		$rows = mysqli_num_rows($result);

		$found = false;

		if ($rows == 1) {
			$row = mysqli_fetch_row($result);
			$hash = $row[0];
			$found = password_verify($password, $hash);
		}

		if ($found) {
			$hour = time() + (7 * 24 * 3600); // A week
			setcookie('ID_your_site', $username, $hour, '/');
			setcookie('Key_your_site', $hash, $hour, '/');
			header("Location: index.php"); // Redirect user to index.php
		} else {
?>
<div class="form">
  <h3>Username or password is wrong!</h3>
  <br/>Please <a href="login.php" onclick="window.history.back(); return false;">relogin</a>.
</div>
<?php
		}

	} else {
?>
<div class="form">
<h1>Login</h1>
<form action="" method="post" name="login">
<input type="text" name="username" placeholder="Username" required />
<input type="password" name="password" placeholder="Password" required />
<br/>
<input name="submit" type="submit" value="Login" />
</form>
<p><a href='registration.php'>Register</a></p>
<p><a href='resetpw.php'>Reset Password</a></p>

</div>
<?php } ?>


</body>
</html>
