-- MySQL dump 10.13  Distrib 5.6.24, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: jcrseguros
-- ------------------------------------------------------
-- Server version	5.6.21

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
-- Table structure for table `acceso_opciones_rol`
--

DROP TABLE IF EXISTS `acceso_opciones_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acceso_opciones_rol` (
  `tipo_usuario_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `active_menu` int(11) DEFAULT NULL,
  PRIMARY KEY (`menu_id`,`tipo_usuario_id`),
  KEY `user_type_id_fk_idx_idx` (`tipo_usuario_id`),
  CONSTRAINT `menu_id_fk_idx` FOREIGN KEY (`menu_id`) REFERENCES `menu_aplicacion` (`menu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_type_id_fk_idx` FOREIGN KEY (`tipo_usuario_id`) REFERENCES `tipo_usuario` (`tipo_usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acceso_opciones_rol`
--

LOCK TABLES `acceso_opciones_rol` WRITE;
/*!40000 ALTER TABLE `acceso_opciones_rol` DISABLE KEYS */;
INSERT INTO `acceso_opciones_rol` VALUES (4,1,1),(4,2,1),(4,3,1),(4,4,1),(4,5,1),(4,6,1),(4,7,1);
/*!40000 ALTER TABLE `acceso_opciones_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aseguradora`
--

DROP TABLE IF EXISTS `aseguradora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aseguradora` (
  `aseguradora_id` int(11) NOT NULL AUTO_INCREMENT,
  `aseguradora_nombre` varchar(100) NOT NULL,
  `aseguradora_documento_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`aseguradora_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aseguradora`
--

LOCK TABLES `aseguradora` WRITE;
/*!40000 ALTER TABLE `aseguradora` DISABLE KEYS */;
INSERT INTO `aseguradora` VALUES (1,'Seguros Universitas C.A.',NULL),(2,'Seguros Piramide C.A.',NULL),(3,'Seguros Caracas de Liberty Mutual C.A.',NULL),(4,'Vivir Seguros C.A.',NULL),(5,'Estar Seguros S.A.',NULL),(6,'Universal de Seguros C.A.',NULL),(7,'Seguros La Occidental',NULL),(8,'La Venezolaa de Seguros y Vida C.A.',NULL),(9,'Oceanica de Seguros',NULL),(10,'C.A. de Seguros La Internacional',NULL);
/*!40000 ALTER TABLE `aseguradora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cobertura`
--

DROP TABLE IF EXISTS `cobertura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cobertura` (
  `cobertura_id` int(11) NOT NULL AUTO_INCREMENT,
  `cobertura_nombre` varchar(45) NOT NULL,
  `ramo_id` int(11) NOT NULL,
  `monto_cobertura` double NOT NULL,
  `monto_deducible` double NOT NULL,
  `monto_prima` double NOT NULL,
  PRIMARY KEY (`cobertura_id`),
  KEY `ramo_cobertura_fk_idx` (`ramo_id`),
  CONSTRAINT `ramo_cobertura_fk` FOREIGN KEY (`ramo_id`) REFERENCES `ramo` (`ramo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cobertura`
--

LOCK TABLES `cobertura` WRITE;
/*!40000 ALTER TABLE `cobertura` DISABLE KEYS */;
INSERT INTO `cobertura` VALUES (1,'Hospitalizacion Basica',1,150000,0,0),(2,'Exceso',1,0,0,0),(3,'Maternidad Basica',1,60000,0,0),(4,'Exceso Maternidad',1,0,0,0),(5,'Vida',1,0,0,0),(6,'Accidentes Personales',1,0,0,0),(7,'Funerario',1,0,0,0),(8,'Condiciones Extremas',1,0,0,0),(9,'Gastos Medicos Mayores',1,0,0,0),(10,'Cobertura Amplia',3,0,0,0),(11,'Perdida Total',3,0,0,0),(12,'RCV',3,0,0,0),(13,'Productos Especiales',3,0,0,0),(14,'APOV',3,0,0,0),(15,'Rotura de Vidrios',3,0,0,0),(16,'Incendio',2,0,0,0),(17,'Terremoto',2,0,0,0),(18,'Sustraccion Ilegitima',2,0,0,0),(19,'Transporte',2,0,0,0),(20,'Embarcacion',2,0,0,0),(21,'Aviacion',2,0,0,0),(22,'Ramos Tecnicos de Ingenieria',2,0,0,0),(23,'Lucro Cesante',2,0,0,0),(24,'Riesgos Especiales',2,0,0,0),(25,'Seguros de Credito a la Exportacion',2,0,0,0),(26,'General',4,0,0,0),(27,'Predios y Operaciones',4,0,0,0),(28,'Ante Vecino',4,0,0,0),(29,'Locativo',4,0,0,0),(30,'De Productos',4,0,0,0),(31,'Profesional',4,0,0,0),(32,'Patronal',4,0,0,0),(33,'Empresarial',4,0,0,0),(34,'Cruzada',4,0,0,0),(35,'Robo',2,0,0,0),(36,'Robo',3,0,0,0);
/*!40000 ALTER TABLE `cobertura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financiamientos`
--

DROP TABLE IF EXISTS `financiamientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financiamientos` (
  `financiamiento_id` int(11) NOT NULL AUTO_INCREMENT,
  `poliza_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `numero_cuotas` int(11) DEFAULT NULL,
  `monto_inicial` double DEFAULT NULL,
  `numero_Financiamiento` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`financiamiento_id`),
  KEY `financiamiento_usuario_fk_idx` (`usuario_id`),
  KEY `fiananciamiento_poliza_fk_idx` (`poliza_id`),
  CONSTRAINT `fiananciamiento_poliza_fk` FOREIGN KEY (`poliza_id`) REFERENCES `poliza` (`poliza_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `financiamiento_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financiamientos`
--

LOCK TABLES `financiamientos` WRITE;
/*!40000 ALTER TABLE `financiamientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `financiamientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_aplicacion`
--

DROP TABLE IF EXISTS `menu_aplicacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_aplicacion` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_menu` int(11) NOT NULL DEFAULT '1',
  `name_menu` varchar(45) NOT NULL,
  `icon_class` varchar(25) NOT NULL,
  `description` varchar(60) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_aplicacion`
--

LOCK TABLES `menu_aplicacion` WRITE;
/*!40000 ALTER TABLE `menu_aplicacion` DISABLE KEYS */;
INSERT INTO `menu_aplicacion` VALUES (1,1,'Usuarios','fa fa-bank','JCR Seguros',1),(2,1,'Polizas','fa fa-user','JCR Seguros',2),(3,1,'Vehiculos','fa fa-hdd-o','JCR Seguros',3),(4,1,'Siniestros','fa fa-map-signs','JCR Seguros',4),(5,1,'Coberturas','fa fa-street-view','JCR Seguros',5),(6,1,'Aseguradoras','fa fa-gear','JCR Seguros',6),(7,1,'Opciones Administrativas','fa fa-gear','JCR Seguros',7);
/*!40000 ALTER TABLE `menu_aplicacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poliza`
--

DROP TABLE IF EXISTS `poliza`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poliza` (
  `poliza_id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_poliza` varchar(45) NOT NULL,
  `ramo_id` int(11) NOT NULL,
  `usuario_id_tomador` int(11) NOT NULL,
  `usuario_id_titular` int(11) NOT NULL,
  `usuario_id_agente` int(11) NOT NULL,
  `aseguradora_id` int(11) NOT NULL,
  `munero_recibo` varchar(45) NOT NULL,
  `vegencia` date NOT NULL,
  `tipo_poliza_id` int(11) NOT NULL,
  `referencia` varchar(45) NOT NULL,
  `prima_total` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`poliza_id`),
  KEY `poliza_ramo_fk_idx` (`ramo_id`),
  KEY `tomador_poliza_fk_idx` (`usuario_id_tomador`),
  KEY `titular_poliza_fk_idx` (`usuario_id_titular`),
  KEY `agente_poliza_fk_idx` (`usuario_id_agente`),
  KEY `aseguradora_poliza_fk_idx` (`aseguradora_id`),
  KEY `poliza_tipo_poliza_fk_idx` (`tipo_poliza_id`),
  CONSTRAINT `agente_poliza_fk` FOREIGN KEY (`usuario_id_agente`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `aseguradora_poliza_fk` FOREIGN KEY (`aseguradora_id`) REFERENCES `aseguradora` (`aseguradora_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `poliza_ramo_fk` FOREIGN KEY (`ramo_id`) REFERENCES `ramo` (`ramo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `poliza_tipo_poliza_fk` FOREIGN KEY (`tipo_poliza_id`) REFERENCES `tipo_poliza` (`tipo_poliza_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `titular_poliza_fk` FOREIGN KEY (`usuario_id_titular`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tomador_poliza_fk` FOREIGN KEY (`usuario_id_tomador`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poliza`
--

LOCK TABLES `poliza` WRITE;
/*!40000 ALTER TABLE `poliza` DISABLE KEYS */;
/*!40000 ALTER TABLE `poliza` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ramo`
--

DROP TABLE IF EXISTS `ramo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ramo` (
  `ramo_id` int(11) NOT NULL AUTO_INCREMENT,
  `ramo_nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`ramo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ramo`
--

LOCK TABLES `ramo` WRITE;
/*!40000 ALTER TABLE `ramo` DISABLE KEYS */;
INSERT INTO `ramo` VALUES (1,'Personas'),(2,'Patrimoniales'),(3,'Auto'),(4,'RC'),(5,'Fianza');
/*!40000 ALTER TABLE `ramo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siniestro`
--

DROP TABLE IF EXISTS `siniestro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siniestro` (
  `siniestro_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `poliza_id` int(11) NOT NULL,
  `numero_siniestro` varchar(45) NOT NULL,
  `fecha_siniestro` varchar(45) NOT NULL,
  `monto_siniestro` double NOT NULL,
  `ramo_id` int(11) NOT NULL,
  `vehiculo_id` int(11) DEFAULT NULL,
  `fecha_resolucion` varchar(45) DEFAULT NULL,
  `fecha_declaracion` varchar(45) NOT NULL,
  PRIMARY KEY (`siniestro_id`),
  KEY `siniestro_usuario_fk_idx` (`usuario_id`),
  KEY `siniestro_poliza_fk_idx` (`poliza_id`),
  KEY `siniestro_ramo_fk_idx` (`ramo_id`),
  KEY `siniestro_vehiculo_fk_idx` (`vehiculo_id`),
  CONSTRAINT `siniestro_poliza_fk` FOREIGN KEY (`poliza_id`) REFERENCES `poliza` (`poliza_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `siniestro_ramo_fk` FOREIGN KEY (`ramo_id`) REFERENCES `ramo` (`ramo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `siniestro_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `siniestro_vehiculo_fk` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculo` (`vehiculo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siniestro`
--

LOCK TABLES `siniestro` WRITE;
/*!40000 ALTER TABLE `siniestro` DISABLE KEYS */;
/*!40000 ALTER TABLE `siniestro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_menu_aplicacion`
--

DROP TABLE IF EXISTS `sub_menu_aplicacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_menu_aplicacion` (
  `sub_menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `url` varchar(45) NOT NULL,
  `menu_id` int(11) NOT NULL,
  PRIMARY KEY (`sub_menu_id`,`menu_id`),
  KEY `menu_id_frk_idx` (`menu_id`),
  CONSTRAINT `menu_id_frk` FOREIGN KEY (`menu_id`) REFERENCES `menu_aplicacion` (`menu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_menu_aplicacion`
--

LOCK TABLES `sub_menu_aplicacion` WRITE;
/*!40000 ALTER TABLE `sub_menu_aplicacion` DISABLE KEYS */;
INSERT INTO `sub_menu_aplicacion` VALUES (1,'Ver Usuarios','verUsuarios',1),(2,'Crear Usuarios','crearUsuarios',1),(3,'Ver Polizas','verPolizas',2),(4,'Crear Polizas','crearPolizas',2),(5,'Ver Vehiculos','verVehiculos',3),(6,'Crear Vehiculos','crearVehiculos',3),(7,'Ver Siniestros','verSiniestro',4),(8,'Crear Siniestro','crearSiniestro',4),(9,'Ver Coberturas','verCobertura',5),(10,'Crear Cobertura','crearCobertura',5),(11,'Ver Aseguradoras','verAseguradora',6),(12,'Crear Aseguradora','crearAseguradora',6),(13,'Acceso de Usuarios','accesoUsuario',7);
/*!40000 ALTER TABLE `sub_menu_aplicacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_poliza`
--

DROP TABLE IF EXISTS `tipo_poliza`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_poliza` (
  `tipo_poliza_id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_poliza_nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`tipo_poliza_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_poliza`
--

LOCK TABLES `tipo_poliza` WRITE;
/*!40000 ALTER TABLE `tipo_poliza` DISABLE KEYS */;
INSERT INTO `tipo_poliza` VALUES (1,'Individual'),(2,'Colectiva'),(3,'Solidaria');
/*!40000 ALTER TABLE `tipo_poliza` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_usuario`
--

DROP TABLE IF EXISTS `tipo_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_usuario` (
  `tipo_usuario_id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_usuario_nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`tipo_usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_usuario`
--

LOCK TABLES `tipo_usuario` WRITE;
/*!40000 ALTER TABLE `tipo_usuario` DISABLE KEYS */;
INSERT INTO `tipo_usuario` VALUES (1,'Juridico'),(2,'Natural'),(3,'Gubernamental'),(4,'Administrador'),(5,'Siniestros'),(6,'Transcripcion'),(7,'Suscripcion'),(8,'Consulta');
/*!40000 ALTER TABLE `tipo_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `documento_id` varchar(45) NOT NULL,
  `fecha_nacimiento` varchar(45) NOT NULL,
  `correo` varchar(45) DEFAULT NULL,
  `direccion` varchar(1500) DEFAULT NULL,
  `tipo_usuario_id` int(11) DEFAULT NULL,
  `clave` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `cedula_UNIQUE` (`documento_id`),
  UNIQUE KEY `correo_UNIQUE` (`correo`),
  KEY `usuario_tipo_usuario_fk_idx` (`tipo_usuario_id`),
  CONSTRAINT `usuario_tipo_usuario_fk` FOREIGN KEY (`tipo_usuario_id`) REFERENCES `tipo_usuario` (`tipo_usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Yajaira','Vera','1573934','21/03/1984','yaja.vera21@gmail.com','Los Teques',2,''),(2,'Vladimir','Fernandez','16368619','21/03/1984','vladimir.fernandez21@gmail.com','Los Teques',4,'12345');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculo`
--

DROP TABLE IF EXISTS `vehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehiculo` (
  `vehiculo_id` int(11) NOT NULL AUTO_INCREMENT,
  `placa` varchar(45) NOT NULL,
  `marca` varchar(45) NOT NULL,
  `modelo` varchar(45) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `ano` varchar(45) NOT NULL,
  `version` varchar(45) NOT NULL,
  PRIMARY KEY (`vehiculo_id`),
  KEY `vehiculo_usuario_fk_idx` (`usuario_id`),
  CONSTRAINT `vehiculo_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculo`
--

LOCK TABLES `vehiculo` WRITE;
/*!40000 ALTER TABLE `vehiculo` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehiculo` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-29  8:46:48
