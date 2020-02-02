<?php
session_start();

require "bd/initialConfig.php";
require "error.php";

if(!isset($_POST["username"]))
	exitWithHttpError(400, 'Missing username');

if(!isset($_POST["password"]))
	exitWithHttpError(400, 'Missing password');

$user = $_POST["username"];
$password = md5($_POST["password"]);	

try{
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT u.usuario,e.idEmpresa,u.permisos FROM  usuarios u,empresas e  WHERE u.usuario = ? AND u.idEmpresa = e.idEmpresa AND u.contrasena = ?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($user,$password));
	$data = $stmt->fetchAll(PDO::FETCH_OBJ);
	$dbh = null;

	if(count($data)==1){	
		$_SESSION['user'] = $user;
		$_SESSION['permissions'] = $data[0]->permisos;
		$idCompany = $data[0]->idEmpresa;

		echo $idCompany;

		 
	}else echo 0;
	
} catch(PDOException $e){
 	echo '{"Error":{"text":'. $e->getMessage() .'}}';
 }
		
?>