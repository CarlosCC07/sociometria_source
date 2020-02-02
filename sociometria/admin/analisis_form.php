<?php
require '../headers/admin_header.php';
require '../bd/initialConfig.php';
require "../error.php";
try
{   
    if(!isset($_GET['idCompany']))
        exitWithHttpError(400, 'Missing idCompany');

    if(!isset($_GET['nameCompany']))
        exitWithHttpError(400, 'Missing nameCompany');

    $idCompany = $_GET['idCompany'];
    $nameCompany = $_GET['nameCompany'];

    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbh->beginTransaction();

    $sql="SELECT idEmpresa,nombreEmpresa FROM empresas";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $companies = $stmt->fetchAll(PDO::FETCH_OBJ); 

    $dbh->commit();
  
    $dbh = null;
} catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
}

?>
    <div class="jumbotron" align="center">
    <?php
        echo "<img src=\"img/back.png\" onmouseover=\"this.src='img/back2.png'\" onmouseout=\"this.src='img/back.png'\" onclick=\"view(20,".$idCompany.",'".$nameCompany."')\" align=\"left\">";
        echo "<h3>Nuevo Análisis de ".$nameCompany."</h3><br>";
    ?>
        <b>Por favor introduce los nombres de las plantas separados por comas (,)</b><br><br>
        <div class="control-group" id="inputControl2">
            <input type="text" class="form-control" id="plants"  maxlength="100" onblur="validatePlants(this)" placeholder="Ej. Monterrey,San Pedro"><span id="inputHelp2" class="help-inline"></span>
        </div>

        <b>Indica el año al que corresponde</b><br><br>
        <div class="control-group" id="inputControl3">
        	<?php echo "<input type=\"month\" class=\"form-control\" id=\"year\" maxlength=\"100\" onblur=\"validateAnalisisYear(this,'".$nameCompany."')\" placeholder=\"Ej. 2014\">"; ?>
            <span id="inputHelp3" class="help-inline"></span>
        </div>

        <b>BD de la encuesta (.csv)</b><br><br>
        <div class="control-group" id="inputControl4">
            <input type="file" id="file" name="file" class="form-control"  maxlength="100" onblur="validateBlank(this)"><span id="inputHelp4" class="help-inline"></span>
        </div>

        <b>BD personal (.csv)</b><br><br>
        <div class="control-group" id="inputControl5">
            <input type="file" id="file2" name="file2" class="form-control"  maxlength="100" onblur="validateBlank(this)"><span id="inputHelp5" class="help-inline"></span>
        </div>

        <b>Introduce los diferenciadores del personal</b><br><br>
       <div class="control-group" id="inputControl5">
            <input type="text" id="extra" name="extra" class="form-control"  maxlength="150" onblur="validatePersonal(this)" placeholder="Ej. Clave,Directivos y Gerentes"><span id="inputHelp6" class="help-inline"></span>
        </div>
        <div id="personalPreview" class="control-group">
        </div>
        <?php echo "<button class=\"btn btn-primary btn-lg\" onclick=\"loadFiles(".$idCompany.",'".$nameCompany."',0,0)\">Subir información</button>"; ?>

    </div>
