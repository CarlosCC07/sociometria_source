-- MySQL dump 10.13  Distrib 5.5.27, for Linux (i686)
--
-- Host: localhost    Database: adminSociometria
-- ------------------------------------------------------
-- Server version	5.5.27

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `analisis`
--

DROP TABLE IF EXISTS `analisis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analisis` (
  `idAnalisis` int(11) NOT NULL AUTO_INCREMENT,
  `idEmpresa` int(11) NOT NULL,
  `ano` year(4) NOT NULL,
  `nombreExtra` varchar(200) DEFAULT NULL,
  `bd` int(1) NOT NULL,
  `estatus` int(1) NOT NULL,
  `plantas` varchar(200) CHARACTER SET utf8 NOT NULL,
  `extra` varchar(200) NOT NULL,
  `ultimoAnalisis` datetime DEFAULT NULL,
  `bateo` double DEFAULT NULL,
  `totalEmpleados` int(6) DEFAULT NULL,
  `encuestasEnBlanco` int(6) DEFAULT NULL,
  `nombresReconocidos` int(6) DEFAULT NULL,
  `nombresNoReconocidos` int(6) DEFAULT NULL,
  `porSiMismo` int(6) DEFAULT NULL,
  `tiempoEjecucion` int(12) DEFAULT NULL,
  `usuarioRegistro` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`idAnalisis`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analisis`
--

LOCK TABLES `analisis` WRITE;
/*!40000 ALTER TABLE `analisis` DISABLE KEYS */;
INSERT INTO `analisis` VALUES (2,44,2013,NULL,1,1,'Santa Catarina,Jilotepec,Los Mochis,Deplayusa','Clave,,Directivos y Gerentes,ComitÃ© Sindical','2013-12-16 17:50:35',95.76,272,342,2012,89,5,12,'administrator'),(3,45,2013,NULL,1,1,'Ensamble,Componentes','Clave,Clave2,Directivos y Gerentes,ComitÃ© Sindical','2013-12-16 16:58:10',91.14,1605,1489,11763,1143,50,441,'administrator');
/*!40000 ALTER TABLE `analisis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empresas` (
  `idEmpresa` int(5) NOT NULL AUTO_INCREMENT,
  `nombreEmpresa` varchar(75) CHARACTER SET utf8 NOT NULL,
  `fechaCreacion` datetime NOT NULL,
  PRIMARY KEY (`idEmpresa`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,'Matchpeople','2013-10-10 00:00:00'),(44,'ADS Mexicana','2013-12-11 12:14:09'),(45,'Sisamex','2013-12-16 15:58:33');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrosAnalisis`
--

DROP TABLE IF EXISTS `registrosAnalisis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registrosAnalisis` (
  `idRegAna` int(5) NOT NULL AUTO_INCREMENT,
  `idAnalisis` int(5) NOT NULL,
  `nombreAnalisis` varchar(50) CHARACTER SET utf8 NOT NULL,
  `usuarioRegistro` varchar(50) CHARACTER SET utf8 NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo` int(1) NOT NULL,
  PRIMARY KEY (`idRegAna`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrosAnalisis`
--

LOCK TABLES `registrosAnalisis` WRITE;
/*!40000 ALTER TABLE `registrosAnalisis` DISABLE KEYS */;
INSERT INTO `registrosAnalisis` VALUES (88,0,'ADSMexicana2013','administrator','2013-12-16 15:08:31',1),(89,0,'Sisamex2013','administrator','2013-12-16 16:22:14',1),(90,3,'Sisamex2013','administrator','2013-12-16 16:51:08',0),(91,0,'Sisamex2013','administrator','2013-12-16 16:56:07',1),(92,2,'ADSMexicana2013','administrator','2013-12-16 17:31:38',0),(93,0,'ADSMexicana2013','administrator','2013-12-16 17:32:02',1);
/*!40000 ALTER TABLE `registrosAnalisis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrosEmpresa`
--

DROP TABLE IF EXISTS `registrosEmpresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registrosEmpresa` (
  `idRegEmp` int(5) NOT NULL AUTO_INCREMENT,
  `idEmpresa` int(5) NOT NULL,
  `nombreEmpresa` varchar(75) CHARACTER SET utf8 NOT NULL,
  `usuarioRegistro` varchar(50) CHARACTER SET utf8 NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo` int(1) NOT NULL,
  PRIMARY KEY (`idRegEmp`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrosEmpresa`
--

LOCK TABLES `registrosEmpresa` WRITE;
/*!40000 ALTER TABLE `registrosEmpresa` DISABLE KEYS */;
INSERT INTO `registrosEmpresa` VALUES (8,44,'ADS Mexicana','administrator','2013-12-11 12:14:09',1),(9,45,'Sisamex','administrator','2013-12-16 15:58:33',1);
/*!40000 ALTER TABLE `registrosEmpresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrosSesion`
--

DROP TABLE IF EXISTS `registrosSesion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registrosSesion` (
  `idRegSes` int(5) NOT NULL AUTO_INCREMENT,
  `usuarioRegistro` varchar(50) CHARACTER SET utf8 NOT NULL,
  `empresaRegistro` varchar(50) CHARACTER SET utf8 NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo` int(1) NOT NULL,
  PRIMARY KEY (`idRegSes`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrosSesion`
--

LOCK TABLES `registrosSesion` WRITE;
/*!40000 ALTER TABLE `registrosSesion` DISABLE KEYS */;
/*!40000 ALTER TABLE `registrosSesion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrosUsuario`
--

DROP TABLE IF EXISTS `registrosUsuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registrosUsuario` (
  `idRegUsr` int(5) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) CHARACTER SET utf8 NOT NULL,
  `usuarioRegistro` varchar(50) CHARACTER SET utf8 NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo` int(1) NOT NULL,
  PRIMARY KEY (`idRegUsr`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrosUsuario`
--

LOCK TABLES `registrosUsuario` WRITE;
/*!40000 ALTER TABLE `registrosUsuario` DISABLE KEYS */;
INSERT INTO `registrosUsuario` VALUES (8,'adminADS','administrator','2013-12-11 12:14:09',1),(9,'adminSisamex','administrator','2013-12-16 15:58:33',1);
/*!40000 ALTER TABLE `registrosUsuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `idUsuario` int(5) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) CHARACTER SET utf8 NOT NULL,
  `contrasena` varchar(50) CHARACTER SET utf8 NOT NULL,
  `fechaCreacion` datetime NOT NULL,
  `idEmpresa` int(5) NOT NULL,
  `permisos` int(1) NOT NULL,
  PRIMARY KEY (`idUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'administrator','b051edf0db16e6781ed1656baa902801','2013-10-10 00:00:00',1,1),(53,'adminADS','26f7dc8ab32f83e5b22053eebf2c51d2','2013-12-11 12:14:09',44,3),(54,'adminSisamex','60e5447a94e4f10cfd8313d848cfd3e5','2013-12-16 15:58:33',45,3);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-12-16 17:50:52
