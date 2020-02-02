<?
require '../headers/admin_header.php';

?><div class="jumbotron" align="center">
    <img src="img/back.png" onmouseover="this.src='img/back2.png'" onmouseout="this.src='img/back.png'" onclick="view(30)" align="left">
    <h3>Cambiar contraseña de <?php echo $_GET['user']; ?></h3>
    
    <b>Nueva contraseña para ingresar al sistema</b><br><br>
    <div class="control-group" id="inputControl2">
     	<input id="password" type="password" class="form-control"   maxlength="100" onblur="validatePass(this);" value="" placeholder="******"><span id="inputHelp2" class="help-inline"></span>
    </div>

    <b>Repetir nueva contraseña</b><br><br>
    <div class="control-group" id="inputControl3">
    	<input id="password2" type="password" class="form-control"  maxlength="100" onblur="validatePass(this);" value="" placeholder="******"><span id="inputHelp3" class="help-inline"></span>
    </div>
   <?php echo "<button class=\"btn btn-success btn-lg\" onclick=\"changePassword(".$_GET['idUser'].")\" class=\"btn btn-default\">Cambiar</button>";?>
</div>
