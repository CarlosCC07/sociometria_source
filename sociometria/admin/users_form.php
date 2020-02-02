<?php
session_start();
require '../headers/admin_header.php';


require '../bd/initialConfig.php';
try {
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

    <img src="img/back.png" onmouseover="this.src='img/back2.png'" onmouseout="this.src='img/back.png'" onclick="view(30)" align="left">

    <h3>Crear nuevo usuario</h3>
    
    <b>Selecciona a cuál empresa va a pertenecer el usuario</b><br><br>
        <div class="control-group" id="inputControl0">
            <select id="companies">
                <?php
                    if(count($companies)){
                        foreach($companies as $key => $companie){
                            if($_SESSION['user'] =="administrator")
                                echo "<option value=\"".$companie->idEmpresa."\">".$companie->nombreEmpresa."</option>";
                            else if($companie->idEmpresa != 1)
                                     echo "<option value=\"".$companie->idEmpresa."\">".$companie->nombreEmpresa."</option>";

                        }
                    }
                ?>
            </select>
        </div>
    <input type="hidden">
    <b>Nombre de usuario</b><br><br>
    <div class="control-group" id="inputControl1">
    	<input id="userCompany" type="text" class="form-control"  maxlength="30" onblur="validateCompanyUser(this)" value="" placeholder="Ej. adminMatchpeople"><span id="inputHelp1" class="help-inline"></span>
    </div>
     
    <b>Contraseña para ingresar al sistema</b><br><br>
    <div class="control-group" id="inputControl2">
     	<input id="password" type="password" class="form-control"   maxlength="100" onblur="validatePass(this);" value="" placeholder="******"><span id="inputHelp2" class="help-inline"></span>
    </div>

    <b>Repetir contraseña</b><br><br>
    <div class="control-group" id="inputControl3">
    	<input id="password2" type="password" class="form-control"  maxlength="100" onblur="validatePass(this);" value="" placeholder="******"><span id="inputHelp3" class="help-inline"></span>
    </div>
    <button class="btn btn-success btn-lg" onclick="createUser()" class="btn btn-default">Crear</button>
</div>
