<?php

session_start();
require '../headers/admin_header.php';

require "../bd/initialConfig.php";
require "../error.php";
try
{	
	
	if(!isset($_POST['userCompany']))
		exitWithHttpError(400, 'Missing userCompany');

	if(!isset($_POST['password']))
		exitWithHttpError(400, 'Missing password');

	if(!isset($_POST['idCompany']))
		exitWithHttpError(400, 'idCompany');

	$userCompany = $_POST['userCompany'];
	$password = md5($_POST['password']);
	$idCompany = $_POST['idCompany'];

	date_default_timezone_set('America/Chicago');
	$initialDate = getdate();
	$date = $initialDate['year'].'-'.$initialDate['mon'].'-'.$initialDate['mday'].' '.$initialDate['hours'].':'.$initialDate['minutes'].':'.$initialDate['seconds'];
	$date = new DateTime($date);
	$date = date_format($date, 'Y-m-d H:i:s');
		
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();
	
	$sql = "INSERT INTO usuarios(usuario,contrasena,idEmpresa,permisos,fechaCreacion) VALUES (?,?,?,?,?)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($userCompany,$password,$idCompany,3,$date));


	$sql = "INSERT INTO registrosUsuario(usuario,usuarioRegistro,fecha,tipo) VALUES (?,?,?,?)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($userCompany,$_SESSION['user'],$date,1));


	if($stmt)
		echo "<h3>¡Usuario ".$nameCompany." creado correctamente!</h3>";
	
	$dbh->commit();
	$dbh = null;
		
} catch(PDOException $e)
 {
 	echo '{"Error":{"text":'. $e->getMessage() .'}}';
 }

?>
