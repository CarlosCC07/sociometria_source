<?php

session_start();
require '../headers/admin_header.php';

require "../bd/initialConfig.php";
require "../error.php";
try
{	
	
	if(!isset($_POST['password']))
		exitWithHttpError(400, 'Missing password');

	if(!isset($_POST['idUser']))
		exitWithHttpError(400, 'Missing idUser');


	$password = md5($_POST['password']);
	$idUser = $_POST['idUser'];

		
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();

	$sql = "UPDATE usuarios SET contrasena = ? WHERE idUsuario = ?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($password,$idUser));
	if($stmt)
		echo 1;
	else
		echo 0;
	$dbh->commit();

	$dbh = null;
		
} catch(PDOException $e)
 {
 	echo '{"Error":{"text":'. $e->getMessage() .'}}';
 }

?>
