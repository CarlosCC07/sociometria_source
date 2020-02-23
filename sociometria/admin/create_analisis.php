<?php
session_start();
require '../headers/admin_header.php';

function createFolders(){

  require "../error.php";

  if(!isset($_POST['company']))
      exitWithHttpError(400, 'Missing company');

  if(!isset($_POST['id']))
    exitWithHttpError(400, 'Missing id');

  if(!isset($_POST['plants']))
      exitWithHttpError(400, 'Missing plants');

  if(!isset($_POST['idAnalisis']))
      exitWithHttpError(400, 'Missing idAnalisis');

   if(!isset($_FILES["survey"]))
      exitWithHttpError(400, 'Missing survey');

   if(!isset($_FILES["employees"]))
      exitWithHttpError(400, 'Missing employees');

    if(!isset($_POST['extra']))
      exitWithHttpError(400, 'Missing extra');



  $dataOK = 0;
  $allowedExts = array("csv");
  $mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');

  $plants = $_POST['plants'];
  $company = $_POST['company'];
  $idAnalisis = $_POST['idAnalisis'];
  $extra = $_POST['extra'];
  $id  = $_POST['id'];
  $year = explode("-",$_POST['year'])[0];
  $dbName= str_replace(' ', '', $company.$year); 
  $urlNewCompany = "../input/".$dbName."/";

  $temp = explode(".", $_FILES["survey"]["name"]);
  $extension = end($temp);

  if (in_array($extension, $allowedExts)){
    if ($_FILES["survey"]["error"] > 0){
      $dataOK = 1;
    }
    else{
      if(!is_dir($urlNewCompany)){
        mkdir($urlNewCompany, 0777);
        mkdir($urlNewCompany.$year."/", 0777);
        mkdir($urlNewCompany.$year."/survey", 0777);
        mkdir($urlNewCompany.$year."/employees", 0777);
        mkdir($urlNewCompany.$year."/excel", 0777);
      }
      if(!move_uploaded_file($_FILES["survey"]["tmp_name"],$urlNewCompany.$year."/survey/inputSurvey.csv"))
        $dataOK = 2;
    }
  }
  else
    $dataOK = 3;
  

  $temp = explode(".", $_FILES["employees"]["name"]);
  $extension = end($temp);

  if (in_array($extension, $allowedExts)){
    if ($_FILES["employees"]["error"] > 0){
      $dataOK = 4;
    }
    else{
      if(!move_uploaded_file($_FILES["employees"]["tmp_name"],$urlNewCompany.$year."/employees/inputEmployees.csv"))
        $dataOK = 5;
    }
  }
  else
   $dataOK = 6;
  
  if(!$dataOK)
    createDatabase($dbName,$year,$plants,$id,$company,$idAnalisis,$extra);
  else{
    echo $dataOK; //Error al crear carpetas

  }
  
}
  
  /*Crear base de datos de la empresa*/

function createDatabase($dbName,$year,$plants,$id,$company,$idAnalisis,$extra){
  require "../bd/initialConfig.php"; 
  try {
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $dbh->beginTransaction();

    $sql = "CREATE DATABASE IF NOT EXISTS `".$dbName."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;USE `".$dbName."`;";

    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $sql = "CREATE TABLE IF NOT EXISTS `contadorPersona` (
          `idTrabajador` int(7) NOT NULL,
          `contAscendencia1` int(7) DEFAULT '0',
          `contAscendencia2` int(7) DEFAULT '0',
          `contAscendencia3` int(7) DEFAULT '0',
          `contAfinidad1` int(7) DEFAULT '0',
          `contAfinidad2` int(7) DEFAULT '0',
          `contAfinidad3` int(7) DEFAULT '0',
          `contPopularidad1` int(7) DEFAULT '0',
          `contPopularidad2` int(7) DEFAULT '0',
          `contPopularidad3` int(7) DEFAULT '0',
          `ascendenciaDir` int(3) DEFAULT '0',
          `ascendenciaInd` int(3) DEFAULT '0',
          `ascendenciaIndAmp` int(3) DEFAULT '0',
          `afinidadDir` int(3) DEFAULT '0',
          `afinidadInd` int(3) DEFAULT '0',
          `afinidadIndAmp` int(3) DEFAULT '0',
          `popularidadDir` int(3) DEFAULT '0',
          `popularidadInd` int(3) DEFAULT '0',
          `popularidadIndAmp` int(3) DEFAULT '0',
          `totalDirecto` int(3) DEFAULT '0',
          `totalIndirecto` int(3) DEFAULT '0',
          `total` int(3) DEFAULT '0',
          `totalAmp` int(3) DEFAULT '0',
          `ascendenciaTotal` int(4) DEFAULT '0',
          `afinidadTotal` int(4) DEFAULT '0',
          `popularidadTotal` int(4) DEFAULT '0',
           PRIMARY KEY (`idTrabajador`)
         )ENGINE=MyISAM DEFAULT CHARSET=utf8;
        
         CREATE TABLE IF NOT EXISTS `encuestaPersona` (
          `idTrabajador` int(7) NOT NULL,
          `folioEncuesta` int(5) DEFAULT NULL,
          `planta` int(1) DEFAULT NULL,
          `idAscendencia1` int(7) DEFAULT '0',
          `idAscendencia2` int(7) DEFAULT '0',
          `idAscendencia3` int(7) DEFAULT '0',
          `idAfinidad1` int(7) DEFAULT '0',
          `idAfinidad2` int(7) DEFAULT '0',
          `idAfinidad3` int(7) DEFAULT '0',
          `idPopularidad1` int(7) DEFAULT '0',
          `idPopularidad2` int(7) DEFAULT '0',
          `idPopularidad3` int(7) DEFAULT '0',
           PRIMARY KEY (`idTrabajador`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        
        CREATE TABLE IF NOT EXISTS `nombresRepetidos` (
          `idTrabajadorEncuesta` int(7) NOT NULL,
          `planta` int(1) NOT NULL,
          `nombreRepetido` varchar(100) NOT NULL,
          `lugarPregunta` int(6) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        
        CREATE TABLE IF NOT EXISTS `personas` (
          `idTrabajador` int(7) NOT NULL,
          `nombre` varchar(100) NOT NULL,
          `fechaIngreso` varchar(10) NOT NULL,
          `departamento` varchar(80) NOT NULL,
          `tipoTrabajador` int(1) NOT NULL,
          `extra` int(1) NOT NULL,
          `planta` int(1) NOT NULL,
          `puesto` varchar(255) DEFAULT 'N/A',
          `bidr` int(1) DEFAULT '0',
           PRIMARY KEY (`idTrabajador`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

        CREATE TABLE IF NOT EXISTS `personasDuplicadas` (
          `folioEncuesta1` int(5) NOT NULL,
          `planta1` int(2) NOT NULL,
          `nombre` varchar(100) NOT NULL,
          `folioEncuesta2` int(5) NOT NULL,
          `planta2` int(2) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;



        CREATE TABLE IF NOT EXISTS `personasNoReconocidas` (
          `folio` int(5) NOT NULL,
          `Nombre` varchar(100) NOT NULL,
          `Planta` int(3) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $bdEmployeesFile = @fopen('../input/'.$dbName.'/'.$year.'/employees/inputEmployees.csv', "r");
    while(!feof($bdEmployeesFile)){
      $currentLine = fgets($bdEmployeesFile);
      $infoPerson = explode(",",$currentLine);
      $infoPerson[1] = trim(str_replace("  ", " ", $infoPerson[1]));
      if($infoPerson[0]){
        $sql = "INSERT INTO `personas` (`idTrabajador`, `nombre`, `fechaIngreso`, `departamento`, `tipoTrabajador`, `extra`, `planta`) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $dbh->prepare($sql);
        $stmt->execute($infoPerson);

        $sql = "INSERT INTO `contadorPersona` (`idTrabajador`) VALUES (?)";

        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($infoPerson[0]));

      }
    }
    
    if($stmt){
      $sql = "USE `adminSociometria`";
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      if(!$idAnalisis){
        $sql = "INSERT INTO `analisis` (`idEmpresa`,`ano`,`bd`,`estatus`,`plantas`,`extra`,`usuarioRegistro`) VALUES (?,?,?,?,?,?,?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($id,$year,1,0,$plants,$extra,$_SESSION['user']));

      }else{
        $sql = "UPDATE analisis SET idEmpresa = ?,ano = ?, bd = ?, estatus = ?, plantas = ?, usuarioRegistro = ? WHERE idAnalisis = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($id,$year,1,0,$plants,$_SESSION['user'],$idAnalisis));
      }

      date_default_timezone_set('America/Chicago');
      $initialDate = getdate();
      $date = $initialDate['year'].'-'.$initialDate['mon'].'-'.$initialDate['mday'].' '.$initialDate['hours'].':'.$initialDate['minutes'].':'.$initialDate['seconds'];
      $date = new DateTime($date);
      $date = date_format($date, 'Y-m-d H:i:s');

      $sql = "INSERT INTO registrosAnalisis(idAnalisis,nombreAnalisis,usuarioRegistro,fecha,tipo) VALUES (?,?,?,?,?)";
      $stmt = $dbh->prepare($sql);
      $stmt->execute(array(0,$dbName,$_SESSION['user'],$date,1));
     

    }

    if($stmt)
      echo $id.",".$company;
    else
      echo "0,end";


    $dbh->commit();
    $dbh = null;
  } 
  catch(PDOException $e) {
    echo '{"Error":{"text":'. $e->getMessage() .'}}';
  } 
}

createFolders();

?>


