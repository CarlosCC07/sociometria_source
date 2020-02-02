<?php

session_start();
require '../headers/admin_header.php';

require "../bd/initialConfig.php";
require "../error.php";
try
{	
	if(!isset($_POST['idAnalisis']))
		exitWithHttpError(400, 'Missing idAnalisis');
	if(!isset($_POST['nameCompany']))
		exitWithHttpError(400, 'Missing nameCompany');
	if(!isset($_POST['year']))
		exitWithHttpError(400, 'Missing year');

	$idAnalisis = $_POST['idAnalisis'];
	$nameCompany = $_POST['nameCompany'];
	$year = $_POST['year'];
	$dbName = str_replace(' ', '',$nameCompany.$year);

	date_default_timezone_set('America/Chicago');
	$initialDate = getdate();
	$date = $initialDate['year'].'-'.$initialDate['mon'].'-'.$initialDate['mday'].' '.$initialDate['hours'].':'.$initialDate['minutes'].':'.$initialDate['seconds'];
	$date = new DateTime($date);
	$date = date_format($date, 'Y-m-d H:i:s');

	$dirPath = '../input/'.$dbName;
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
    	$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
	}
	rmdir($dirPath);
		
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();
	
	$sql ="DROP DATABASE `".$dbName."`";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	
	$sql ="DELETE FROM analisis WHERE idAnalisis = ?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($idAnalisis));

	$sql = "INSERT INTO registrosAnalisis(idAnalisis,nombreAnalisis,usuarioRegistro,fecha,tipo) VALUES (?,?,?,?,?)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($idAnalisis,$dbName,$_SESSION['user'],$date,2));


	$dbh->commit();
	$dbh = null;
		
} catch(PDOException $e)
 {
 	echo '{"Error":{"text":'. $e->getMessage() .'}}';
 }

?>
