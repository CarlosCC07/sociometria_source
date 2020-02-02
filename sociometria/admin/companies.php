<?php
require '../bd/initialConfig.php';
require '../headers/admin_header.php';

try {
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $dbh->beginTransaction();

   // $sql="SELECT e.idEmpresa,e.nombreEmpresa,e.fechaCreacion,r.usuarioRegistro FROM empresas e,registrosEmpresa r WHERE e.idEmpresa = r.idEmpresa";
    $sql="SELECT * FROM empresas e";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $companies = $stmt->fetchAll(PDO::FETCH_OBJ);

    $dbh->commit();

    $dbh = null;
} catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
}

?>

<div align="center">
    <h3>Empresas</h3>
    <table class="table table-bordered" >
        <tr class="title">
            <th>Nombre de la empresa</th>
            <th>Fecha de creación</th>
            <!--<th>Creado por</th>-->
            <th>Análisis</th>
            <th>Quitar empresa</th>
        </tr>
        <?php
        	if(count($companies)){
                foreach($companies as $key => $company){
                    if($company->idEmpresa != 1){
                        echo "<tr>";
                	    echo "<td>".$company->nombreEmpresa."</td>";
                        echo "<td>".$company->fechaCreacion."</td>";
                	 //   echo "<td>".$company->usuarioRegistro."</td>";
                        echo "<td><button type=\"button\" class=\"btn btn-primary\" onclick=\"view(20,".$company->idEmpresa.",'".$company->nombreEmpresa."')\"><i class=\"icon-zoom-in icon-white\"></i></button></td>"; 
                	    echo "<td><button type=\"button\" class=\"btn btn-danger\" onclick=\"deleteCompany(".$company->idEmpresa.",'".$company->nombreEmpresa."')\"><i class=\"icon-trash icon-white\"></i></button></td>";
                	    echo "</tr>";
                    }
                }
            }
        ?>
	</table>
    <p><button type="button" onclick="view(1)" class="btn btn-success"><i class="icon-plus icon-white" ></i>Nueva empresa</button></p>

</div>