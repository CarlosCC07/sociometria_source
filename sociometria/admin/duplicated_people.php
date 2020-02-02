<?php
require '../bd/initialConfig.php';
require "../error.php";
require '../headers/admin_header.php';

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
    
    $dbh->beginTransaction();

    $sql="SELECT plantas FROM analisis WHERE idAnalisis = ? ";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($idAnalisis));
    $plants = $stmt->fetchAll(PDO::FETCH_OBJ);
    $eachPlant = explode(",",$plants[0]->plantas); 

    $sql = "USE `".$bd."`";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $sql="SELECT * FROM personasDuplicadas";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $duplicated = $stmt->fetchAll(PDO::FETCH_OBJ); 

    $dbh->commit();
  
    $dbh = null;
} catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
}

?>

<div align="center">
     <?php
        echo "<img src=\"img/back.png\" onmouseover=\"this.src='img/back2.png'\" onmouseout=\"this.src='img/back.png'\" onclick=\"view(20,".$idCompany.",'".$nameCompany."')\" align=\"left\">";
    ?>
    <h3>Personas que se repitieron en encuestas</h3>
    <table class="table table-bordered">
        <tr class="title">
            <th>Nombre</th>
            <th>Folio Encuesta 1</th>
            <th>Planta 1</th>
            <th>Folio Encuesta 2</th>
            <th>Planta 2</th>
        </tr>
        <?php
        	if(count($duplicated)){
                foreach($duplicated as $key => $person){
                    $plantString1 = $eachPlant[$person->planta1];
                    $plantString2 = $eachPlant[$person->planta2];

                    echo "<tr>";
                	echo "<td>".utf8_encode($person->nombre)."</td>";
                    echo "<td>".$person->folioEncuesta1."</td>";
                	echo "<td>".$plantString1."</td>";
                    echo "<td>".$person->folioEncuesta2."</td>";
                    echo "<td>".$plantString2."</td>";
                	echo "</tr>";
                }
            }
        ?>
	</table>
</div>