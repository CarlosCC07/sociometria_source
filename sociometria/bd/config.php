<?php
	if(session_status() == PHP_SESSION_NONE) 
		session_start();

	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpass = 'PeopleMountainInsugiri**';
	$dbname = $_SESSION['company'];
?>
