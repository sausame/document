<?php

chdir(dirname(__FILE__));
$config = parse_ini_file('../config.ini');

$host = $config["mysql-host"];
$username = $config["mysql-user"];
$password = $config["mysql-password"];
$dbname = $config["db-name"];

$con = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>
