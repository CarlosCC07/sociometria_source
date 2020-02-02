<?php
	
    require "../bd/config.php"; //Incluyo la base de datos
	require '../headers/admin_header.php';

	
	function clean(){
		
		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
		global $mas;
		
		
		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "TRUNCATE TABLE `contadorPersona`";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$sql = "TRUNCATE TABLE `encuestaPersona`";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$sql = "TRUNCATE TABLE `nombresRepetidos`";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$dbh = null;
			
			
		} catch(PDOException $e) {
			echo '{"Error":{"text":'. $e->getMessage() .'}}';
		}
		
	}

    clean();
    ?>
