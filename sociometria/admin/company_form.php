<?
require '../headers/admin_header.php';

?>
<div class="jumbotron" align="center">
<img src="img/back.png" onmouseover="this.src='img/back2.png'" onmouseout="this.src='img/back.png'" onclick="view(0)" align="left">

<h3>Crear nueva empresa</h3><br>
    
    <b>Nombre de la empresa</b><br><br>
    <div class="control-group" id="inputControl0">
     	<input id="nameCompany" type="text" class="form-control"  maxlength="100" onblur="validateCompanyUser(this)" value="" placeholder="Ej. Matchpeople"><span id="inputHelp0" class="help-inline"></span>
    </div>
    
    <b>Usuario para ingresar al sistema de la empresa</b><br><br>
    <div class="control-group" id="inputControl1">
    	<input id="userCompany" type="text" class="form-control"  maxlength="30" onblur="validateCompanyUser(this)" value="" placeholder="Ej. adminMatchpeople"><span id="inputHelp1" class="help-inline"></span>
    </div>
     
    <b>Contraseña para ingresar al sistema de la empresa</b><br><br>
    <div class="control-group" id="inputControl2">
     	<input id="password" type="password" class="form-control"   maxlength="100" onblur="validatePass(this);" value="" placeholder="******"><span id="inputHelp2" class="help-inline" ></span>
    </div>

    <b>Repetir contraseña</b><br><br>
    <div class="control-group" id="inputControl3">
    	<input id="password2" type="password" class="form-control"  maxlength="100" onblur="validatePass(this);" value="" placeholder="******"><span id="inputHelp3" class="help-inline"></span>
    </div>
    <button class="btn btn-success btn-lg" onclick="createCompany()" class="btn btn-default">Crear</button>
</div>
