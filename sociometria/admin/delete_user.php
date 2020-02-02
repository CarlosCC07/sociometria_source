'<?php

session_start();
require '../headers/admin_header.php';

require "../bd/initialConfig.php";
require "../error.php";
try
{	
	if(!isset($_POST['idUser']))
		exitWithHttpError(400, 'Missing id');

	if(!isset($_POST['user']))
		exitWithHttpError(400, 'Missing id');

	$idUser = $_POST['idUser'];
	$user = $_POST['user'];

	date_default_timezone_set('America/Chicago');
	$initialDate = getdate();
	$date = $initialDate['year'].'-'.$initialDate['mon'].'-'.$initialDate['mday'].' '.$initialDate['hours'].':'.$initialDate['minutes'].':'.$initialDate['seconds'];
	$date = new DateTime($date);
	$date = date_format($date, 'Y-m-d H:i:s');
		
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();

	$sql ="DELETE FROM usuarios WHERE idUsuario = ?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($idUser));

	$sql = "INSERT INTO registrosUsuario(usuario,usuarioRegistro,fecha,tipo) VALUES (?,?,?,?)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($user,$_SESSION['user'],$date,0));
	if($stmt)
		echo "1";
	else
		echo "0";


	$dbh->commit();
	$dbh = null;
		
} catch(PDOException $e)
 {
 	echo '{"Error":{"text":'. $e->getMessage() .'}}';
 }

?>
