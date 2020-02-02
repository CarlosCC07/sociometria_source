<?php

session_start();
require '../headers/admin_header.php';

require "../bd/initialConfig.php";
require "../error.php";
try
{	
	if(!isset($_POST['idAnalisis']))
		exitWithHttpError(400, 'Missing idAnalisis');
	if(!isset($_POST['bdName']))
		exitWithHttpError(400, 'Missing bdName');
	if(!isset($_POST['year']))
		exitWithHttpError(400, 'Missing year');

	$idAnalisis = $_POST['idAnalisis'];
	$bdName = $_POST['bdName'];
	$year = $_POST['year'];

	array_map('unlink', glob('../input/'.$bdName.'/'.$year.'/employees/*'));
	array_map('unlink', glob('../input/'.$bdName.'/'.$year.'/survey/*'));
	
	date_default_timezone_set('America/Chicago');
	$initialDate = getdate();
	$date = $initialDate['year'].'-'.$initialDate['mon'].'-'.$initialDate['mday'].' '.$initialDate['hours'].':'.$initialDate['minutes'].':'.$initialDate['seconds'];
	$date = new DateTime($date);
	$date = date_format($date, 'Y-m-d H:i:s');
		
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();

	$sql = "UPDATE analisis SET estatus = ?,bd = ?,ultimoAnalisis = ?,bateo = ?,totalEmpleados = ?,encuestasEnBlanco = ?, nombresReconocidos = ?,nombresNoReconocidos = ?,porSiMismo = ?,tiempoEjecucion = ? WHERE idAnalisis = ?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array(0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,$idAnalisis));

	$sql = "INSERT INTO registrosAnalisis(idAnalisis,nombreAnalisis,usuarioRegistro,fecha,tipo) VALUES (?,?,?,?,?)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($idAnalisis,$bdName,$_SESSION['user'],$date,0));
	
	$sql = "USE `".$bdName."`";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

	$sql = "TRUNCATE TABLE `contadorPersona`;
			TRUNCATE TABLE `encuestaPersona`;
			TRUNCATE TABLE `nombresRepetidos`;
			TRUNCATE TABLE `personas`;
			TRUNCATE TABLE `personasDuplicadas`;
			TRUNCATE TABLE `personasNoReconocidas`;";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();

	$dbh->commit();
	$dbh = null;
		
} catch(PDOException $e)
 {
 	echo '{"Error":{"text":'. $e->getMessage() .'}}';
 }

?>
