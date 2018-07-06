<?php

require('db.php');

if (isset($_COOKIE['ID_your_site'])) { //if there is, it logs you in and directes you to the members page

 	$username = $_COOKIE['ID_your_site']; 
 	$password = $_COOKIE['Key_your_site'];

	$query = "SELECT UserId FROM `UserTable` WHERE Username = '$username' and Password = '$password'";

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

	$row = mysqli_fetch_row($result);
	$userId = $row[0];
} else {
	header("Location: login.php");
	exit();
}

?>
