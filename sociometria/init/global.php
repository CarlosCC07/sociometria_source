<?php
	require "../bd/config.php"; //Incluyo la base de datos
	$starttime = microtime(true);
	set_time_limit(0);
/*
	function calculateInd($id,$dbh,$type,$level){

		if($level > 20) {
			return 0;
		}

		if($type == 1){
			$sql2 = "SELECT idTrabajador FROM encuestaPersona WHERE idAscendencia1 = ".$id."";
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
				$sql3 = "UPDATE contadorPersona SET total = total + ".$total." WHERE idTrabajador = ".$id."";
				$stmt = $dbh->prepare($sql3);
				$stmt->execute();
			}
		}elseif ($type == 2) {
			$sql2 = "SELECT idTrabajador FROM encuestaPersona WHERE idAfinidad1 = ".$id."";
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
				$sql3 = "UPDATE contadorPersona SET total = total + ".$total." WHERE idTrabajador = ".$id."";
				$stmt = $dbh->prepare($sql3);
				$stmt->execute();
			}
		}elseif ($type == 3) {
			$sql2 = "SELECT idTrabajador FROM encuestaPersona WHERE idPopularidad1 = ".$id."";
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
				$sql3 = "UPDATE contadorPersona SET total = total + ".$total." WHERE idTrabajador = ".$id."";
				$stmt = $dbh->prepare($sql3);
				$stmt->execute();
			}
		}

		return $cont;
	}
	*/
		
	try {
		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		//Saco todas los id de Personas
		$sql = "SELECT idTrabajador FROM personas";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$idsWorker = $stmt->fetchAll(PDO::FETCH_OBJ); 
		$totalWorkers = count($idsWorker);
		for($i=0;$i<$totalWorkers;$i++){ //Saco quienes votaron por mi directamente
			$totalGlobal = 0;$totalInd = 0;$totalDir = 0;$totalGlobalAmp = 0;$totalIndAmp = 0;$totalIndByType = 0;$idsInd = (object)array();$auxIndCalc=0;

			//Busco directos por fersonas
			$sql="SELECT idTrabajador FROM encuestaPersona ep WHERE idAscendencia1 = ".$idsWorker[$i]->idTrabajador." OR idAscendencia2 = ".$idsWorker[$i]->idTrabajador." OR idAscendencia3 = ".$idsWorker[$i]->idTrabajador." OR idAfinidad1 = ".$idsWorker[$i]->idTrabajador." OR idAfinidad2 = ".$idsWorker[$i]->idTrabajador." OR idAfinidad3 = ".$idsWorker[$i]->idTrabajador." OR idPopularidad1 = ".$idsWorker[$i]->idTrabajador." OR idPopularidad2 = ".$idsWorker[$i]->idTrabajador." OR idPopularidad3 = ".$idsWorker[$i]->idTrabajador;
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ); 
			
			$totalDir = count($idsDir); //Total de directos total

							
			//************** Saco los que votaron en ascendencia, luego de esos busco los de as1 sin repetir
			$sql = "SELECT idTrabajador FROM encuestaPersona ep WHERE idAscendencia1 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalDirAux = count($idsDir);
			
			for($j=0;$j<$totalDirAux;$j++){
				$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAscendencia1 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);
				$auxIndCalc += count($idsInd);
				$totalInd = $totalInd + count($idsInd);
				$totalIndAmp = $totalIndAmp + count($idsInd);
			
			}

			//Guardo el total individual de ascendencia
			$sql="SELECT idTrabajador FROM encuestaPersona ep WHERE idAscendencia1 = ".$idsWorker[$i]->idTrabajador." OR idAscendencia2 = ".$idsWorker[$i]->idTrabajador." OR idAscendencia3 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$auxDir = $stmt->fetchAll(PDO::FETCH_OBJ); 
			$totalIndByType =  count($auxDir) + $auxIndCalc;

			$sql = "UPDATE contadorPersona SET ascendenciaTotal = ".$totalIndByType." WHERE idTrabajador = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();


			$auxIndCalc = 0;
			//****

			//**

			$sql = "SELECT idTrabajador FROM encuestaPersona ep WHERE idAscendencia2 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalDirAux = count($idsDir);
			
			for($j=0;$j<$totalDirAux;$j++){
				$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAscendencia2 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);
				$totalIndAmp = $totalIndAmp + count($idsInd);
			
			}

			$sql = "SELECT idTrabajador FROM encuestaPersona ep WHERE idAscendencia3 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalDirAux = count($idsDir);
			
			for($j=0;$j<$totalDirAux;$j++){
				$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAscendencia3 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);
				$totalIndAmp = $totalIndAmp + count($idsInd);
			
			}


			
			//************** Saco los que votaron en afinidad, luego de esos busco los de af1 sin repetir
			$sql = "SELECT idTrabajador FROM encuestaPersona ep WHERE idAfinidad1 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalDirAux = count($idsDir);
			
			for($j=0;$j<$totalDirAux;$j++){
				$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAfinidad1 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);
				$auxIndCalc += count($idsInd);
				$totalInd = $totalInd + count($idsInd);
				$totalIndAmp = $totalIndAmp + count($idsInd);
			
			}

			//Guardo el total individual de afinidad
			$sql="SELECT idTrabajador FROM encuestaPersona ep WHERE idAfinidad1 = ".$idsWorker[$i]->idTrabajador." OR idAfinidad2 = ".$idsWorker[$i]->idTrabajador." OR idAfinidad3 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$auxDir = $stmt->fetchAll(PDO::FETCH_OBJ); 
			$totalIndByType = count($auxDir) + $auxIndCalc;

			$sql = "UPDATE contadorPersona SET afinidadTotal = ".$totalIndByType." WHERE idTrabajador = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			$auxIndCalc = 0;
			//****

			$sql = "SELECT idTrabajador FROM encuestaPersona ep WHERE idAfinidad2 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalDirAux = count($idsDir);
			
			for($j=0;$j<$totalDirAux;$j++){
				$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAfinidad2 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);
				$totalIndAmp = $totalIndAmp + count($idsInd);
			
			}

			$sql = "SELECT idTrabajador FROM encuestaPersona ep WHERE idAfinidad3 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalDirAux = count($idsDir);
			
			for($j=0;$j<$totalDirAux;$j++){
				$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idAfinidad3 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);
				$totalIndAmp = $totalIndAmp + count($idsInd);
			
			}
			
			
			//**************** Saco los que votaron en popularidad, luego de esos busco los de pop1 sin repetir
			$sql = "SELECT idTrabajador FROM encuestaPersona ep WHERE idPopularidad1 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalDirAux = count($idsDir);
			
			for($j=0;$j<$totalDirAux;$j++){
				$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idPopularidad1 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);
				$auxIndCalc += count($idsInd);
				$totalInd = $totalInd + count($idsInd);
				$totalIndAmp = $totalIndAmp + count($idsInd);
			}

			//Guardo el total individual de popularidad
			$sql="SELECT idTrabajador FROM encuestaPersona ep WHERE idPopularidad1 = ".$idsWorker[$i]->idTrabajador." OR idPopularidad2 = ".$idsWorker[$i]->idTrabajador." OR idPopularidad3 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$auxDir = $stmt->fetchAll(PDO::FETCH_OBJ); 
			$totalIndByType = count($auxDir) + $auxIndCalc;

			$sql = "UPDATE contadorPersona SET popularidadTotal = ".$totalIndByType." WHERE idTrabajador = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			//-----

			//****
			$sql = "SELECT idTrabajador FROM encuestaPersona ep WHERE idPopularidad2 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalDirAux = count($idsDir);
			
			for($j=0;$j<$totalDirAux;$j++){
				$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idPopularidad2 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);
				$totalIndAmp = $totalIndAmp + count($idsInd);
			}

			$sql = "SELECT idTrabajador FROM encuestaPersona ep WHERE idPopularidad3 = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
			$totalDirAux = count($idsDir);
			
			for($j=0;$j<$totalDirAux;$j++){
				$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE idPopularidad3 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ);
				$totalIndAmp = $totalIndAmp + count($idsInd);
			}
			
			$totalGlobal = $totalDir + $totalInd;
			$totalGlobalAmp = $totalDir + $totalIndAmp;
			
			
			//Guardo el total global
			//Prueba C: $sql = "UPDATE contadorPersona SET total = ".$totalGlobal." WHERE idTrabajador = ".$idsWorker[$i]->idTrabajador;
			//Prueba Z: $sql = "UPDATE contadorPersona SET total = total + ".$totalGlobal." WHERE idTrabajador = ".$idsWorker[$i]->idTrabajador;
			$sql = "UPDATE contadorPersona SET total = ".$totalGlobal." WHERE idTrabajador = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			$sql = "UPDATE contadorPersona SET totalAmp = ".$totalGlobalAmp." WHERE idTrabajador = ".$idsWorker[$i]->idTrabajador;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			

		}

		/*
		$sql="SELECT idTrabajador FROM contadorPersona";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$ids = $stmt->fetchAll(PDO::FETCH_OBJ); //Saco todas las personas que fueron votadas
		
		$total = count($ids);

		for($i = 0;$i < $total; $i++){
			$id = $ids[$i]->idTrabajador; // Tengo el id del que fue votado
			/************ ASCENDENCIA ************
			$cont = calculateInd($id,$dbh,1,1);
			$sql = "UPDATE contadorPersona SET ascendenciaInd = ascendenciaInd + ".$cont." WHERE idTrabajador = ".$id."";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			
			$sql = "UPDATE contadorPersona SET totalIndirecto = totalIndirecto + ".$cont." WHERE idTrabajador = ".$id."";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			
			$sql = "UPDATE contadorPersona SET ascendenciaIndAmp = ascendenciaIndAmp + ".$cont." WHERE idTrabajador = ".$id."";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			/************ AFINIDAD ************
			$cont = calculateInd($id,$dbh,2,1);
			$sql = "UPDATE contadorPersona SET afinidadInd = afinidadInd + ".$cont." WHERE idTrabajador = ".$id."";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			
			$sql = "UPDATE contadorPersona SET totalIndirecto = totalIndirecto + ".$cont." WHERE idTrabajador = ".$id."";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			
			$sql = "UPDATE contadorPersona SET afinidadIndAmp = afinidadIndAmp + ".$cont." WHERE idTrabajador = ".$id."";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			/************ POPULARIDAD ************
			$cont = calculateInd($id,$dbh,3,1);
			$sql = "UPDATE contadorPersona SET popularidadInd = popularidadInd + ".$cont." WHERE idTrabajador = ".$id."";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			
			$sql = "UPDATE contadorPersona SET totalIndirecto = totalIndirecto + ".$cont." WHERE idTrabajador = ".$id."";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			
			$sql = "UPDATE contadorPersona SET popularidadIndAmp = popularidadIndAmp + ".$cont." WHERE idTrabajador = ".$id."";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
		}
		*/
		
		//Ingreso información de analisis

		$endtime = microtime(true);
		$_SESSION['time']+= $endtime - $starttime;
		$bateo = (100*$_SESSION['found'])/($_SESSION['total']-$_SESSION['empty']-$_SESSION['himself']);
		$bateo = round($bateo, 2);
		$noRecognized = (($_SESSION['total']-$_SESSION['empty'])-$_SESSION['found'] -$_SESSION['himself']);
		$himself = $_SESSION['himself'];
		$dbh->beginTransaction();

		$sql = "USE `adminSociometria`";
      	$stmt = $dbh->prepare($sql);
      	$stmt->execute();



      	$sql = "UPDATE analisis SET estatus = ?, bateo = ?,totalEmpleados = ?,encuestasEnBlanco = ?, nombresReconocidos = ?,nombresNoReconocidos = ?,porSiMismo = ?,tiempoEjecucion = ? WHERE idAnalisis = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->execute(array(1,$bateo,$_SESSION['total']/9,$_SESSION['empty'],$_SESSION['found'],$noRecognized ,$himself,  $_SESSION['time'],$_SESSION['idAnalisis']));

      	
      	$dbh->commit();

      	$_SESSION['time'] = $_SESSION['found'] = $_SESSION['total'] =  $_SESSION['empty'] = $_SESSION['idAnalisis'] = $_SESSION['company'] = NULL;


		$dbh = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}

?>
