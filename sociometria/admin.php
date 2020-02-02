
<?
session_start();
require 'headers/admin_header.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Sociometría</title>
    <link href="../Css/reset.css" rel="stylesheet" type="text/css">
    <link href="../Css/PNE.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="css/admin.css" rel="stylesheet" media="all">
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/admin.js"></script>      
</head>

<body>
<div id="header">
  <div id="conenedorHeader">
    
    <div id="logo">
      <ul>
        <li>
          <div id="icono"><a href="admin.php" target="_self"><img src="../img/icono.jpg"></a></div>
        </li>
        <li>
          <div id="logomatch"><a href="admin.php" target="_self"><img src="../img/logoMatchpeople.png"></a></div>
        </li>
      </ul>
    </div>
    
    <div id="submenu" style="">
	    <div style="float:left; width:35%; text-align:left; margin:auto;">
	    	<h3 style="color:white;" align="center">Sociometría Administrador</h3>
	    </div>
	    <div style=" top:1%; float:right;">
        Sesión: <?php echo $_SESSION['user'];?><br> 
		    <a href="logout.php" style="color:white;"><i class="icon-off icon-white" ></i> Cerrar sesión</a>

	    </div>
	    
	    <div>
	    </div>
    </div>
    
  </div>
</div>
<!-- HEADER END *****************-->
<br>

<div class="container-fluid">
  <div class="row-fluid">
    <div style="width:130px" class="span1" id="leftbar">      <!--Sidebar content-->
    	<div class="well sidebar-nav">
          <ul class="nav nav-list">

    			  <li class="nav-header">MENÚ</li>
            <li><a href="#" onclick="view(0)">Empresas</a></li>
            <li><a href="#" onclick="view(30)">Usuarios</a></li>
            <?php
              if($_SESSION['user'] == 'administrator')
                echo "<li><a href=\"#\" onclick=\"view(40)\">Registros</a></li>"; ?>
            <li><a href="#" onclick="view(50)">Ayuda</a></li>
          </ul>
       </div>
 	</div>
    	
    
    <div class="span10" id="contentArea">      <!--Body content-->
    	<div align="center">
    		  <div id="charging"></div>
    		  <div id="result"><h3>Bienvenido al sistema Administrador de Análisis Sociométrico.</h3>
                            <h4>Para iniciar selecciona alguna opción del menú.</h4></div>
          <div id="result2"></div>
      	</div>
    	<br>
    </div> 
    </div>
    </div>

        
        
</div>



</body>
</html>
