<?php
require '../headers/admin_header.php';
require '../bd/initialConfig.php';
try {
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $dbh->beginTransaction();
    
    $sql="SELECT * FROM registrosEmpresa ORDER BY fecha DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $companiesR = $stmt->fetchAll(PDO::FETCH_OBJ);

    $sql="SELECT * FROM registrosAnalisis ORDER BY fecha DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $analisisR = $stmt->fetchAll(PDO::FETCH_OBJ); 

    $sql="SELECT * FROM registrosUsuario ORDER BY fecha DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $usersR = $stmt->fetchAll(PDO::FETCH_OBJ);  
    
    $dbh->commit();
    
    $dbh = null;
} catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
}

?>

<div align="center">
    <p><h3>Registros</h3></p>
    <?php
        if(count($companiesR)){

    ?>
            <h4>Empresas</h4>
            <table class="table table-bordered" style="width:auto;">
                <tr class="title">
                    <th>Empresa afectada</th>
                    <th>Usuario activo</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                </tr>
    <?php
            	
                        foreach($companiesR as $key => $reg){
                            echo "<tr>";
                	        echo "<td>".$reg->nombreEmpresa."</td>";
                            echo "<td>".$reg->usuarioRegistro."</td>";
                	        echo "<td>".$reg->fecha."</td>";
                            echo "<td>".(($reg->tipo)?"Empresa creada":"Empresa eliminada")."</td>";
                	       echo "</tr>";
                        }
        }
    ?>
    	</table>
    <?php
        if(count($analisisR)){

    ?>
            <h4>Análisis</h4>
            <table class="table table-bordered" style="width:auto;">
                <tr class="title">
                    <th>Empresa afectada</th>
                    <th>Usuario activo</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                </tr>
    <?php
                    
                        foreach($analisisR as $key => $reg){
                            echo "<tr>";
                            echo "<td>".$reg->nombreAnalisis."</td>";
                            echo "<td>".$reg->usuarioRegistro."</td>";
                            echo "<td>".$reg->fecha."</td>";
                            switch($reg->tipo ){
                                case 2:echo "<td>Análisis borrado</td>";break;
                                case 1:echo "<td>Análisis creado</td>";break;
                                case 0:echo "<td>Análisis limpiado</td>";break;
                            }
                            echo "</tr>";
                        }
                    }
    ?>
             </table>

    <?php
        if(count($usersR)){?>
            <h4>Usuarios</h4>
            <table class="table table-bordered" style="width:auto;">
                <tr class="title">
                    <th>Usuario afectado</th>
                    <th>Usuario activo</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                </tr>
    <?php
                        foreach($usersR as $key => $reg){
                            echo "<tr>";
                            echo "<td>".$reg->usuario."</td>";
                            echo "<td>".$reg->usuarioRegistro."</td>";
                            echo "<td>".$reg->fecha."</td>";
                            echo "<td>".(($reg->tipo)?"Usuario creado":"Usuario borrado")."</td>";
                            echo "</tr>";
                        }
            }
    ?>
            </table>


</div>