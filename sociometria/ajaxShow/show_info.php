<?php
if(session_status() == PHP_SESSION_NONE) 
        session_start();

require '../bd/initialConfig.php';
require "../error.php";
try {
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if(!isset($_POST['idAnalisis']))
         exitWithHttpError(400, 'Missing idAnalisis');

    $id = $_POST['idAnalisis'];

    $dbh->beginTransaction();

    $sql="SELECT a.plantas,a.ano,e.nombreEmpresa,a.extra FROM analisis a ,empresas e WHERE a.idAnalisis = ? AND a.estatus = 1 AND a.idEmpresa = e.idEmpresa";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    $analisisAll = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $plants = $analisisAll[0]->plantas;
    $plants = explode(",",$plants);
    $_SESSION['company'] = str_replace(' ', '',$analisisAll[0]->nombreEmpresa.$analisisAll[0]->ano);
    $_SESSION['plants'] = explode(",",$analisisAll[0]->plantas);
    $_SESSION['extra'] = explode(",",$analisisAll[0]->extra);

    
    $dbh->commit();

    $dbh = null;
} catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
}

?>



<div class="span2" id="leftbar">     
            	<div class="well sidebar-nav">
                    <ul class="nav nav-list">
                        <li class="nav-header">Inicio</li>
                        <li ><a href="index.php" >Ir a inicio</a></li>

                        <li class="nav-header">Análisis Global</li>

                        <li id="o2"><a href="#" onclick="globalAnalisisShow(10,8)">Global</a></li>
                        <?php
                            for($i=0;$i<count($plants);$i++)
                                echo "<li><a href=\"#\" onclick=\"globalAnalisisShow(".$i.",8)\">".$plants[$i]."</a></li>";
                        ?>

                        <li class="nav-header">Análisis Ascendencia</li>

                        <li id="o5"><a href="#" onclick="analisis(0,10,2)">Global</a></li>
                         <?php
                            for($i=0;$i<count($plants);$i++)
                                echo "<li><a href=\"#\" onclick=\"analisis(0,".$i.",2)\">".$plants[$i]."</a></li>";
                        ?>

                        <li class="nav-header">Análisis Afinidad</li>

                        <li id="o8"><a href="#" onclick="analisis(1,10,2)">Global</a></li>
                         <?php
                            for($i=0;$i<count($plants);$i++){
                                echo "<li><a href=\"#\" onclick=\"analisis(1,".$i.",2)\">".$plants[$i]."</a></li>";
                            }

                        ?>

                        <li class="nav-header">Análisis Popularidad</li>

                        <li id="o11" ><a href="#" onclick="analisis(2,10,2)">Global</a></li>
                         <?php
                            for($i=0;$i<count($plants);$i++){
                                echo "<li><a href=\"#\" onclick=\"analisis(2,".$i.",2)\">".$plants[$i]."</a></li>";
                            }

                        ?>

                        <li class="nav-header">Extra</li>
                        <li id=""><a href="#" onclick="notFound()">Mostrar sin coincidencias</a></li>
                        <li><a href="#" id="help1" rel="popover" data-title="¡Ayuda!" data-content="Si tienes un problema al imprimir o descargar imagen, clic aquí." data-placement="right" onclick="help()" >Ayuda  <i class="icon-info-sign" ></i></a></li>
                    </ul>
               </div>
         	</div>
            <div class="span9" id="contentArea">      <!--Body content-->
            	<div align="center">
            		  <div id="charging"></div>
            		  <div id="result"><h3>Bienvenido al sistema de Análisis Sociométrico.</h3>
                            <h4 id="analisisName"></h4>
                  </div>
            		  <div id="result2"></div>
              	</div>
            	<br>
            </div> <!--content-->
