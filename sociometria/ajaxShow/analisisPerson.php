<?php
    require "../bd/config.php"; 
	$id = $_GET["idPerson"]; 
   	$typeVar = $_GET["type"]; 
   	$plantR = $_GET["plant"]; 
   	$plants = $_SESSION["plants"];
	$type = "";
	switch ($typeVar){
		case 0:$type="ascendencia";break;
		case 1:$type="afinidad";break;
		case 2:$type="popularidad";break;
		default: break;
	}
	
	try {
		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		echo "<div id=\"infoDownload\" style=\"background-color:white;\">";
		//echo "<img src=\"img/back.png\" onmouseover=\"this.src='img/back2.png'\" onmouseout=\"this.src='img/back.png'\" align=\"left\" onclick=\"analisis(".$typeVar.",".$plantR.")\">";
		echo "<h3 align=\"center\">".(ucfirst($type))."</h3><br><br>";
		
		
		//*************Información superior
		
		$left = -7;
		
		for($i=1;$i<=3;$i++){
			$sql="SELECT id".$type.$i." AS value FROM encuestaPersona WHERE idTrabajador = ".$id;
				
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$dataPerson = $stmt->fetchAll(PDO::FETCH_OBJ); //Saco todas las personas que fueron votadas
				
				if(!empty($dataPerson[0]->value)){
	
					$sql="SELECT p.idTrabajador,p.nombre,p.departamento,p.puesto,cp.".$type."Dir,cp.".$type."Ind FROM personas p, contadorPersona cp WHERE p.idTrabajador = cp.idTrabajador AND p.idTrabajador = ".$dataPerson[0]->value."";
				
					$stmt = $dbh->prepare($sql);
					$stmt->execute();
					$dataPerson = $stmt->fetchAll(PDO::FETCH_OBJ); //Saco todas las personas que fueron votadas
					
					echo "<table class=\"table-bordered\" width=\"25%\" height=\"8%\" style=\"display: inline-table;background-color:#4169E1!important;color:white!important;position:relative;left:".$left."%;font-size:11px;\">";
					echo "<tr><td colspan=\"2\" style=\"text-align:center;color:#4169E1;background-color:#FFFFFF;\">".$i."a. Opción</td></tr>";
					echo "<tr><td colspan=\"2\">".$dataPerson[0]->idTrabajador."</td></tr>";
					echo "<tr><td colspan=\"2\" style=\"text-align:center;border-top: none;\" ><a style=\"text-align:center;color:FFFFFF;\" onclick=\"analisisPerson(".$dataPerson[0]->idTrabajador.",".$_GET["type"].")\">".utf8_encode($dataPerson[0]->nombre)."</a></td></tr>";
				        echo "<tr><td colspan=\"2\" style=\"text-align:center;border-top: none;\" ><strong><a style=\"text-align:center;color:FFFFFF;\" onclick=\"analisisPerson(".$dataPerson[0]->idTrabajador.",".$_GET["type"].")\">".utf8_encode($dataPerson[0]->puesto)."</a></strong></td></tr>";
                                        echo "<tr><td colspan=\"2\" style=\"text-align:center;border-top: none;\" ><a style=\"text-align:center;color:FFFFFF;\" onclick=\"analisisPerson(".$dataPerson[0]->idTrabajador.",".$_GET["type"].")\">".utf8_encode($dataPerson[0]->departamento)."</a></td></tr>";
					switch ($_GET["type"]) {
						case 0:echo "<tr><td style=\"border-top: none;border-right:none;\">ID ".$dataPerson[0]->ascendenciaDir."</td><td style=\"text-align:right;border-top: none;border-left:none;\">IIX ".$dataPerson[0]->ascendenciaInd."</td></tr>";break;
						case 1:echo "<tr><td style=\"border-top: none;border-right:none;\">ID ".$dataPerson[0]->afinidadDir."</td><td style=\"text-align:right;border-top: none;border-left:none;\">IIX ".$dataPerson[0]->afinidadInd."</td></tr>";break;
						case 2:echo "<tr><td style=\"border-top: none;border-right:none;\">ID ".$dataPerson[0]->popularidadDir."</td><td style=\"text-align:right;border-top: none;border-left:none;\">IIX ".$dataPerson[0]->popularidadInd."</td></tr>";break;
					}
		
					echo "</table>";
				}else{
						$numberAnswer = ($typeVar * 3) + $i; //Saco el numero de pregunta en la tabla auxiliar
						$sql = "SELECT nombreRepetido FROM nombresRepetidos WHERE idTrabajadorEncuesta =".$id." AND lugarPregunta=".$numberAnswer;
						
						$stmt = $dbh->prepare($sql);
						$stmt->execute();
						$dataPerson = $stmt->fetchAll(PDO::FETCH_OBJ); //Saco todas las personas que fueron votadas
						
						$name = (empty($dataPerson[0]->nombreRepetido))?"SIN VOTO":strtoupper($dataPerson[0]->nombreRepetido);
						echo "<table class=\"table-bordered\" width=\"22%\" height=\"8%\" style=\"display: inline-table;background-color:#4169E1!important;color:white!important;position:relative;left:".$left."%;font-size:11px;\">";
						echo "<tr><td colspan=\"2\" style=\"text-align:center;color:#4169E1;background-color:#FFFFFF;\">".$i."a. Opción</td></tr>";
						echo "<tr><td colspan=\"2\">--</td></tr>";
						echo "<tr><td colspan=\"2\" style=\"text-align:center;border-top: none;color:FFFFFF;\" >".utf8_encode($name)."</td></tr>";
								
						echo "<tr><td style=\"border-top: none;border-right:none;\">ID --</td><td style=\"text-align:right;border-top: none;border-left:none;\">IIX --</td></tr>";

						echo "</table>";
						
						
						
				}
				
				$left+=7;
				
			
		}
		
	
		echo "<br><br>";
		
		echo "<table width=\"100%\" style=\"color:white;\">";
		echo "<tr><td width=\"33%\" align=\"center\"><img src=\"img/a1.png\"></td><td width=\"33%\" align=\"center\"><img src=\"img/a2.png\"></td><td width=\"33%\" align=\"center\"><img src=\"img/a3.png\"></td></tr>";
		echo "</table>";
		echo "<br>";
		
		//************ Creando la información central de la persona ***************
		
		$sql="SELECT p.idTrabajador,p.nombre,p.departamento,p.puesto,cp.".$type."Dir,cp.".$type."Ind,cp.".$type."IndAmp as amp,p.planta FROM personas p, contadorPersona cp WHERE p.idTrabajador = cp.idTrabajador AND p.idTrabajador = ".$id."";
		
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		
		$dataPerson = $stmt->fetchAll(PDO::FETCH_OBJ); 				
		$plant = $plants[$dataPerson[0]->planta];

                $idT = $dataPerson[0]->idTrabajador;
                $nT = $dataPerson[0]->nombre;
		
		echo "<table class=\"table table-condensed\" style=\"background-color:#4169E1!important;color:white;\">";
		echo "<tr><td>".$dataPerson[0]->idTrabajador."</td><td></td><td style=\"text-align:right;\"><a style=\"color:FFFFFF;\" onclick=\"showindirect(".$id.",".$_GET["type"].",1)\">IIA ".$dataPerson[0]->amp."</a></td></tr>";
		echo "<tr><td colspan=\"3\" style=\"text-align:center;border-top: none;\" ><b>".utf8_encode($dataPerson[0]->nombre)."</b></td></tr>";
		echo "<tr><td colspan=\"3\" style=\"text-align:center;border-top: none;\">".$dataPerson[0]->puesto."</td></tr>";
		echo "<tr><td colspan=\"3\" style=\"text-align:center;border-top: none;\">".utf8_encode($dataPerson[0]->departamento)."</td></tr>";
		
		switch ($_GET["type"]) {
			case 0:echo "<tr><td style=\"border-top: none;\">ID ".$dataPerson[0]->ascendenciaDir."</td><td style=\"border-top: none;\"></td><td style=\"text-align:right;border-top: none;\"><a style=\"color:FFFFFF;\" onclick=\"showindirect(".$id.",".$_GET["type"].",0)\">IIX ".$dataPerson[0]->ascendenciaInd."</a></td></tr>";break;
			
			case 1:echo "<tr><td style=\"border-top: none;\">ID ".$dataPerson[0]->afinidadDir."</td><td style=\"border-top: none;\"></td><td style=\"text-align:right;border-top: none;\"><a style=\"color:FFFFFF;\" onclick=\"showindirect(".$id.",".$_GET["type"].",0)\">IIX ".$dataPerson[0]->afinidadInd."</a></td></tr>";break;
			
			case 2:echo "<tr><td style=\"border-top: none;\">ID ".$dataPerson[0]->popularidadDir."</td><td style=\"border-top: none;\"></td><td style=\"text-align:right;border-top: none;\"><a style=\"color:FFFFFF;\" onclick=\"showindirect(".$id.",".$_GET["type"].",0)\">IIX ".$dataPerson[0]->popularidadInd."</a></td></tr>";break;
			
		}
		
		
		echo "</table>";
		
		// INFORMACION INFERIOR**********************
		
	
		echo "<table width=\"100%\" style=\"color:white;\">";
		echo "<tr><td width=\"33%\" align=\"center\"><img src=\"img/a1.png\"></td><td width=\"33%\" align=\"center\"><img src=\"img/a2.png\"></td><td width=\"33%\" align=\"center\"><img src=\"img/a3.png\"></td></tr>";
		echo "</table>";
		echo "<br>";
		
		    $left = -2;
			
			for($i=1;$i<=3;$i++){
				$sql="SELECT ep.idTrabajador,cp.".$type."Dir,cp.".$type."Ind FROM encuestaPersona ep,contadorPersona cp WHERE id".$type.$i." = ".$id." AND ep.idTrabajador = cp.idTrabajador ORDER BY cp.".$type."Dir DESC,cp.".$type."Ind DESC";
					
					$stmt = $dbh->prepare($sql);
					$stmt->execute();
					$dataPersons = $stmt->fetchAll(PDO::FETCH_OBJ); //Saco todas las personas que fueron votadas
					$total = count($dataPersons);
					echo "<table class=\"table-bordered\" width=\"30%\" height=\"8%\" style=\"display: inline-table;background-color:#4169E1!important;color:white;position:relative;left:".$left."%;font-size:11px;\">";
					echo "<tr><td colspan=\"4\" style=\"text-align:center;color:#4169E1;background-color:#FFFFFF;\">".$i."a. Opción</td></tr>";
					echo "<tr><td></td><td></td><td style=\"text-align:center;\">D</td><td style=\"text-align:center;\">I</td></tr>";
					if($total){
					
						for($j=0;$j<$total;$j++){	
							$sql="SELECT p.idTrabajador,p.nombre,cp.".$type."Dir as val1,cp.".$type."Ind as val2 FROM personas p, contadorPersona cp WHERE p.idTrabajador = cp.idTrabajador AND p.idTrabajador = ".$dataPersons[$j]->idTrabajador;
							$stmt = $dbh->prepare($sql);
							$stmt->execute();
							$dataPerson = $stmt->fetchAll(PDO::FETCH_OBJ);
							
							$class="";
							if($j<=2 && $dataPerson[0]->val1 != 0){
								 $class="important";
								  $color = "white";
							 }else{
								 $class = "";
								 $color = "white";
							}
			
							echo "<tr class=\"$class\" \"><td style=\"text-align:right;\">".$dataPerson[0]->idTrabajador."</td><td  style=\"text-align:center;\"><a style=\"text-align:center;color:".$color.";\" onclick=\"analisisPerson(".$dataPerson[0]->idTrabajador.",".$_GET["type"].")\">".utf8_encode($dataPerson[0]->nombre)."</a></td><td style=\"text-align:right;\">".$dataPerson[0]->val1."</td><td style=\"text-align:right;\">".$dataPerson[0]->val2."</td></tr>";
						 //Saco todas las personas que fueron votadas
					
						}
					
				
				}else{
					echo "<tr><td style=\"text-align:center;\">--</td><td style=\"text-align:center;color:FFFFFF;\">SIN VOTOS</td><td style=\"text-align:center;\">--</td><td style=\"text-align:center;\">--</td></tr>";
					
					
				}
				echo "</table>";
				$left+=2;
				
			}
			
		
		echo "</div>";
		echo "<br><br>";
		echo "<div class=\"row\" align=\"center\" id=\"ad\">";
                echo "<a  class=\"btn btn-primary\" onclick=\"whichInfo(".$idT.",'".utf8_encode($nT)."')\"><i class=\"icon-arrow-left icon-white\" ></i> Regresar</a>&nbsp;&nbsp;";
		echo "<a  class=\"btn btn-primary\" onmouseover=\"printInfo()\" onclick=\"printContent()\"><i class=\"icon-print icon-white\" ></i> Imprimir</a>&nbsp;&nbsp;";
		echo "<a class=\"btn btn-primary\" onmouseover=\"printInfo()\" onclick=\"takeSnapShot()\"><i class=\"icon-download-alt icon-white\" ></i> Descargar</a>";
		echo "</div>";
		//*******
		$dbh = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
		echo $sql;
	}
	

	
	


	
	

    ?>
