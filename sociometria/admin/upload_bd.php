<?php
require '../headers/admin_header.php';

require '../bd/initialConfig.php';
require "../error.php";
try {
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    if(!isset($_GET['idCompany']))
        exitWithHttpError(400, 'Missing idCompany');

    if(!isset($_GET['nameCompany']))
        exitWithHttpError(400, 'Missing nameCompany');

    if(!isset($_GET['idAnalisis']))
        exitWithHttpError(400, 'Missing idAnalisis');

    $idCompany = $_GET['idCompany'];
    $nameCompany = $_GET['nameCompany'];
    $idAnalisis = $_GET['idAnalisis'];

    $dbh->beginTransaction();

    $sql="SELECT plantas,ano,extra FROM analisis WHERE idAnalisis = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($idAnalisis));
    $analisisAll = $stmt->fetchAll(PDO::FETCH_OBJ);

    $dbh->commit();

    $dbh = null;
} catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
}

?>
    <div class="jumbotron" align="center">
    <?php
        echo "<img src=\"img/back.png\" onmouseover=\"this.src='img/back2.png'\" onmouseout=\"this.src='img/back.png'\" onclick=\"view(20,".$idCompany.",'".$nameCompany."')\" align=\"left\">";
        echo "<h3>Subida BD de ".$nameCompany."</h3><br>";
        echo "<input type=\"hidden\" id=\"plants\" value=\"".$analisisAll[0]->plantas."\">";
        echo "<input type=\"hidden\" id=\"year\" value=\"".$analisisAll[0]->ano."\">";
        echo "<input type=\"hidden\" id=\"extra\" value=\"".$analisisAll[0]->extra."\">";

    ?>

        
        <b>BD de la encuesta (.csv)</b>
        <div class="control-group" id="inputControl4">
            <input type="file" id="file" name="file" class="form-control"  maxlength="100" onblur="validateBlank(this)"><span id="inputHelp4" class="help-inline"></span>
        </div>

        <b>BD personal (.csv)</b>
        <div class="control-group" id="inputControl5">
            <input type="file" id="file2" name="file2" class="form-control"  maxlength="100" onblur="validateBlank(this)"><span id="inputHelp5" class="help-inline"></span>
        </div>

        <?php echo "<button class=\"btn btn-primary btn-lg\" onclick=\"loadFiles(".$idCompany.",'".$nameCompany."',".$idAnalisis.",1)\">Subir información</button>"; ?>
    </div>

