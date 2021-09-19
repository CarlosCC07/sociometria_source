<?php
	session_start();

    require "../bd/config.php"; //Incluyo la base de datos
	 analisisGlobal();

	function analisisGlobal(){
		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
		$typeVar = $_GET["plant"];
		$order = $_GET["orderBy"];
		$quantityLimit = $_GET["limit"];
        $segment = $_GET["seg"];
		$extra = $_GET["value"];
		$extraString="";
		switch($extra){
			case 0:break;
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:$extraString = "AND p.extra = ".$extra."";break;
		}		

		
		//Forma de ordenamiento
	
		$plantString = "";
		$plantTitle= "";
		$typeTitle= "";
		$limit = "";
        $segmentString = "";
		switch($quantityLimit){
			case 0:$limit ="LIMIT 0 , 10";break;
			case 1:$limit ="LIMIT 0 , 50";break;
			case 2:$limit ="LIMIT 0 , 100";break;
			case 3:$limit ="";break;//todos

		}
		if($typeVar != 10){
			$plantTitle = $_SESSION['plants'][$typeVar];
			$plantString="AND p.planta = ".$typeVar."";
			$sql2="SELECT p.idTrabajador, p.tipoTrabajador FROM personas p WHERE p.planta = ".$typeVar."";
		}else{
			$plantTitle = "Global";
			$plantString="";
			$sql2="SELECT p.idTrabajador, p.tipoTrabajador FROM personas p";
		}
		
        switch($segment){
                case 0:$segmentString ="";break;//todos
                case 1:$segmentString ="AND p.tipoTrabajador = 1";break;
                case 2:$segmentString ="AND p.tipoTrabajador = 0";break;
        }
		
		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$stmt2 = $dbh->prepare($sql2);
			$stmt2->execute();
			$info_hc = $stmt2->fetchAll(PDO::FETCH_OBJ);
			$total_hc = count($info_hc);
			
			$orderBy="";
			switch($order){
				case 0:$orderBy="cp.ascendenciaDir DESC, cp.ascendenciaInd";$typeTitle="Ascendencia Directa";break;
				case 1:$orderBy="cp.ascendenciaInd DESC, cp.ascendenciaDir";$typeTitle="Ascendencia Indirecta";break;
				case 2:$orderBy="cp.afinidadDir DESC, afinidadInd";$typeTitle="Afinidad Directa";break;
				case 3:$orderBy="cp.afinidadInd DESC, afinidadDir";$typeTitle="Afinidad Indirecta";break;
				case 4:$orderBy="cp.popularidadDir DESC, popularidadInd";$typeTitle="Popularidad Directa";break;
				case 5:$orderBy="cp.popularidadInd DESC, popularidadDir";$typeTitle="Popularidad Indirecta";break;
				case 6:$orderBy="cp.totalDirecto DESC, cp.totalIndirecto";$typeTitle="Total Directo";break;
				case 7:$orderBy="cp.totalIndirecto DESC, cp.totalDirecto";$typeTitle="Total Indirecto";break;
				case 8:$orderBy="cp.total DESC, cp.totalDirecto DESC, cp.totalIndirecto";$typeTitle="Total";break;
				case 9:$orderBy="cp.totalAmp";$typeTitle="Total Ampliado";break;
			}

			if($extra == 0){
				$sql="SELECT p.idTrabajador, p.extra, p.tipoTrabajador, p.nombre, p.fechaIngreso, p.bidr, cp.ascendenciaDir,cp.ascendenciaInd,cp.afinidadDir, cp.afinidadInd, cp.popularidadDir,cp.popularidadInd,cp.totalDirecto,cp.totalIndirecto,cp.total,cp.totalAmp FROM contadorPersona cp, personas p WHERE p.idTrabajador = cp.idTrabajador ".$plantString." ".$extraString." ".$segmentString." ORDER BY ".$orderBy." DESC ".$limit.""; //LIMIT 0 , 10
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$info = $stmt->fetchAll(PDO::FETCH_OBJ); 			
				$total = count($info);
			} else {
				$sql="SELECT p.idTrabajador, p.extra, p.tipoTrabajador, p.nombre, p.fechaIngreso, p.bidr, cp.ascendenciaDir,cp.ascendenciaInd,cp.afinidadDir, cp.afinidadInd, cp.popularidadDir,cp.popularidadInd,cp.totalDirecto,cp.totalIndirecto,cp.total,cp.totalAmp FROM contadorPersona cp, personas p WHERE p.idTrabajador = cp.idTrabajador ".$plantString." ".$segmentString." ORDER BY ".$orderBy." DESC ";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$info = $stmt->fetchAll(PDO::FETCH_OBJ); 			
				$total = count($info);
			}
			
			$class = "";		
			
			echo "<div class=\"row\">";
			echo "<div align=\"left\" id=\"ad\" class=\"span4\">";
			echo "<b>Cantidad de personas</b><br><br>";
			echo "<select name=\"".$typeVar."\" id=\"limit\" class=\"span4\"  onchange=\"changeGlobalOrder(this)\">";
			echo "<option >10</option>";
			echo "<option >50</option>";
			echo "<option >100</option>";
			echo "<option >Todos</option>";
			echo "<select>";
			echo "</div>";

            echo "<div align=\"center\" id=\"ad\" class=\"span4\">";
            echo "<b>Segmento</b><br><br>";
            echo "<select id=\"seg\" name=\"".$typeVar."\" class=\"span4\" onchange=\"changeGlobalOrder(this)\">";
            echo "<option >Todos</option>";
            echo "<option >Empleados</option>";
            echo "<option >Sindicalizados</option>";
            echo "<select>";
            echo "</div>";

			echo "<div align=\"right\" id=\"ad\" class=\"span4\">";
			echo "<b>Forma de ordenamiento</b><br><br>";
			echo "<select id=\"orderBy\" name=\"".$typeVar."\" class=\"span4\" onchange=\"changeGlobalOrder(this)\">";
			echo "<option >Ascendencia Dir.</option>";
			echo "<option >Ascendencia Ind.</option>";
			echo "<option >Afinidad Dir.</option>";
			echo "<option >Afinidad Ind.</option>";
			echo "<option >Popularidad Dir.</option>";
			echo "<option >Popularidad Ind.</option>";
			echo "<option >Total Dir.</option>";
			echo "<option >Total Ind.</option>";
			echo "<option >Total Personas</option>";
			echo "<select>";
			echo "</div>";

			echo "<div>";
			
			echo "<div id=\"infoDownload\" style=\"background-color:white;\">";
			echo "<h3 align=\"center\">".$typeTitle." ".$plantTitle." </h3>";
			
			echo "<table class=\"table table-condensed table-bordered navbar\" style=\"text-align:center;\">";

			echo "<tr class=\"title\" style=\"background-color:rgb(65, 105, 225);color:white;\"><th rowspan=\"2\" style=\"text-align:center;\">RK</th><th rowspan=\"2\" style=\"text-align:center;\">#</th><th rowspan=\"2\" style=\"text-align:center;\">Antig.</th><th rowspan=\"2\" style=\"text-align:center;\">Nombre</th><th colspan=\"2\" style=\"text-align:center;\">Ascendencia</th><th colspan=\"2\" style=\"text-align:center;\">Afinidad</th><th colspan=\"2\" style=\"text-align:center;\">Popularidad</th><th colspan=\"2\" style=\"text-align:center;\">Total Menciones</th><th rowspan=\"2\" style=\"text-align:center;\">Total Pers. Dif.</th><th rowspan=\"2\" style=\"text-align:center;\"> % / HC</th></tr>";
			echo "<tr class=\"title\" style=\"background-color:rgb(65, 105, 225);color:white;\"><th style=\"text-align:center;\">Dir.</th><th style=\"text-align:center;\">IIX</th><th style=\"text-align:center;\">Dir.</th><th style=\"text-align:center;\">IIX</th><th style=\"text-align:center;\">Dir.</th><th style=\"text-align:center;\">IIX</th><th style=\"text-align:center;\">Dir.</th><th style=\"text-align:center;\">IIX</th></tr>";

			$seg_hc = 0;
			$emp_hc = 0;

			for($j=0;$j<$total_hc;$j++){
				if($info_hc[$j]->tipoTrabajador == 0){
					$seg_hc++;
				} else {
					$emp_hc++;
				}
			}
			
			for($i=0;$i<$total;$i++){
				
				$d1 = new DateTime($info[$i]->fechaIngreso);
				$d2 = new DateTime(date('Y-m-d'));
				$interval = $d2->diff($d1);
                $years = $interval->y;
				$meses = $interval->m;
				$rank = $i+1;

				if($extra == 0 || $extra == $info[$i]->extra){
					if($segment == 1) {
						$phc = ceil((($info[$i]->total)/$emp_hc)*100);
					} else if($segment == 2) {
						$phc = ceil((($info[$i]->total)/$seg_hc)*100);
					} else {
						$phc = ceil((($info[$i]->total)/$total_hc)*100);
					}

					echo "<tr class=\"per".$info[$i]->extra."\" onclick=\"\" >";

					if($extra == 0){
						if($info[$i]->tipoTrabajador == 0) {
							echo "<td style=\"text-align:center;font-weight:bold;color:red;\">".$rank."</td>";
						} else {
							echo "<td style=\"text-align:center;font-weight:bold;\">".$rank."</td>";
						}
					} else if($extra == $info[$i]->extra) {
						if($info[$i]->tipoTrabajador == 0) {
							echo "<td style=\"text-align:center;font-weight:bold;color:red;\">".$rank."</td>";
						} else {
							echo "<td style=\"text-align:center;font-weight:bold;\">".$rank."</td>";
						}
					}

					echo "<td style=\"text-align:right;\">".$info[$i]->idTrabajador."</td><td style=\"text-align:center;\">";

	                if($years == 1 && $meses == 1){
	                    echo $years."a-".$meses."m</td>";
	                }else if($years == 1 && $meses != 1){
	                    echo $years."a-".$meses."m</td>";
	                }else if($years != 1 && $meses == 1){
	                    echo $years."a-".$meses."m</td>";
	                }else{
	                    echo $years."a-".$meses."m</td>";
	                }

	                if($info[$i]->bidr == 0){
	                	 echo "<td ><a onclick=\"whichInfo(".$info[$i]->idTrabajador.",'".utf8_encode($info[$i]->nombre)."')\" style=\"color:black;\">".utf8_encode($info[$i]->nombre)."</a></td>";
	                } else {
	                	 echo "<td ><a onclick=\"whichInfo(".$info[$i]->idTrabajador.",'".utf8_encode($info[$i]->nombre)."')\" style=\"color:black;\"><b>".utf8_encode($info[$i]->nombre)."</b></a></td>";
	                }

					echo "<td style=\"text-align:center;\">".$info[$i]->ascendenciaDir."</td><td style=\"text-align:center;\">".$info[$i]->ascendenciaInd."</td>";
					echo "<td style=\"text-align:center;\">".$info[$i]->afinidadDir."</td><td style=\"text-align:center;\">".$info[$i]->afinidadInd."</td>";
					echo "<td style=\"text-align:center;\">".$info[$i]->popularidadDir."</td><td style=\"text-align:center;\">".$info[$i]->popularidadInd."</td>";
					echo "<td style=\"text-align:center;\">".$info[$i]->totalDirecto."</td><td style=\"text-align:center;\">".$info[$i]->totalIndirecto."</td>";
					echo "<td style=\"text-align:center;\">".$info[$i]->total."</td>";
					// Percentaje replaced totalAmp
					echo "<td style=\"text-align:center;\">".$phc."</td></tr>";
				}
				
			}
			echo "</table>";
			
			echo "<br>";
			

			echo "<div class=\"row\">";
				echo "<table border=\"1\" align=\"right\">";
				foreach ($_SESSION['extra'] as $key => $value) {
					if($value != ""){
						$realKey = $key + 1;
						echo "<tr class=\"per".$realKey."\"><td><a style=\"color:black;\" name=\"".$typeVar."\" onclick=\"changeGlobalOrder(this,".$realKey.")\">".$value."</a></td></tr>";
					}
				}				
				echo "</table>";				
			echo "</div>";
			echo "<br>";
			echo "</div>";
			
			echo "<div class=\"row\" align=\"center\" id=\"ad\">";
			echo "<a  class=\"btn btn-primary\" onmouseover=\"printInfo()\" onclick=\"printContent()\"><i class=\"icon-print icon-white\" ></i> Imprimir</a>&nbsp;&nbsp;";
			echo "<a class=\"btn btn-primary\" onmouseover=\"printInfo()\" onclick=\"takeSnapShot()\"><i class=\"icon-download-alt icon-white\" ></i> Descargar</a>";
			echo "</div>";
			
		
			
			$dbh = null;
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}

	}
	
	
	
	

    ?>
