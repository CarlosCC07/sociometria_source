<?php
	
    require "../bd/config.php"; //Incluyo la base de datos

	$name = $_GET["name"];
	$name = str_replace('ñ','n',$name);
	$name = str_replace('Ñ','N',$name);
	$plants = $_SESSION["plants"];
	
		
	try {
		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
		$sql="SELECT p.idTrabajador,p.nombre,p.departamento,p.planta FROM personas p WHERE p.nombre LIKE '%".$name."%' OR p.departamento LIKE '%".$name."%' OR p.idTrabajador LIKE '%".$name."%' ORDER BY nombre ASC";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$persons = $stmt->fetchAll(PDO::FETCH_OBJ); 
		$total = count($persons);			
		
		if($total){
			echo "<table class=\"table table-bordered\" style=\"text-align:center;\">";
			echo "<tr class=\"title\">";
			echo "<th>ID</th>";
			echo "<th>Nombre</th>";
			echo "<th>Departamento</th>";
			echo "<th>Planta</th></tr>";
			for($i=0;$i<$total;$i++){
				$plant = $plants[$persons[$i]->planta];
				echo "<tr>";
				echo "<td>".$persons[$i]->idTrabajador."</td>";
				echo "<td><a onclick=\"whichInfo(".$persons[$i]->idTrabajador.",'".utf8_encode($persons[$i]->nombre)."')\">".utf8_encode($persons[$i]->nombre)."</a></td>";
				echo "<td>".utf8_encode($persons[$i]->departamento)."</td>";
				echo "<td>".utf8_encode($plant)."</td>";
				echo "</tr>";		
			}
			echo "</table>";
			
		}else{
			echo "<b align=\"center\">Sin coincidencias de: ".utf8_encode($name)."</b>";
		}
		$dbh = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}


	
	

    ?>
