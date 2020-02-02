<?php

session_start();
require '../headers/admin_header.php';

require "../bd/initialConfig.php";
require "../error.php";
try
{	
	if(!isset($_POST['id']))
		exitWithHttpError(400, 'Missing id');
	if(!isset($_POST['cName']))
		exitWithHttpError(400, 'Missing cName');

	$id = $_POST['id'];
	$nameCompany = $_POST['cName'];
	date_default_timezone_set('America/Chicago');
	$initialDate = getdate();
	$date = $initialDate['year'].'-'.$initialDate['mon'].'-'.$initialDate['mday'].' '.$initialDate['hours'].':'.$initialDate['minutes'].':'.$initialDate['seconds'];
	$date = new DateTime($date);
	$date = date_format($date, 'Y-m-d H:i:s');
		
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();

	$sql ="SELECT idAnalisis FROM analisis WHERE idEmpresa = ?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($id));
	$analisisAll = $stmt->fetchAll(PDO::FETCH_OBJ);

	

	if(!count($analisisAll)){
		
		//Seleccionar todos los usuarios y eliminarlos
		$sql ="SELECT usuario FROM usuarios WHERE idEmpresa = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->execute(array($id));
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);

		foreach($users as $key => $user){
			$sql = "INSERT INTO registrosUsuario(usuario,usuarioRegistro,fecha,tipo) VALUES (?,?,?,?)";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($user->usuario,$_SESSION['user'],$date,0));
		}

		$sql ="DELETE FROM usuarios WHERE idEmpresa = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->execute(array($id));

		$sql ="DELETE FROM empresas WHERE idEmpresa = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->execute(array($id));

		$sql = "INSERT INTO registrosEmpresa(idEmpresa,nombreEmpresa,usuarioRegistro,fecha,tipo) VALUES (?,?,?,?,?)";
		$stmt = $dbh->prepare($sql);
		$stmt->execute(array($id,$nameCompany,$_SESSION['user'],$date,0));
		if($stmt)
			echo "1";
	}else{
		echo "0";
	}

	$dbh->commit();
	$dbh = null;
		
} catch(PDOException $e)
 {
 	echo '{"Error":{"text":'. $e->getMessage() .'}}';
 }

?>
