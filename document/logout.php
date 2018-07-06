<?php

$past = time() - 1800; 

//this makes the time in the past to destroy the cookie 
setcookie('ID_your_site', gone, $past, '/');
setcookie('Key_your_site', gone, $past, '/');

header("Location: login.php"); // Redirecting To Home Page
?>

