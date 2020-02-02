<?php
	session_start();
    require "../bd/config.php"; //Incluyo la base de datos
	
	function notFound(){
		global $dbuser;
		global $dbpass;
		global $dbname;
		global $dbhost;
			
		try {
			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$idPerson = $_GET['idPerson'];
			$typeNumber = $_GET['type'];
			$isII = $_GET['isII'];
			$type="";

			switch($typeNumber){
				case 0:$type="Ascendencia";break;
				case 1:$type="Afinidad";break;
				case 2:$type="Popularidad";break;

			}

			echo "<img src=\"img/back.png\" onmouseover=\"this.src='img/back2.png'\" onmouseout=\"this.src='img/back.png'\" align=\"left\" onclick=\"analisisPerson(".$idPerson.",".$typeNumber.")\">";
			
			switch ($isII) {
				case 0:echo "<h4>Personas que votaron Indirectamente de manera normal</h4>";break;
				case 1:echo "<h4>Personas que votaron Indirectamente de manera Ampliada</h4>";break;
			}
			
			

			echo "<table class=\"table table-bordered\" style=\"text-align:center;\">";
			
			echo "<tr class=\"title\">";
			echo "<th style=\"text-align:center;\">ID</th>";
			echo "<th style=\"text-align:center;\">Nombre</th>";
			echo "<th style=\"text-align:center;\">Directos</th>";
			echo "<th style=\"text-align:center;\">Indirectos</th>";
			echo "</tr>";

			$sql="SELECT idTrabajador FROM encuestaPersona ep WHERE id".$type."1 = ".$idPerson." OR id".$type."2 = ".$idPerson." OR id".$type."3 = ".$idPerson;
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$idsDir = $stmt->fetchAll(PDO::FETCH_OBJ);  //Directos
			$totalDirAux = count($idsDir);
			for($j=0;$j<$totalDirAux;$j++){
				switch($isII){
					case 0:$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE id".$type."1 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";break;
					case 1:$sql="SELECT DISTINCT idTrabajador FROM encuestaPersona WHERE id".$type."1 = ".$idsDir[$j]->idTrabajador." OR id".$type."2 = ".$idsDir[$j]->idTrabajador." OR id".$type."3 = ".$idsDir[$j]->idTrabajador." GROUP BY idTrabajador";break;
				}
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$idsInd = $stmt->fetchAll(PDO::FETCH_OBJ); //Ids de ascendencia 1
				
				foreach ($idsInd as $key => $value) {
					$sql = "SELECT p.nombre,p.idTrabajador,cp.totalDirecto,cp.totalIndirecto FROM personas p, contadorPersona cp WHERE cp.idTrabajador = p.idTrabajador AND p.idTrabajador = ? ORDER BY cp.totalDirecto";
					$stmt = $dbh->prepare($sql);
					$stmt->execute(array($value->idTrabajador));
					$info = $stmt->fetchAll(PDO::FETCH_OBJ);
					foreach ($info as $key2 => $value2) {
						echo "<tr>";
						echo "<td align=\"center\">".$value2->idTrabajador."</td>";
						echo "<td align=\"center\" ><a style=\"color:black;\" onclick=\"analisisPerson(".$value2->idTrabajador.",".$typeNumber.")\">".utf8_encode($value2->nombre)."</a></td>";
						echo "<td align=\"center\">".$value2->totalDirecto."</td>";
						echo "<td align=\"center\">".$value2->totalIndirecto."</td>";
						echo "</tr>";
					}

				}

						
			}

			echo "</table>";
			
		
			
			$dbh = null;
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}

	}
	
		notFound();
	
	
	

    ?>
