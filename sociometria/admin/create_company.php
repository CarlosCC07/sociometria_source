<?php

session_start();
require '../headers/admin_header.php';

require "../bd/initialConfig.php";
require "../error.php";
try
{	
	if(!isset($_POST['nameCompany']))
		exitWithHttpError(400, 'Missing nameCompany');

	if(!isset($_POST['userCompany']))
		exitWithHttpError(400, 'Missing userCompany');

	if(!isset($_POST['password']))
		exitWithHttpError(400, 'Missing password');

	$nameCompany = $_POST['nameCompany'];
	$userCompany = $_POST['userCompany'];
	$password = md5($_POST['password']);
	date_default_timezone_set('America/Chicago');
	$initialDate = getdate();
	$date = $initialDate['year'].'-'.$initialDate['mon'].'-'.$initialDate['mday'].' '.$initialDate['hours'].':'.$initialDate['minutes'].':'.$initialDate['seconds'];
	$date = new DateTime($date);
	$date = date_format($date, 'Y-m-d H:i:s');
		
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();
	
	$sql = "INSERT INTO empresas(nombreEmpresa,fechaCreacion) VALUES (?,?)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($nameCompany,$date));

	$sql = "SELECT MAX(idEmpresa) AS maxID FROM empresas";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$id = $stmt->fetchAll(PDO::FETCH_OBJ); 	

	$sql = "INSERT INTO registrosEmpresa(idEmpresa,nombreEmpresa,usuarioRegistro,fecha,tipo) VALUES (?,?,?,?,?)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($id[0]->maxID,$nameCompany,$_SESSION['user'],$date,1));

	$sql = "INSERT INTO registrosUsuario(usuario,usuarioRegistro,fecha,tipo) VALUES (?,?,?,?)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($userCompany,$_SESSION['user'],$date,1));

	$sql = "INSERT INTO usuarios(usuario,contrasena,fechaCreacion,idEmpresa,permisos) VALUES (?,?,?,?,?)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($userCompany,$password,$date,$id[0]->maxID,2));
	if($stmt)
		echo "<h3>¡Empresa ".$nameCompany." creada correctamente!</h3>";
	
	$dbh->commit();
	$dbh = null;
		
} catch(PDOException $e)
 {
 	echo '{"Error":{"text":'. $e->getMessage() .'}}';
 }

?>
