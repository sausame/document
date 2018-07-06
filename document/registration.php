<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>Register</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?php
	require('db.php');

	$result = null;

	// If form submitted, insert values into the database.
	if (isset($_REQUEST['username'])){
		$username = stripslashes($_REQUEST['username']); // removes backslashes
		$username = mysqli_real_escape_string($con,$username); //escapes special characters in a string
		$email = stripslashes($_REQUEST['email']);
		$email = mysqli_real_escape_string($con,$email);
		$password = stripslashes($_REQUEST['password']);
		$password = mysqli_real_escape_string($con,$password);

		$password = password_hash($password, PASSWORD_DEFAULT);

		$query = "INSERT into `users` (username, password, email, createTime, token, tokenUpdateTime) VALUES ('$username', '$password', '$email', NULL, NULL, NULL)";
		$result = mysqli_query($con, $query);
		if($result){
			echo "<div class='form'><h3>Registration succeeded.</h3><br/> Back to <a href='login.php'>HOME</a>.</div>";
		} else {
			echo "<div class='form'><h3>Username or email is occupied, please try another one.</h3></div>";
		}
	}

	if (! $result) {
?>
<div class="form">
<h1>Registration</h1>
<form name="registration" action="" method="post">
<input type="email" name="email" placeholder="Email" required />
<input type="text" name="username" placeholder="Username" required />
<input type="password" name="password" placeholder="Password" required />
<input type="submit" name="submit" value="Register" />
</form>
</div>
<?php } ?>
</body>
</html>
