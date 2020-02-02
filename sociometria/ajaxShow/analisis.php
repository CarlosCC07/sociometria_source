<?php
	
    require "../bd/config.php"; //Incluyo la base de datos
	$typeVar = $_GET["type"]; //Afinidad, ascendencia, o popularidad
	$plant = $_GET["plant"];  //Planta
	$order = $_GET["orderBy"]; //Ordenamiento
	$quantityLimit = $_GET["limit"]; //Lmite
	$segment = $_GET["seg"]; //Segmento
	$extra = $_GET["value"];
	$extraString="";
	switch($extra){
		case 0:break;
		case 1:
		case 2:
		case 3:
		case 4:$extraString = "AND p.extra = ".$extra."";break;
	}

	$type = "";
	$orderBy="";
	switch ($typeVar){
		case 0:$type="ascendencia";break;
		case 1:$type="afinidad";break;
		case 2:$type="popularidad";break;
	}
	$plantString = "";
	$plantTitle="";

	if($plant != 10){
		$plantTitle = $_SESSION['plants'][$plant];
		$plantString="AND p.planta = ".$plant."";
	}else{
		$plantTitle = "Global";
		$plantString = "";
	}

	$limit = "";
	$segmentString = "";
	switch($quantityLimit){
	    case 0:$limit ="LIMIT 0 , 10";break;
	    case 1:$limit ="LIMIT 0 , 50";break;
	    case 2:$limit ="LIMIT 0 , 100";break;
	    case 3:$limit ="";break;//todos
	}

	switch($segment){
	    case 0:$segmentString ="";break;//todos
	    case 1:$segmentString ="AND p.tipoTrabajador = 1";break;
	    case 2:$segmentString ="AND p.tipoTrabajador = 0";break;
	}

	if($typeVar == 0){
	    if($order == 0){
		$orderBy = "ascendenciaDir";
		$typeTitle= "Ascendencia Directa";
	    } else {
		$orderBy = "ascendenciaInd";
		$typeTitle= "Ascendencia Indirecta";
	    }
	} elseif ($typeVar == 1) {
	    if($order == 0){
		$orderBy = "afinidadDir";
		$typeTitle= "Afinidad Directa";
	    } else {
		$orderBy = "afinidadInd";
		$typeTitle= "Afinidad Indirecta";
	    }
	} elseif ($typeVar == 2) {
	    if($order == 0){
		$orderBy = "popularidadDir";
		$typeTitle= "Popularidad Directa";
	    } else {
		$orderBy = "popularidadInd";
		$typeTitle= "Popularidad Indirecta";
	    }
	}

	try {
		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
		$sql="SELECT p.idTrabajador, p.extra, p.nombre, p.fechaIngreso, cp.".$type."Dir as dir, cp.".$type."Ind as ind,cp.".$type."Total as total FROM  contadorPersona cp INNER JOIN personas p ON p.idTrabajador = cp.idTrabajador ".$plantString." ".$extraString." ".$segmentString." ORDER BY cp.".$orderBy." DESC ".$limit."";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$info = $stmt->fetchAll(PDO::FETCH_OBJ); 			
		$total = count($info);
		$class = "";

		echo "<div class=\"row\">";
		echo "<div align=\"left\" id=\"ad\" class=\"span4\">";
		echo "<b>Cantidad de personas</b><br><br>";
		echo "<select name=\"".$typeVar."\" id=\"limit\" class=\"span4\"  onchange=\"changeTypeOrder(this,".$plant.")\">";
		echo "<option >10</option>";
		echo "<option >50</option>";
		echo "<option >100</option>";
		echo "<option >Todos</option>";
		echo "<select>";
		echo "</div>";

		echo "<div align=\"center\" id=\"ad\" class=\"span4\">";
		echo "<b>Segmento</b><br><br>";
		echo "<select id=\"seg\" name=\"".$typeVar."\" class=\"span4\" onchange=\"changeTypeOrder(this,".$plant.")\">";
		echo "<option >Todos</option>";
		echo "<option >Empleados</option>";
		echo "<option >Sindicalizados</option>";
		echo "<select>";
		echo "</div>";

		echo "<div align=\"right\" id=\"ad\" class=\"span4\">";
		echo "<b>Forma de ordenamiento</b><br><br>";
		echo "<select id=\"orderBy\" name=\"".$typeVar."\" class=\"span4\" onchange=\"changeTypeOrder(this,".$plant.")\">";
		echo "<option >Directo</option>";
		echo "<option >Indirecto</option>";
		echo "<select>";
		echo "</div>";

		echo "<div>";

		echo "<div id=\"infoDownload\" style=\"background-color:white;\">";
		echo "<h3 align=\"center\">".(ucfirst($type))." ".$plantTitle."</h3>";

		echo "<table class=\"table table-condensed table-bordered navbar \" style=\"text-align:center;\">";

		echo "<tr class=\"title\" style=\"background-color:rgb(65, 105, 225);\">";
		echo "<th rowspan=\"2\" >Ranking</th>";
		echo "<th rowspan=\"2\" >#</th>";
        echo "<th rowspan=\"2\" >Antiguedad</th>";
		echo "<th rowspan=\"2\" >Nombre</th>";
		echo "<th colspan=\"2\" >".(ucfirst($type))."</th>";
		echo "<th rowspan=\"2\">Total</th>";
		echo "</tr>";
		echo "<tr class=\"title\" style=\"background-color:rgb(65, 105, 225);color:white;\">";
		echo "<th>Dir.</th>";
		echo "<th>Ind.</th>";
		echo "</tr>";
		for($i=0;$i<$total;$i++){
		
                        $d1 = new DateTime($info[$i]->fechaIngreso);
                        $d2 = new DateTime(date('Y-m-d'));

                        $interval = $d2->diff($d1);
                        $years = $interval->y;
                        $meses = $interval->m;
                        $rank = $i+1;

			echo"<tr class=\"per".$info[$i]->extra." \" onclick=\"analisisPerson(".$info[$i]->idTrabajador.",".$typeVar.",".$plant.")\" >";
			echo "<td style=\"text-align:center;\">".$rank."</td>";
			echo "<td style=\"text-align:right;\">".$info[$i]->idTrabajador."</td>";

                        if($years == 1 && $meses == 1){
                            echo "<td style=\"text-align:center;\">".$years." a. - ".$meses." m.</td>";
                        }else if($years == 1 && $meses != 1){
                            echo "<td style=\"text-align:center;\">".$years." a. - ".$meses." m.</td>";
                        }else if($years != 1 && $meses == 1){
                            echo "<td style=\"text-align:center;\">".$years." a. - ".$meses." m.</td>";
                        }else{
                            echo "<td style=\"text-align:center;\">".$years." a. - ".$meses." m.</td>";
                        }

			echo "<td><a style=\"text-align:center;color:#000000\">".utf8_encode($info[$i]->nombre)."</a></td>";
			echo"<td style=\"text-align:center;\">".$info[$i]->dir."</td>";
			echo "<td style=\"text-align:center;\">".$info[$i]->ind."</td>";
			echo "<td style=\"text-align:center;\">".$info[$i]->total."</td>";
			echo "</tr>";
			
			
		}
		echo "</table>";
		
		echo "<br>";
	echo "<div class=\"row\">";
		echo "<table border=\"1\" align=\"right\">";
		
		foreach ($_SESSION['extra'] as $key => $value) {
			if($value != ""){
				$realKey = $key + 1;
				echo "<tr class=\"per".$realKey."\"><td>".$value."</td></tr>";
			}
		}	
		
		echo "</table>";				
	echo "</div>";
	echo "<br><br>";
	echo "</div>";
		echo "</div>";
	echo "<div class=\"row\" align=\"center\" id=\"ad\">";
	echo "<a  class=\"btn btn-primary\" onmouseover=\"printInfo()\" onclick=\"printContent()\"><i class=\"icon-print icon-white\" ></i> Imprimir</a>&nbsp;&nbsp;";
	echo "<a class=\"btn btn-primary\" onmouseover=\"printInfo()\" onclick=\"takeSnapShot()\"><i class=\"icon-download-alt icon-white\" ></i> Descargar</a>";
	echo "</div>";
	

		
		$dbh = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}

	
	
	
	
	

    ?>
