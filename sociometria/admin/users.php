<?php
session_start();
require '../bd/initialConfig.php';
require '../headers/admin_header.php';

try {
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql="SELECT u.usuario,u.idUsuario,e.nombreEmpresa,u.fechaCreacion,e.idEmpresa FROM usuarios u, empresas e WHERE u.idEmpresa = e.idEmpresa";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_OBJ); 
  
    $dbh = null;
} catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
}

?>

<div align="center">
    <h3>Usuarios</h3>
    <table class="table table-bordered">
        <tr class="title">
            <th>Usuario</th>
            <th>Empresa</th>
            <th>Fecha de creación</th>
            <th>Contraseña</th>
            <th>Quitar usuario</th>
        </tr>
        <?php
        	if(count($users)){
                foreach($users as $key => $user){
                    if($_SESSION['user'] == "administrator"){
                        echo "<tr>";
                	    echo "<td>".$user->usuario."</td>";
                        echo "<td>".$user->nombreEmpresa."</td>";
                	    echo "<td>".$user->fechaCreacion."</td>"; 
                        echo "<td><button type=\"button\" class=\"btn btn-primary\" onclick=\"view(32,0,0,0,0,".$user->idUsuario.",'".$user->usuario."')\"><i class=\"icon-pencil icon-white\"></i></button></td>";
                	    echo "<td>";
                        if($user->usuario != "administrator")
                            echo "<button type=\"button\" class=\"btn btn-danger\" onclick=\"deleteUser(".$user->idUsuario.",'".$user->usuario."')\"><i class=\"icon-trash icon-white\"></i></button>";
                        echo "</td>";
                	    echo "</tr>";
                    }else if($user->idUsuario != 1){
                        echo "<tr>";
                        echo "<td>".$user->usuario."</td>";
                        echo "<td>".$user->nombreEmpresa."</td>";
                        echo "<td>".$user->fechaCreacion."</td>"; 
                        echo "<td><button type=\"button\" class=\"btn btn-primary\" onclick=\"view(32,0,0,0,0,".$user->idUsuario.",'".$user->usuario."')\"><i class=\"icon-pencil icon-white\"></i></button></td>";
                        echo "<td>";
                        if($user->idEmpresa != 1)
                            echo "<button type=\"button\" class=\"btn btn-danger\" onclick=\"deleteUser(".$user->idUsuario.",'".$user->usuario."')\"><i class=\"icon-trash icon-white\"></i></button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
            }
        ?>
	</table>
    <p><button type="button" onclick="view(31)" class="btn btn-success"><i class="icon-plus icon-white" ></i>Nuevo usuario</button></p>

</div>