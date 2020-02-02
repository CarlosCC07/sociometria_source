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
			

			echo "<div class=\"row\">";
			echo "<div align=\"left\" id=\"ad\" class=\"span6\">";
			echo "<b>Ordenar por: </b><br><br>";
			echo "<select id=\"order\" class=\"span4\" onchange=\"changeNotFoundOrder(this)\">";
			echo "<option>Nombre</option>";
			echo "<option>Repetidos</option>";
			echo "<select>";
			echo "</div>";
			$order = "";
			switch($_GET['order']){
				case 0:$order="nombreRepetido ASC";break;
				case 1:$order="cantidad DESC";break;
			}

			$plants = $_SESSION['plants'];

			$sql="SELECT nr.nombreRepetido, nr.planta,count(nr.nombreRepetido) as cantidad FROM nombresRepetidos nr,personas p WHERE p.idTrabajador = nr.idTrabajadorEncuesta GROUP BY nr.nombreRepetido,nr.planta ORDER BY ".$order;
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$info = $stmt->fetchAll(PDO::FETCH_OBJ); 			
			$total = count($info);
			$plant;

			echo "<table class=\"table table-bordered\" style=\"text-align:center;\">";
			
			
			echo "<tr class=\"title\">";
			echo "<th style=\"text-align:center;\">Nombre no encontrado</th>";
			echo "<th style=\"text-align:center;\">Planta</th>";
			echo "<th style=\"text-align:center;\">Cantidad aparición</th>";
			echo "</tr>";
			for($i=0;$i<$total;$i++){						
				echo"<tr>";
				echo "<td>".$info[$i]->nombreRepetido."</td>";
				echo "<td style=\"text-align:center;\">".$plants[$info[$i]->planta]."</td>";
				echo "<td style=\"text-align:center;\">".$info[$i]->cantidad."</td>";
				echo "</tr>";	
			}
			echo "</table>";
			
			$dbh = null;
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}

	}
	
		notFound();
	
	
	

    ?>
