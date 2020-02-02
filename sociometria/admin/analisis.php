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

    $idCompany = $_GET['idCompany'];
    $nameCompany = $_GET['nameCompany'];

    $dbh->beginTransaction();

    $sql="SELECT * FROM analisis WHERE idEmpresa = ? ORDER BY ano";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($idCompany));
    $analisisAll = $stmt->fetchAll(PDO::FETCH_OBJ);

    $dbh->commit();

    $dbh = null;
} catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
}

?>

<div align="center">
    <img src="img/back.png" onmouseover="this.src='img/back2.png'" onmouseout="this.src='img/back.png'" onclick="view(0)" align="left"><h3>Análisis de <?php echo $nameCompany?></h3>
    <table class="table table-bordered" style="width:auto;">
        <tr class="title">
            <th rowspan="2">Año</th>
            <th rowspan="2">Plantas</th>
            <th rowspan="2">Personal</th>
            <th rowspan="2">Usuario</th>
            <th rowspan="2">Fecha de último análisis</th>
            <th rowspan="2" >Información del análisis</th>
            <th rowspan="2\">Ver duplicados (Nombre encuesta)</th>
            <th rowspan="2\">Ver no reconocidos (Nombre encuesta)</th>
            <th rowspan="2\">Ver no reconocidos (Encuesta)</th>
            <th rowspan="2" >Base de datos</th>
            <th rowspan="2" >Estado analisis</th>
            <th rowspan="2">Limpiar</th>
            <th rowspan="2">Quitar análisis</th>
        </tr>
        <tr></tr>
        <?php
        	if(count($analisisAll)){
                foreach($analisisAll as $key => $analisis){
                    $plants = explode(',',$analisis->plantas);
                    $bd = str_replace(' ', '', $nameCompany.$analisis->ano);
                    $extra = explode(',',$analisis->extra);

                    echo "<tr>";
                    echo "<td>".$analisis->ano."</td>";
                	echo "<td>";
                    foreach($plants as $key2 => $value)
                        echo $value."<br>";
                    echo "</td>";
                    echo "<td>";
                        foreach($extra as $key2 => $value)
                            if($value!="")
                                echo $value."<br>"; 
                    echo "</td>";
                    echo "<td>".$analisis->usuarioRegistro."</td>";
                    echo "<td>".$analisis->ultimoAnalisis."</td>";

                    if($analisis->estatus == 1){
                        echo "<td><button type=\"button\" class=\"btn btn-primary\" onclick=\"view(26,".$idCompany.",'".$nameCompany."',".$analisis->idAnalisis.")\"><i class=\"icon-info-sign icon-white\"></i></button></td>";
                        echo "<td><button class=\"btn btn-primary\" onclick=\"view(23,".$idCompany.",'".$nameCompany."',".$analisis->idAnalisis.",'".$bd."')\" ><i class=\"icon-user icon-white\"></i><i class=\"icon-user icon-white\"></i></button></td>";
                        echo "<td><button class=\"btn btn-primary\" onclick=\"view(24,".$idCompany.",'".$nameCompany."',".$analisis->idAnalisis.",'".$bd."')\" ><i class=\"icon-eye-close icon-white\"></i></button></td>";
                        echo "<td><button type=\"button\" class=\"btn btn-primary\" onclick=\"view(25,".$idCompany.",'".$nameCompany."',".$analisis->idAnalisis.",'".$bd."')\"><i  class=\"icon-eye-open icon-white\" ></i></button></td>";

                    }else{
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                    }
                    echo "<td class=\"estatus".$analisis->bd."\">";
                        if($analisis->bd == 0)
                            echo "<button type=\"button\" class=\"btn btn-primary\" onclick=\"view(22,".$idCompany.",'".$nameCompany."',".$analisis->idAnalisis.")\"><i class=\"icon-upload icon-white\" ></i></button>";
                    echo "</td>";
                    echo "<td class=\"estatus".$analisis->estatus."\">";
                        if($analisis->estatus == 0 && $analisis->bd == 1)
                            echo "<button type=\"button\" class=\"btn btn-primary\" onclick=\"init('".$bd."',".$analisis->ano.",".$analisis->idAnalisis.",".$idCompany.",'".$nameCompany."')\"><i class=\"icon-refresh icon-white\" ></i></button>";
                    echo "</td>";

                    if($analisis->bd != 0)
                        echo "<td><button class=\"btn btn-warning\" onclick=\"cleanAnalisis(".$analisis->idAnalisis.",'".$bd."',".$idCompany.",'".$nameCompany."',".$analisis->ano.")\"><i class=\"icon-star-empty icon-white\"></i></button></td>";
                    else
                        echo "<td></td>";
                    echo "<td><button class=\"btn btn-danger\" onclick=\"deleteAnalisis(".$analisis->idAnalisis.",".$idCompany.",'".$nameCompany."',".$analisis->bd.",".$analisis->ano.")\"><i class=\"icon-trash icon-white\"></i></button></td>"; 
                	echo "</tr>";
                }
            }
        ?>
	</table>
    <p>
        <div class="container-fluid">
            <div class="row-fluid">
                <?php echo "<div class=\"span6\" align=\"left\"><button type=\"button\" onclick=\"view(21,".$idCompany.",'".$nameCompany."')\" class=\"btn btn-success\"><i class=\"icon-plus icon-white\" ></i>Nuevo Análisis</button></div>" ?>
                <?php echo "<div class=\"span6\" align=\"right\"><button type=\"button\"  onclick=\"backup()\" class=\"btn btn-inverse\"><i class=\"icon-hdd icon-white\" ></i>Hacer respaldo</button></div>" ?>
            </div>
        </div>
    </p>
    <br><br>
    <p><b>*Recuerda hacer respaldo cuando tengas un análsis definitivo.</b></p>

</div>