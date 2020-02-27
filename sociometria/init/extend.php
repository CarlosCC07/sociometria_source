<?php

	$starttime = microtime(true);
	set_time_limit(800);
	
    require "../bd/config.php"; //Incluyo la base de datos
	
	/*
	 *1. Saco todas las persona votadas
	 *2. por cada persona votada, saco los ids de quienes votaron por ellas
	 *
	*/
	global $dbuser;
	global $dbpass;
	global $dbname;
	global $dbhost;

	function calculateInd($id,$dbh,$type,$level){

		$cont = 0;

		if($level > 20) {
			return $cont;
		}

		if($type == 1){
			$sql2 = "SELECT idTrabajador FROM encuestaPersona WHERE idAscendencia1 = '$id'";
			$stmt = $dbh->prepare($sql2);
			$stmt->execute();
			$idsFirst = $stmt->fetchAll(PDO::FETCH_OBJ);
			if(is_null($idsFirst)){
				return 0;
			}
			$total = count($idsFirst);
			for($i = 0; $i < $total; $i++){
				$temp = calculateInd($idsFirst[$i]->idTrabajador,$dbh,1,$level+1);
				$sql="SELECT contAscendencia1 FROM contadorPersona WHERE idTrabajador = ".$idsFirst[$i]->idTrabajador.""; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$value = $stmt->fetchAll(PDO::FETCH_OBJ);
				$cont = $value[0]->contAscendencia1;
				$cont = $cont + $temp;
			}
			if($level == 1){
				$sql3 = "UPDATE contadorPersona SET total = total + '$total' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql3);
				$stmt->execute();
			}
		}elseif ($type == 2) {
			$sql2 = "SELECT idTrabajador FROM encuestaPersona WHERE idAfinidad1 = '$id'";
			$stmt = $dbh->prepare($sql2);
			$stmt->execute();
			$idsFirst = $stmt->fetchAll(PDO::FETCH_OBJ);
			if(is_null($idsFirst)){
				return 0;
			}
			$total = count($idsFirst);
			for($i = 0; $i < $total; $i++){
				$temp = calculateInd($idsFirst[$i]->idTrabajador,$dbh,2,$level+1);
				$sql="SELECT contAfinidad1 FROM contadorPersona WHERE idTrabajador = ".$idsFirst[$i]->idTrabajador.""; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$value = $stmt->fetchAll(PDO::FETCH_OBJ);
				$cont = $value[0]->contAfinidad1;
				$cont = $cont + $temp;
			}
			if($level == 1){
				$sql3 = "UPDATE contadorPersona SET total = total + '$total' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql3);
				$stmt->execute();
			}
		}elseif ($type == 3) {
			$sql2 = "SELECT idTrabajador FROM encuestaPersona WHERE idPopularidad1 = '$id'";
			$stmt = $dbh->prepare($sql2);
			$stmt->execute();
			$idsFirst = $stmt->fetchAll(PDO::FETCH_OBJ);
			if(is_null($idsFirst)){
				return 0;
			}
			$total = count($idsFirst);
			for($i = 0; $i < $total; $i++){
				$temp = calculateInd($idsFirst[$i]->idTrabajador,$dbh,3,$level+1);
				$sql="SELECT contPopularidad1 FROM contadorPersona WHERE idTrabajador = ".$idsFirst[$i]->idTrabajador.""; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$value = $stmt->fetchAll(PDO::FETCH_OBJ);
				$cont = $value[0]->contPopularidad1;
				$cont = $cont + $temp;
			}
			if($level == 1){
				$sql3 = "UPDATE contadorPersona SET total = total + '$total' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql3);
				$stmt->execute();
			}
		}

		return $cont;
	}

	try {
		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql="SELECT idTrabajador FROM contadorPersona";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$ids = $stmt->fetchAll(PDO::FETCH_OBJ); //Saco todas las personas que fueron votadas
		
		$total = count($ids);

		for($i = 0;$i < $total; $i++){
			$id = $ids[$i]->idTrabajador; // Tengo el id del que fue votado
			/************ ASCENDENCIA *************/
			$cont = calculateInd($id,$dbh,1,1);
			$sql = "UPDATE contadorPersona SET ascendenciaInd = ascendenciaInd + '$cont' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			
			$sql = "UPDATE contadorPersona SET totalIndirecto = totalIndirecto + '$cont' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			
			$sql = "UPDATE contadorPersona SET ascendenciaIndAmp = ascendenciaIndAmp + '$cont' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			/************ AFINIDAD *************/
			$cont = calculateInd($id,$dbh,2,1);
			$sql = "UPDATE contadorPersona SET afinidadInd = afinidadInd + '$cont' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			
			$sql = "UPDATE contadorPersona SET totalIndirecto = totalIndirecto + '$cont' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			
			$sql = "UPDATE contadorPersona SET afinidadIndAmp = afinidadIndAmp + '$cont' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			/************ POPULARIDAD *************/
			$cont = calculateInd($id,$dbh,3,1);
			$sql = "UPDATE contadorPersona SET popularidadInd = popularidadInd + '$cont' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			
			$sql = "UPDATE contadorPersona SET totalIndirecto = totalIndirecto + '$cont' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			
			$sql = "UPDATE contadorPersona SET popularidadIndAmp = popularidadIndAmp + '$cont' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
		}

		$dbh = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	
	$endtime = microtime(true);
	$_SESSION['time']+= $endtime - $starttime;

    ?>
