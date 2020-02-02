<?php
session_start();

if(empty($_SESSION['user']))
    header('Location:bienvenida.php');
else if($_SESSION['permissions'] == 3 || $_SESSION['permissions'] == 1)
        header('Location:admin.php');
      else{

        require "bd/initialConfig.php";	
try {
            $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $dbh->beginTransaction();

            $sql="SELECT  a.idAnalisis,a.plantas,a.ano,e.nombreEmpresa FROM analisis a,usuarios u,empresas e WHERE u.usuario = ? AND u.idEmpresa = e.idEmpresa AND a.estatus=1 AND e.idEmpresa = a.idEmpresa";
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array($_SESSION['user']));
            $analisisAll = $stmt->fetchAll(PDO::FETCH_OBJ);

            $dbh->commit();

            $dbh = null;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }



      }

?>
<html>
<head>
	<meta charset="UTF-8">
	<title>Sociometría</title>
     <link href="../Css/reset.css" rel="stylesheet" type="text/css">
     <link href="../Css/PNE.css" rel="stylesheet" type="text/css">
     <link href="css/class.css" rel="stylesheet" media="all">
     <link href="css/bootstrap.min.css" rel="stylesheet" media="all">
     <script src="js/jquery-1.10.2.min.js"></script>
     <script src="js/html2canvas.js"></script>
     <script src="js/bootstrap.min.js"></script>
     <script src="js/bootstrap-popover.js"></script>
     <script src="js/index.js"></script> 
</head>

<body>
    <div id="header">
        <div id="conenedorHeader">
            <div id="logo">
                <ul>
                    <li><div id="icono"><a href="index.php" target="_self"><img src="../img/icono.jpg"></a></div></li>
                    <li><div id="logomatch"><a href="index.php" target="_self"><img src="../img/logoMatchpeople.png"></a></div></li>
                </ul>
            </div>
            <div id="submenu" style="">
	            <div style="float:left; width:35%; text-align:left; margin:auto;">
	    	           <h3 style="color:white;" align="center">Sociometría</h3>
	            </div>
    	        <div style=" top:1%; float:right;">
                   <a href="logout.php" style="color:white;"><i class="icon-off icon-white" ></i> Cerrar sesión</a><br>
    		   	    <?php
                   // if(isset($_SESSION['company'])){
                ?>
                      <form class="navbar-search pull-right" align="" style="float:left">
          		   		  <i class="icon-search icon-white" ></i>
          		      	<input id="search"  onkeyup="searchName()" type="text" class="search-query" style="height: 20px; width:200px;" placeholder="Búsqueda de personas" align="right">
          		        </form>
                 <?php  //} ?>
    	       </div>
            </div>
        </div>
    </div> <!-- Header End -->

    <div class="container-fluid" style="position:relative; top:3% ;">
          <div class="row-fluid" id="index">
          
            <div class="span12" id="contentArea">      <!--Body content-->
            	<div align="center">
            		 <h3>Bienvenido al sistema de Análisis Sociométrico.</h3>
                            <h4>Para iniciar selecciona el análisis.</h4>
                            <?php
                              foreach ($analisisAll as $key => $value) 
                                echo "<button class=\"btn btn-primary\" onclick=\"showInfo(".$value->idAnalisis.",'".utf8_encode($value->nombreEmpresa)." ".$value->ano."')\">".utf8_encode($value->nombreEmpresa)." ".$value->ano."</button>";
                            ?>
               
              	</div>
                </div>
            </div> <!--content-->
    </div>

</body>
</html>
