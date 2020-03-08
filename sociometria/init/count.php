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

	function var_error_log($object){
	    ob_start();                    // start buffer capture
	    var_dump($object);           // dump the values
	    $contents = ob_get_contents(); // put the buffer into a variable
	    ob_end_clean();                // end capture
	    error_log($contents);        // log contents of the result of var_dump( $object )
	}

	function calculateInd($id,$dbh,$type,$level,$processedKeys){
		$cont = 0;
		//var_error_log($processedKeys);
		if($level == 20){
			return 0;
		}

		$pK = $processedKeys;

		if(!array_key_exists($id,$pK)){
			$pK[$id] = 0;
		}
		
		if($type == 1){
			$sql="SELECT idTrabajador FROM encuestaPersona WHERE idAscendencia1 = '$id'"; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);

			if(is_null($idsInd)){
				return 0;
			}

			$total = count($idsInd);
			$cont = $cont + $total;

			for($i=0;$i<$total;$i++){
				$id2 = $idsInd[$i]->idTrabajador;
				if(!array_key_exists($id2,$pK)){
					$temp = calculateInd($id2,$dbh,1,$level+1,$pK);
					$cont = $cont + $temp;
				} 
				$pK = $processedKeys;
				$pK[$id] = 0;
			}

		} elseif ($type == 2) {
			$sql="SELECT idTrabajador FROM encuestaPersona WHERE idAfinidad1 = '$id'"; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);

			if(is_null($idsInd)){
				return 0;
			}

			$total = count($idsInd);
			$cont = $cont + $total;

			for($i=0;$i<$total;$i++){
				$id2 = $idsInd[$i]->idTrabajador;
				if(!array_key_exists($id2,$pK)){
					$temp = calculateInd($id2,$dbh,2,$level+1,$pK);
					$cont = $cont + $temp;
				} 
				$pK = $processedKeys;
				$pK[$id] = 0;
			}
		} elseif ($type == 3) {
			$sql="SELECT idTrabajador FROM encuestaPersona WHERE idPopularidad1 = '$id'"; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);

			if(is_null($idsInd)){
				return 0;
			}

			$total = count($idsInd);
			$cont = $cont + $total;

			for($i=0;$i<$total;$i++){
				$id2 = $idsInd[$i]->idTrabajador;
				if(!array_key_exists($id2,$pK)){
					$temp = calculateInd($id2,$dbh,3,$level+1,$pK);
					$cont = $cont + $temp;
				} 
				$pK = $processedKeys;
				$pK[$id] = 0;
			}
		}

		return $cont;
		
	}

	function calculatePD($id,$dbh,$type){

		$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAscendencia1 = '$id' OR idAscendencia2 = '$id' OR idAscendencia3 = '$id' OR idAfinidad1 = '$id' OR idAfinidad2 = '$id' OR idAfinidad3 = '$id' OR idPopularidad1 = '$id' OR idPopularidad2 = '$id' OR idPopularidad3 = '$id'";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$idTot = $stmt->fetchAll(PDO::FETCH_OBJ); // ids de las personas que votaron por el
		$totalTot = count($idTot);

		$processedKeys = array();
		$processedKeys[$id] = 0;

		for($k = 0;$k < $totalTot;$k++){
			$id3 = $idTot[$k]->idTrabajador;
			$processedKeys[$id3] = 0;
		}

		if($type == 1){
			$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAscendencia1 = '$id' OR idAscendencia2 = '$id' OR idAscendencia3 = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idDir = $stmt->fetchAll(PDO::FETCH_OBJ); // ids de las personas que votaron por el
			$totalDir = count($idDir);

			$sql="SELECT idTrabajador FROM encuestaPersona WHERE idAscendencia1 = '$id' OR idAscendencia2 = '$id' OR idAscendencia3 = '$id'";	
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idInd = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalInd = count($idInd);

			for($j = 0; $j <$totalInd;$j++){
				$id2 = $idInd[$j]->idTrabajador;
				$contAsc = calculateInd($id2,$dbh,1,1,$processedKeys);
				$sql = "UPDATE contadorPersona SET ascendenciaPD = ascendenciaPD + '$contAsc' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			}

			$sql = "UPDATE contadorPersona SET ascendenciaPD = ascendenciaPD + '$totalDir' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

		} elseif($type == 2){
			$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAfinidad1 = '$id' OR idAfinidad2 = '$id' OR idAfinidad3 = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idDir = $stmt->fetchAll(PDO::FETCH_OBJ); // ids de las personas que votaron por el
			$totalDir = count($idDir);

			$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAfinidad1 = '$id' OR idAfinidad2 = '$id' OR idAfinidad3 = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idInd = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalInd = count($idInd);

			for($j = 0; $j <$totalInd;$j++){
				$id2 = $idInd[$j]->idTrabajador;
				$contAfi = calculateInd($id2,$dbh,2,1,$processedKeys);
				$sql = "UPDATE contadorPersona SET afinidadPD = afinidadPD + '$contAfi' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			}

			$sql = "UPDATE contadorPersona SET afinidadPD = afinidadPD + '$totalDir' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

		} elseif($type == 3){
			$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idPopularidad1 = '$id' OR idPopularidad2 = '$id' OR idPopularidad3 = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idDir = $stmt->fetchAll(PDO::FETCH_OBJ); // ids de las personas que votaron por el
			$totalDir = count($idDir);

			$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idPopularidad1 = '$id' OR idPopularidad2 = '$id' OR idPopularidad3 = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idInd = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalInd = count($idInd);

			for($j = 0; $j <$totalInd;$j++){
				$id2 = $idInd[$j]->idTrabajador;
				$contPop = calculateInd($id2,$dbh,3,1,$processedKeys);
				$sql = "UPDATE contadorPersona SET popularidadPD = popularidadPD + '$contPop' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			}

			$sql = "UPDATE contadorPersona SET popularidadPD = popularidadPD + '$totalDir' WHERE idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

		}

	}
	
	
	try {
		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
		// -----Directos
	
		$sql="SELECT * FROM encuestaPersona";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$id = $stmt->fetchAll(PDO::FETCH_OBJ); //Saco todas las personas que votaron
		$total = count($id);
		
		
		for($i = 0;$i < $total; $i++){
			$ids = $id[$i];
			if($ids->idAscendencia1){
				$sql = "UPDATE contadorPersona SET contAscendencia1 = contAscendencia1 + 1,ascendenciaDir = ascendenciaDir +1,totalDirecto = totalDirecto +1 WHERE idTrabajador = ".$ids->idAscendencia1."";
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
			if($ids->idAscendencia2){
				$sql = "UPDATE contadorPersona SET contAscendencia2 = contAscendencia2 + 1,ascendenciaDir = ascendenciaDir +1,totalDirecto = totalDirecto +1 WHERE idTrabajador = ".$ids->idAscendencia2."";
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
			if($ids->idAscendencia3){
				$sql = "UPDATE contadorPersona SET contAscendencia3 = contAscendencia3 + 1,ascendenciaDir = ascendenciaDir +1,totalDirecto = totalDirecto +1 WHERE idTrabajador = ".$ids->idAscendencia3."";
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
			if($ids->idAfinidad1){
				$sql = "UPDATE contadorPersona SET contAfinidad1 = contAfinidad1 + 1,afinidadDir = afinidadDir +1,totalDirecto = totalDirecto +1 WHERE idTrabajador = ".$ids->idAfinidad1."";
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
			if($ids->idAfinidad2){
				$sql = "UPDATE contadorPersona SET contAfinidad2 = contAfinidad2 + 1,afinidadDir = afinidadDir +1,totalDirecto = totalDirecto +1 WHERE idTrabajador = ".$ids->idAfinidad2."";
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
			if($ids->idAfinidad3){
				$sql = "UPDATE contadorPersona SET contAfinidad3 = contAfinidad3 + 1,afinidadDir = afinidadDir +1,totalDirecto = totalDirecto +1 WHERE idTrabajador = ".$ids->idAfinidad3."";
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
			if($ids->idPopularidad1){
				$sql = "UPDATE contadorPersona SET contPopularidad1 = contPopularidad1 + 1,popularidadDir = popularidadDir +1,totalDirecto = totalDirecto +1 WHERE idTrabajador = ".$ids->idPopularidad1."";
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
			if($ids->idPopularidad2){
				$sql = "UPDATE contadorPersona SET contPopularidad2 = contPopularidad2 + 1,popularidadDir = popularidadDir +1,totalDirecto = totalDirecto +1 WHERE idTrabajador = ".$ids->idPopularidad2."";
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
			if($ids->idPopularidad3){
				$sql = "UPDATE contadorPersona SET contPopularidad3 = contPopularidad3 + 1,popularidadDir = popularidadDir +1,totalDirecto = totalDirecto +1 WHERE idTrabajador = ".$ids->idPopularidad3."";
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
		}
	
	
	// Fin directos ------
	
		$sql="SELECT idTrabajador FROM contadorPersona";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$ids = $stmt->fetchAll(PDO::FETCH_OBJ); //Saco todas las personas que fueron votadas
		
		$total = count($ids);
		
		for($i = 0;$i < $total; $i++){
			$id = $ids[$i]->idTrabajador; // Tengo el id del que fue votado

			$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAscendencia1 = '$id' OR idAscendencia2 = '$id' OR idAscendencia3 = '$id' OR idAfinidad1 = '$id' OR idAfinidad2 = '$id' OR idAfinidad3 = '$id' OR idPopularidad1 = '$id' OR idPopularidad2 = '$id' OR idPopularidad3 = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idTot = $stmt->fetchAll(PDO::FETCH_OBJ); // ids de las personas que votaron por el
			$totalTot = count($idTot);

			$pkArr = array();
			$pkArr[$id] = 0;

			for($k = 0;$k < $totalTot;$k++){
				$id3 = $idTot[$k]->idTrabajador;
				$pkArr[$id3] = 0;
			}

			calculatePD($id,$dbh,1);
			calculatePD($id,$dbh,2);
			calculatePD($id,$dbh,3);
			
			// INDIRECTOS
			//************************* ASCENDENCIA
			
		
			$sql="SELECT idTrabajador FROM encuestaPersona WHERE idAscendencia1 = '$id' OR idAscendencia2 = '$id' OR idAscendencia3 = '$id'";
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idInd = $stmt->fetchAll(PDO::FETCH_OBJ); // ids de las personas que votaron por el
			$totalInd = count($idInd);
			
			for($j = 0; $j <$totalInd;$j++){
				$id2 = $idInd[$j]->idTrabajador;
				
				$cont = calculateInd($id2,$dbh,1,1,$pkArr);
				$sql = "UPDATE contadorPersona SET ascendenciaInd = ascendenciaInd + '$cont' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				
				$sql = "UPDATE contadorPersona SET totalIndirecto = totalIndirecto + '$cont' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();

				$sql="SELECT ascendenciaDir FROM contadorPersona WHERE idTrabajador = '$id2'"; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$value = $stmt->fetchAll(PDO::FETCH_OBJ);
				$cont = $value[0] -> ascendenciaDir;
				$sql = "UPDATE contadorPersona SET ascendenciaIndAmp = ascendenciaIndAmp + '$cont' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
						
			}
			
			//************************* AFINIDAD
			
			$sql="SELECT idTrabajador FROM encuestaPersona WHERE idAfinidad1 = '$id' OR idAfinidad2 = '$id' OR idAfinidad3 = '$id'";
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idInd = $stmt->fetchAll(PDO::FETCH_OBJ); // ids de las personas que votaron por el
			$totalInd = count($idInd);
			
			for($j = 0; $j <$totalInd;$j++){
				$id2 = $idInd[$j]->idTrabajador;
				/*
				$sql="SELECT contAfinidad1 FROM contadorPersona WHERE idTrabajador = '$id2'"; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$value = $stmt->fetchAll(PDO::FETCH_OBJ);
				$cont = $value[0] -> contAfinidad1;
				*/
				$cont = calculateInd($id2,$dbh,2,1,$pkArr);
				$sql = "UPDATE contadorPersona SET afinidadInd = afinidadInd + '$cont' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				
				$sql = "UPDATE contadorPersona SET totalIndirecto = totalIndirecto + '$cont' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();

				$sql="SELECT afinidadDir FROM contadorPersona WHERE idTrabajador = '$id2'"; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$value = $stmt->fetchAll(PDO::FETCH_OBJ);
				$cont = $value[0] -> afinidadDir;
				$sql = "UPDATE contadorPersona SET afinidadIndAmp = afinidadIndAmp + '$cont' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
			
			//********************* POPULARIDAD
			
			
			$sql="SELECT idTrabajador FROM encuestaPersona WHERE idPopularidad1 = '$id' OR idPopularidad2 = '$id' OR idPopularidad3 = '$id'";
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idInd = $stmt->fetchAll(PDO::FETCH_OBJ); // ids de las personas que votaron por el
			$totalInd = count($idInd);
			
			for($j = 0; $j <$totalInd;$j++){
				$id2 = $idInd[$j]->idTrabajador;
				/*
				$sql="SELECT contPopularidad1 FROM contadorPersona WHERE idTrabajador = '$id2'"; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$value = $stmt->fetchAll(PDO::FETCH_OBJ);
				$cont = $value[0] -> contPopularidad1;
				*/
				$cont = calculateInd($id2,$dbh,3,1,$pkArr);
				$sql = "UPDATE contadorPersona SET popularidadInd = popularidadInd + '$cont' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				
				$sql = "UPDATE contadorPersona SET totalIndirecto = totalIndirecto + '$cont' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();

				$sql="SELECT popularidadDir FROM contadorPersona WHERE idTrabajador = '$id2'"; // de una persona que voto, saco cuanto tiene en primer lugar de ascendencia
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$value = $stmt->fetchAll(PDO::FETCH_OBJ);
				$cont = $value[0] -> popularidadDir;
				$sql = "UPDATE contadorPersona SET popularidadIndAmp = popularidadIndAmp + '$cont' WHERE idTrabajador = '$id'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			
			}
		
		}
		
		$dbh = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	
	$endtime = microtime(true);
	$_SESSION['time']+= $endtime - $starttime;

    ?>
