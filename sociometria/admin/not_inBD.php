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

    $sql="SELECT * FROM personasNoReconocidas";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $notInBd = $stmt->fetchAll(PDO::FETCH_OBJ); 

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
    <h3>Personas que no se encontraron en la base de datos</h3>
    <table class="table table-bordered">
        <tr class="title">
            <th>Folio</th>
            <th>Nombre</th>
            <th>Planta</th>
        </tr>
        <?php
            if(count($notInBd)){
                foreach($notInBd as $key => $person){
                    $plantString = $eachPlant[$person->Planta];

                    echo "<tr>";
                    echo "<td>".$person->folio."</td>";
                    echo "<td>".utf8_encode($person->Nombre)."</td>";
                    echo "<td>".$plantString."</td>";
                    echo "</tr>";
                }
            }
        ?>
    </table>
</div>