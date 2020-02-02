<?php
	session_start();
	$_SESSION['company'] = $_POST['companyBD'];
	$_SESSION['idAnalisis'] = $_POST['idAnalisis'];
	$_SESSION['found'] = 0; //Encontrados
	$_SESSION['empty'] = 0; //Vacios
	$_SESSION['total'] = 0; //Totales
	$_SESSION['time'] = 0; //Tiempo
	$_SESSION['himself'] = 0;
	$repeatedIDS = array();

    require '../bd/config.php'; 
    require '../Math/Combinatorics.php';

	$starttime = microtime(true);

	function readData(){ 
		global $repeatedIDS;
		$file = @fopen("../input/".$_POST['companyBD']."/".$_POST['year']."/survey/inputSurvey.csv", "r") ;
		$_SESSION['total'] = count(file("../input/".$_POST['companyBD']."/".$_POST['year']."/survey/inputSurvey.csv")) * 9;
		saveAnalisisStatus();

		while (!feof($file)){ 
			$currentLine = fgets($file) ;
			$infoPerson = explode(",",$currentLine);
			$folioNumber = $infoPerson[0];
			$name = $infoPerson[1];
			$department = $infoPerson[2];
			$plant = $infoPerson[3];
			$idPerson = existPerson(cleanString($infoPerson[1],0),1);

			if($idPerson){  //Si la persona existe
				$personArray = existInBdSurvey($idPerson);
				if(!$personArray[0]){ // Si no existe la persona en la base de datos nueva, la guarda
					saveFolio($idPerson,$folioNumber,$plant);
					fillSurveyData($infoPerson,$idPerson);					
				}else{
					$_SESSION['empty']+=9;
					saveRepeatedName($folioNumber,$plant,$name,$personArray[0],$personArray[1]); //folio se intenta meter, nombre, planta, folio ya guardado,planta ya guardado		
				}
			}else{ 
				$_SESSION['empty']+=9;
				saveNoCoincidence($folioNumber,$name,$plant);
			}
		}
		fclose($file);
	}
	
	function fillSurveyData($infoPerson2,$idPerson2){
		$infoPerson = $infoPerson2;
		$idPerson = $idPerson2;

		$plant = $infoPerson[3];
		$department = getDepartment($idPerson);	
		for($i=4;$i<13;$i++){
			if(strlen(trim($infoPerson[$i]))>0){ 
				$id = existPerson(cleanString($infoPerson[$i],1),0);
				if($id && $id != $idPerson){  
					saveInfo($id,$i,$idPerson);
					$_SESSION['found']++;
				}else{
					$nameArray = explode(" ",cleanString($infoPerson[$i],1));
					$combinatorics = new Math_Combinatorics;
					$totalCombinations = count($nameArray);
					$j = $totalCombinations;
					for(;$j>=1;$j--){
						foreach($combinatorics->combinations($nameArray, $j) as $p){ 
							$chain = createChain($p,0); 
							$id = exist($chain,$department,0,1,$plant);
							if($id){									
								if($id != $idPerson){ 
									$_SESSION['found']++;
									saveInfo($id,$i,$idPerson);
									break;
								}else{
									$_SESSION['himself']++;
									break;
								}
							}	

						}
						if($id)
							break;

	
					}
							
					if(!$id){ 
						saveInfoAux($idPerson,$plant,cleanString($infoPerson[$i],1),($i-3));
					}
				}
					
			}else
				$_SESSION['empty']++;
		}

	}
	
	//Función crea la cadena para el query de cada nombre, parámetro nombre como arreglo
	function createChain($nameOrDepartment,$type){ //$
		$column = ($type)?"departamento":"nombre";		
		$totalArray = count($nameOrDepartment);
		$chain = "";
		$j = 0;
		foreach ($nameOrDepartment as $key => $value) {
				if($j == ($totalArray - 1)){
					$chain.= $aux = "MATCH ($column) AGAINST ('".$value."' IN BOOLEAN MODE)";
				}else{
					$chain.=  "MATCH ($column) AGAINST ('".$value."' IN BOOLEAN MODE) and ";
				}
				$j++;
		}		
		return $chain;
	}
		
	/*
	 *Función para buscar la existencia de una persona, recibe como parametros:
	 * $chain: cadena de busqueda para el query
	 * $departament: departamento de la persona que contesto la encuesta
	 * $case: sirve como indicador de si es la segunda busqueda
	 * $filter1: para activar el filtro por departamento
	 */
	function exist($chain,$department,$case,$filter1,$plant){
		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
		global $mas;
		
	    $sql = "";
		switch($case){
			case 0:$sql="SELECT p.idTrabajador,p.nombre FROM personas p WHERE ".$chain;break;
			case 1:$sql="SELECT p.idTrabajador,p.nombre FROM personas p,encuestaPersona ep WHERE ep.idTrabajador = p.idTrabajador AND ep.planta=".$plant." AND ".$chain;break;
			case 2:$sql="SELECT p.idTrabajador,p.nombre FROM personas p,encuestaPersona ep WHERE p.departamento LIKE '%$department%' AND ep.idTrabajador = p.idTrabajador AND ep.planta=".$plant." AND ".$chain;break;
			case 3:$sql="SELECT p.idTrabajador,p.nombre FROM personas p WHERE p.departamento LIKE '%$department%' AND ".$chain;break;
		}
				
		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$dbh->beginTransaction();
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$id = $stmt->fetchAll(PDO::FETCH_OBJ);

			$dbh->commit();

			$dbh = null;
			if(count($id) == 1){ //Se encontro el nombre que hace match perfectamente
					return $id[0]->idTrabajador;
			}else{ 
				if($filter1){
					switch($case){
						case 0:return 0 + exist($chain,$department,1,$filter1,$plant);break;
						case 1:return 0 + exist($chain,$department,2,$filter1,$plant);break;
						case 2:return 0 + exist($chain,$department,3,$filter1,$plant);break;
						case 3:return 0;break;
					}
				}
			}

		} catch(PDOException $e) {
			echo '{"Error":{"text":'. $e->getMessage() .'}}<br>';
		}
		
	}
	/*Función para buscar a una persona por su nombre tal y como se escribio
	 *$name: parametro del nombre de la persona
	 *$type: indicador de 1 si es la persona que hizo encuesta o 0 por la que voto
	*/
	function existPerson($name,$type){
		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
		global $mas;
		
		
		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$dbh->beginTransaction();

			$sql = "SELECT idTrabajador,nombre FROM personas p WHERE p.nombre LIKE ?";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($name));
			$id = $stmt->fetchAll(PDO::FETCH_OBJ);

			if(count($id)!=1){
				$nameArray = explode(" ",$name);
				$combinatorics2 = new Math_Combinatorics;
					$totalCombinations = count($nameArray);
					$j = $totalCombinations;
					for(;$j>=1;$j--){
						foreach($combinatorics2->combinations($nameArray, $j) as $p){ 
							$chain = createChain($p,0);
							$sql="SELECT idTrabajador,nombre FROM personas p WHERE ".$chain;
							$stmt = $dbh->prepare($sql);
							$stmt->execute();
							$id = $stmt->fetchAll(PDO::FETCH_OBJ);
							if(count($id)==1)
								break;
						}
						if(count($id)==1)
							break;	
					}
			}

			$dbh->commit();

			$dbh = null;

			if(count($id) == 1){
					return $id[0]->idTrabajador;

			}else{
					return 0;
			}
			
			
		} catch(PDOException $e) {
			echo '{"Error":{"text":'. $e->getMessage() .'}}';
		}

	}
	
	
	
	/*
	 *Funcion que recibe un id para ver si ya existe la encuesta de esa persona con ese ida
	 *$id: id de la persona que hizo la encuesta
	 */
	function existInBdSurvey($id){
		
		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
		global $mas;
		
		
		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$dbh->beginTransaction();

			$sql = "SELECT idTrabajador,folioEncuesta,planta FROM encuestaPersona cp WHERE cp.idTrabajador = '$id'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$data = $stmt->fetchAll(PDO::FETCH_OBJ);

			$dbh->commit();

			$dbh = null;

			$dataArray;			
			if(count($data) == 0){
				$dataArray[0] = 0;
				$dataArray[1] = 0;
				return $dataArray;
			}else{// si existe
			
				$dataArray[0] = $data[0]->folioEncuesta;
				$dataArray[1] = $data[0]->planta;
				return $dataArray;
			}
			
		} catch(PDOException $e) {
			echo '{"Error":{"text":'. $e->getMessage() .'}}';
		}
	}
	
	/*
	 *Funcion que recibe un nombre para remplazar caracteres que no se desean
	 *$name: nombre que recibe
	 *$advance: indica si se busca quitar articulo de los nombres, de,la,del,los
	 */
	function cleanString($name,$advance){
		$name = trim($name);
		$name= str_replace('.', '', $name);
		$name= str_replace('/', '', $name);
		$name= str_replace('(', '', $name);
		$name= str_replace(')', '', $name);
		$name= str_replace('-', '', $name);
		$name = str_replace(',','',$name);
		$name = str_replace('"','',$name);
		$name = str_replace('  ',' ',$name);
		$name = str_replace('   ',' ',$name);
		$name = str_replace('    ',' ',$name);
		
		$name = strtolower($name);
		
		$name = str_replace(' hdz ',' hernandez ',$name);
		$name = str_replace(' mtz ',' martinez ',$name);
		$name = str_replace(' gtz ',' gutierrez ',$name);
		$name = str_replace(' gles ',' gonzalez ',$name);
		$name = str_replace(' glez ',' gonzalez ',$name);
		$name = str_replace(' rdz ',' rodriguez ',$name);
		$name = str_replace(' jmz ',' jimenez ',$name);
		$name = str_replace(' fdz ',' fernandez ',$name);
		$name = str_replace(' hd ',' hernandez ',$name);
		$name = str_replace(' gpe ',' guadalupe ',$name);
		$name = str_replace(' mtto ',' mantenimiento ',$name);
		$name = str_replace(' lux ',' lubricantes ',$name);
		$name = str_replace(' chuy ',' jesus ',$name);
		$name = str_replace(' lalo ',' eduardo ',$name);
		$name = str_replace(' nacho ',' ignacio ',$name);
		$name = str_replace(' robert ',' roberto ',$name);
		$name = str_replace(' robert ',' roberto ',$name);
		$name = str_replace(' robertillo ',' roberto ',$name);
		$name = str_replace(' tavo ',' gustavo ',$name);
		$name = str_replace(' tono ',' antonio ',$name);
		$name = str_replace(' panchito ',' francisco ',$name);
		$name = str_replace(' pancho ',' francisco ',$name);
		$name = str_replace(' elizerio ',' eliserio ',$name);
		$name = str_replace(' alvares ',' alvarez ',$name);
		
		$name = str_replace(' hdz',' hernandez',$name);
		$name = str_replace(' mtz',' martinez',$name);
		$name = str_replace(' gtz',' gutierrez',$name);
		$name = str_replace(' gles',' gonzalez',$name);
		$name = str_replace(' glez',' gonzalez',$name);
		$name = str_replace(' rdz',' rodriguez',$name);
		$name = str_replace(' jmz',' jimenez',$name);
		$name = str_replace(' fdz',' fernandez',$name);
		$name = str_replace(' hd',' hernandez',$name);
		$name = str_replace(' gpe',' guadalupe',$name);
		$name = str_replace(' mtto',' mantenimiento',$name);
		$name = str_replace(' lux',' lubricantes',$name);
		$name = str_replace(' chuy',' jesus',$name);
		$name = str_replace(' lalo',' eduardo',$name);
		$name = str_replace(' nacho',' ignacio',$name);
		$name = str_replace(' robertillo',' roberto',$name);
		$name = str_replace(' tono',' antonio',$name);
		$name = str_replace(' panchito',' francisco',$name);
		$name = str_replace(' pancho',' francisco',$name);
		$name = str_replace(' elizerio',' eliserio',$name);
		$name = str_replace(' alvares',' alvarez',$name);
		
		$name = str_replace('hdz ','hernandez ',$name);
		$name = str_replace('mtz ','martinez ',$name);
		$name = str_replace('gtz ','gutierrez ',$name);
		$name = str_replace('gles ','gonzalez ',$name);
		$name = str_replace('glez ','gonzalez ',$name);
		$name = str_replace('rdz ','rodriguez ',$name);
		$name = str_replace('jmz ','jimenez ',$name);
		$name = str_replace('fdz ','fernandez ',$name);
		$name = str_replace('hd ','hernandez ',$name);
		$name = str_replace('gpe ','guadalupe ',$name);
		$name = str_replace('mtto ','mantenimiento ',$name);
		$name = str_replace('lux ','lubricantes ',$name);
		$name = str_replace('chuy ','jesus ',$name);
		$name = str_replace('lalo ','eduardo ',$name);
		$name = str_replace('nacho ','ignacio ',$name);
		$name = str_replace('robert ','roberto ',$name);
		$name = str_replace('robert ','roberto ',$name);
		$name = str_replace('robertillo ','roberto ',$name);
		$name = str_replace('tono ','antonio ',$name);
		$name = str_replace('panchito ','francisco ',$name);
		$name = str_replace('pancho ','francisco ',$name);
		$name = str_replace('elizerio ','eliserio ',$name);
		$name = str_replace('alvares ','alvarez ',$name);
		$name = str_replace('+','',$name);
		
		
		
			
		if($advance){
			$name = str_replace(' de ',' ',$name);
			$name = str_replace(' DE ',' ',$name);
			$name = str_replace(' De ',' ',$name);
			$name = str_replace(' LA ',' ',$name);
			$name = str_replace(' la ',' ',$name);
			$name = str_replace(' La ',' ',$name);
			$name = str_replace(' DEL ',' ',$name);
			$name = str_replace(' del ',' ',$name);
			$name = str_replace(' Del ',' ',$name);
			$name = str_replace(' Los ',' ',$name);
			$name = str_replace(' los ',' ',$name);
			$name = str_replace(' LOS ',' ',$name);
			$name = str_replace('Ing ','',$name);
			
		}
				
		return $name;
	}
	
	
	/*Función para guardar la informacion de una encuesta, id, folio, planta 
	 */

	function saveFolio($id,$folio,$plant){
		
		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
		global $mas;
	
		//$sql = "UPDATE encuestaPersona SET folioEncuesta = $folio,planta = $plant WHERE idTrabajador = $id";
		
		
		try {

			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$dbh->beginTransaction();

			$sql = "INSERT INTO encuestaPersona (idTrabajador, folioEncuesta, planta) VALUES ('$id','$folio','$plant')";

			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			$dbh->commit();

			$dbh = null;
	
		} catch(PDOException $e) {
			echo '{"Error":{"text":'. $e->getMessage() .'}}';
		}
		
	}
	
	
	function saveInfo($id, $i,$idPerson){

		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
		global $mas;

		$type = "";
		switch($i){
			case 4:$type = "idAscendencia1";break;
			case 5:$type = "idAscendencia2";break;
			case 6:$type = "idAscendencia3";break;
			case 7:$type = "idAfinidad1";break;
			case 8:$type = "idAfinidad2";break;
			case 9:$type = "idAfinidad3";break;
			case 10:$type = "idPopularidad1";break;
			case 11:$type = "idPopularidad2";break;
			case 12:$type = "idPopularidad3";break;
				
		}
			
		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$dbh->beginTransaction();

			$sql = "UPDATE encuestaPersona SET $type = $id WHERE idTrabajador = $idPerson";

			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			
			$dbh->commit();
			
			$dbh = null;
			
		} catch(PDOException $e) {
			echo '{"Error":{"text":'. $e->getMessage() .'}}';
		}
			
		
	}
	

	
	/*
	 *Función para guardar la información no encontrada del usuario
	 *
	*/
	
	function saveInfoAux($id,$plant,$name,$place){
	
	global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
		global $mas;
	
		
		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$dbh->beginTransaction();

			$sql = "INSERT INTO nombresRepetidos (idTrabajadorEncuesta,planta, nombreRepetido,lugarPregunta) VALUES ('$id','$plant','$name','$place')";

			$stmt = $dbh->prepare($sql);
			$stmt->execute();

			$dbh->commit();

			$dbh = null;
	
		} catch(PDOException $e) {
			echo '{"Error":{"text":'. $e->getMessage() .'}}';
		}
		
	}
	
	function searchDepartment($nameArray){
		$totalArray = count($nameArray);
		for($i=0;$i<$totalArray;$i++){
			// cada parte del nombre la tengo que buscar y ver si coincide con departamento si coincide, agarrar esa parte y usarla como departamento
		}
	}
	
	/* Función que regresa el departamento de una persona */
	
	function getDepartment($id){
		
		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
				
				
		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$dbh->beginTransaction();

			$sql = "SELECT departamento FROM personas WHERE idTrabajador = ".$id;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$dep = $stmt->fetchAll(PDO::FETCH_OBJ);

			$dbh->commit();

			$dbh = null;
		
			return $dep[0]->departamento;
										
			} catch(PDOException $e) {
				echo '{"Error":{"text":'. $e->getMessage() .'}}';
			}
		
	}

	function saveAnalisisStatus(){

		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;

		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			date_default_timezone_set('America/Chicago');
			$initialDate = getdate();
			$date = $initialDate['year'].'-'.$initialDate['mon'].'-'.$initialDate['mday'].' '.$initialDate['hours'].':'.$initialDate['minutes'].':'.$initialDate['seconds'];
			$date = new DateTime($date);
			$date = date_format($date, 'Y-m-d H:i:s');
			
			$dbh->beginTransaction();

			$sql = "USE `adminSociometria`";
      		$stmt = $dbh->prepare($sql);
      		$stmt->execute();
			
			$sql = "UPDATE analisis SET estatus = ?,ultimoAnalisis = ? WHERE idAnalisis = ?";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array(2,$date,$_SESSION['idAnalisis']));
			
			$dbh->commit();

			$dbh = null;
												
			} catch(PDOException $e) {
				echo '{"Error":{"text":'. $e->getMessage() .'}}';
			}

	}

	function saveRepeatedName($folioNumber,$plant,$name,$existentFolio,$existentPlant){

		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;

		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$dbh->beginTransaction();
			
			$sql = "INSERT INTO personasDuplicadas (folioEncuesta1,planta1, nombre,folioEncuesta2,planta2) VALUES (?,?,?,?,?)";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($folioNumber,$plant,$name,$existentFolio,$existentPlant));
			
			$dbh->commit();

			$dbh = null;
												
			} catch(PDOException $e) {
				echo '{"Error":{"text":'. $e->getMessage() .'}}';
			}

	}

	function saveNoCoincidence($folioNumber,$name,$plant){

		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;

		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$dbh->beginTransaction();
			
			$sql = "INSERT INTO personasNoReconocidas (folio,nombre, planta) VALUES (?,?,?)";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($folioNumber,$name,$plant));
			
			$dbh->commit();

			$dbh = null;
												
			} catch(PDOException $e) {
				echo '{"Error":{"text":'. $e->getMessage() .'}}';
			}

	}	
	
	readData();
	
	$endtime = microtime(true);
	$_SESSION['time'] = $endtime - $starttime;    
    ?>
