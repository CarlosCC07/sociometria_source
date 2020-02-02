<?php
require "../error.php";
require "../bd/initialConfig.php";
require '../headers/admin_header.php';

if(!isset($_POST['type']))
	exitWithHttpError(400, 'Missing type');

try
{	if(!isset($_POST['value']))
		exitWithHttpError(400, 'Missing value');
	
	$value = $_POST['value'];
	$type = ($_POST['type'] == "nameCompany")?0:1;
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();

	$sql = (!$type)?"SELECT nombreEmpresa FROM empresas WHERE nombreEmpresa = ?":"SELECT usuario FROM usuarios WHERE usuario = ?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($value));
	$exist = $stmt->fetchAll(PDO::FETCH_OBJ);
	if(count($exist))
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
