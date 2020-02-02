<?php
require '../headers/admin_header.php';
require '../bd/initialConfig.php';
require "../error.php";

if(!isset($_GET['idAnalisis']))
    exitWithHttpError(400, 'Missing idAnalisis');

if(!isset($_GET['idCompany']))
    exitWithHttpError(400, 'Missing idCompany');

if(!isset($_GET['nameCompany']))
    exitWithHttpError(400, 'Missing nameCompany');

$idCompany = $_GET['idCompany'];
$nameCompany = $_GET['nameCompany'];
$idAnalisis = $_GET['idAnalisis'];
try {
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $dbh->beginTransaction();

    $sql="SELECT * FROM analisis WHERE idAnalisis = ? ";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($idAnalisis));
    $analisis = $stmt->fetchAll(PDO::FETCH_OBJ); 


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
    <h3>Información del Análisis</h3>
    <ul>
        <?php
        echo "<table class=\"table table-bordered\" style=\"width:auto;\">";
        echo "<tr><td>Total de encuestas</td><td>".$analisis[0]->totalEmpleados."</td></tr>";
        echo "<tr><td>Encuestas en blanco</td><td>".$analisis[0]->encuestasEnBlanco."</td></tr>";
        echo "<tr><td>Nombres Reconocidos</td><td>".$analisis[0]->nombresReconocidos."</td></tr>";
        echo "<tr><td>Nombres No Reconocidos</td><td>".$analisis[0]->nombresNoReconocidos."</td></tr>";
        echo "<tr><td>Votaron por si mismo</td><td>".$analisis[0]->porSiMismo."</td></tr>";
        echo "<tr><td>Porcentaje de bateo</td><td>".$analisis[0]->bateo."%</td></tr>";
        echo "<tr><td>Tiempo en segundos</td><td>".$analisis[0]->tiempoEjecucion."</td></tr>";
        echo "</table>";
        ?>
    </ul>
   
</div>