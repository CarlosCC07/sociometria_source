<?php
require "../error.php";
require "../bd/initialConfig.php";
require '../headers/admin_header.php';

try
{	if(!isset($_POST['value']))
		exitWithHttpError(400, 'Missing value');

	if(!isset($_POST['name']))
		exitWithHttpError(400, 'Missing name');
	
	$year = explode("-",$_POST['value'])[0];
	$name = str_replace(" ", "", $_POST['name']);
	$db = $name.$year;

	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();

	$sql = "SHOW DATABASES LIKE ?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($db));
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
