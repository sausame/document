<?php

class Conf {

	public $host;
	public $username;
	public $password;

	public function __construct() {
		$config = parse_ini_file('config.ini');

		$this->host = $config["mysql-host"];
		$this->username = $config["mysql-user"];
		$this->password = $config["mysql-password"];
	}

	// Connecting, selecting database
	public function connDatabase ($db_name){
		$link = mysql_connect($this->host, $this->username, $this->password) or die('Could not connect: ' . mysql_error());
		mysql_select_db($db_name) or die('Could not select database');
	}
}

?>
