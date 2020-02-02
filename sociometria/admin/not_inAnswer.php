<?php
session_start();
require '../headers/admin_header.php';

require '../bd/initialConfig.php';
require "../error.php";

if(!isset($_GET["base"]))
  exitWithHttpError(400, 'Missing base');

if(!isset($_GET['idCompany']))
    exitWithHttpError(400, 'Missing idCompany');

if(!isset($_GET['nameCompany']))
    exitWithHttpError(400, 'Missing nameCompany');

if(!isset($_GET['idAnalisis']))
        exitWithHttpError(400, 'Missing idAnalisis');

$idCompany = $_GET['idCompany'];
$nameCompany = $_GET['nameCompany'];
$idAnalisis = $_GET['idAnalisis'];
$bd = $_GET["base"];
			
try {
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	/*echo "<div class=\"row\">";
	echo "<div align=\"left\" id=\"ad\" class=\"span6\">";
	echo "<b>Ordenar por: </b><br><br>";
	echo "<select id=\"order\" class=\"span4\" onchange=\"changeNotFoundOrder(this)\">";
	echo "<option>Nombre</option>";
	echo "<option>Repetidos</option>";
	echo "<select>";
	echo "</div>";*/
	$order = "nombreRepetido ASC";
	/*switch($_GET['order']){
		case 0:$order="nombreRepetido ASC";break;
		case 1:$order="cantidad DESC";break;
	}*/

    $dbh->beginTransaction();

    $sql="SELECT plantas FROM analisis WHERE idAnalisis = ? ";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($idAnalisis));
    $plants = $stmt->fetchAll(PDO::FETCH_OBJ); 
    $eachPlant = explode(",",$plants[0]->plantas); 


    $sql = "USE `".$bd."`";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

		
	$sql="SELECT nr.nombreRepetido, nr.planta,count(nr.nombreRepetido) as cantidad FROM nombresRepetidos nr,personas p WHERE p.idTrabajador = nr.idTrabajadorEncuesta GROUP BY nr.nombreRepetido,nr.planta ORDER BY ".$order;
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$info = $stmt->fetchAll(PDO::FETCH_OBJ); 			


	
	$dbh = null;
} catch(PDOException $e) {
	echo '{"error":{"text":'. $e->getMessage() .'}}';
}

?>
<div align="center">
    <?php
		echo "<img src=\"img/back.png\" onmouseover=\"this.src='img/back2.png'\" onmouseout=\"this.src='img/back.png'\" onclick=\"view(20,".$idCompany.",'".$nameCompany."')\" align=\"left\">";
    ?>
     <h3>Nombres votados que no se encontraron en la base de datos</h3>

    <table class="table table-bordered">
        <tr class="title">
            <th>Nombre no encontrado</th>
            <th>Planta</th>
            <th>Cantidad aparición</th>
        </tr>
        <?php
            if(count($info)){
                foreach($info as $key => $person){
                    $plantString = $eachPlant[$person->planta];

                    echo "<tr>";
                    echo "<td>".utf8_encode($person->nombreRepetido)."</td>";
                    echo "<td>".$plantString."</td>";
                    echo "<td>".$person->cantidad."</td>";
                    echo "</tr>";
                }
            }
        ?>
    </table>
</div>
