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
use jcrseguros;

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
INSERT INTO `acceso_opciones_rol` VALUES (1,1,1),(1,2,1),(1,3,1),(1,4,1),(3,4,1),(1,5,1),(1,6,1),(1,7,1);
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
  `status_id` int(11) DEFAULT '1',
  PRIMARY KEY (`aseguradora_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aseguradora`
--

LOCK TABLES `aseguradora` WRITE;
/*!40000 ALTER TABLE `aseguradora` DISABLE KEYS */;
INSERT INTO `aseguradora` VALUES (1,'Seguros Universitas C.A.',NULL,1),(2,'Seguros Piramide C.A.',NULL,1),(3,'Seguros Caracas de Liberty Mutual C.A.',NULL,1),(4,'Vivir Seguros C.A.',NULL,1),(5,'Estar Seguros S.A.',NULL,1),(6,'Universal de Seguros C.A.',NULL,1),(7,'Seguros La Occidental',NULL,1),(8,'La Venezolaa de Seguros y Vida C.A.',NULL,1),(9,'Oceanica de Seguros',NULL,1),(10,'C.A. de Seguros La Internacional',NULL,1);
/*!40000 ALTER TABLE `aseguradora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `cliente_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cliente` varchar(45) NOT NULL,
  `apellido_cliente` varchar(45) NOT NULL,
  `documento_id_cliente` varchar(45) NOT NULL,
  `fecha_nacimiento` varchar(45) NOT NULL,
  `correo_cliente` varchar(45) DEFAULT NULL,
  `direccion` varchar(1500) DEFAULT NULL,
  `tipo_cliente_id` int(11) DEFAULT NULL,
  `status_id_cliente` int(11) DEFAULT '1',
  PRIMARY KEY (`cliente_id`),
  UNIQUE KEY `cedula_UNIQUE` (`documento_id_cliente`),
  UNIQUE KEY `correo_UNIQUE` (`correo_cliente`),
  KEY `cliente_tipo_cliente_fk_idx` (`tipo_cliente_id`),
  KEY `status_id_cliente_fk_idx` (`status_id_cliente`),
  CONSTRAINT `cliente_tipo_cliente_fk` FOREIGN KEY (`tipo_cliente_id`) REFERENCES `tipo_cliente` (`tipo_cliente_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `status_id_cliente_fk` FOREIGN KEY (`status_id_cliente`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'Ronald','Perez','20256148','20-01-1983','ronal_perez@gmail.com','Caracas',2,1),(2,'Vladimir','Fernandez','16368619','21/03/1984','vladimir.fernandez21@gmail.com','Los Teques',4,1),(5,'Luis','Fernandez','14368619','21/03/1984','luis.fernandez@gmail.com','Los Teques',2,1),(8,'Gabriela','Wilches','37474444','22/05/1981','gwilches@gmail.com','Caracas',8,1),(9,'Lenin','Fernandez','3639363','02/03/2016','mao123@gmail.com','caracas,prados de este',1,1),(11,'Eduardo','Luttinger','12477393','13/05/1982','eluttinger@gmail.com','San antonio de los altos',4,1),(12,'Eduardo David','De La Cruz Marrero','15870957','20/01/1983','gualdodelacruz@gmail.com','La Tahona',4,1);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
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
  `status_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_aplicacion`
--

LOCK TABLES `menu_aplicacion` WRITE;
/*!40000 ALTER TABLE `menu_aplicacion` DISABLE KEYS */;
INSERT INTO `menu_aplicacion` VALUES (1,1,'Usuarios','fa fa-user','JCR Seguros',1,1),(2,1,'Polizas','fa fa-file-powerpoint-o','JCR Seguros',2,1),(3,1,'Vehiculos','fa fa-car','JCR Seguros',3,2),(4,1,'Siniestros','fa fa-warning','JCR Seguros',4,1),(5,1,'Coberturas','fa fa-credit-card','JCR Seguros',5,2),(6,1,'Aseguradoras','fa fa-industry','JCR Seguros',6,1),(7,1,'Opciones Administrativas','fa fa-cogs','JCR Seguros',7,1);
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
  `numero_recibo` varchar(45) NOT NULL,
  `vigencia` varchar(15) NOT NULL,
  `tipo_poliza_id` int(11) NOT NULL,
  `referencia` varchar(45) NOT NULL,
  `prima_total` varchar(45) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poliza`
--

LOCK TABLES `poliza` WRITE;
/*!40000 ALTER TABLE `poliza` DISABLE KEYS */;
INSERT INTO `poliza` VALUES (1,'423454',2,8,1,2,9,'32423','12/01/2015',3,'1111','10000',1),(3,'34535',1,8,9,2,10,'543543','13/09/2016',2,'4353453','20000',1),(4,'111666',1,9,8,11,3,'79364','14/05/1982',1,'0923','20000',3),(5,'84936363',4,5,8,2,1,'32','14/05/1982',2,'11222','900',1);
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
  `poliza_id` int(11) NOT NULL,
  `numero_siniestro` varchar(45) NOT NULL,
  `fecha_siniestro` varchar(45) NOT NULL,
  `monto_siniestro` double NOT NULL,
  `ramo_id` int(11) NOT NULL,
  `vehiculo_id` int(11) DEFAULT NULL,
  `fecha_resolucion` varchar(45) DEFAULT NULL,
  `fecha_declaracion` varchar(45) NOT NULL,
  `status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`siniestro_id`),
  KEY `siniestro_poliza_fk_idx` (`poliza_id`),
  KEY `siniestro_ramo_fk_idx` (`ramo_id`),
  KEY `siniestro_vehiculo_fk_idx` (`vehiculo_id`),
  CONSTRAINT `siniestro_poliza_fk` FOREIGN KEY (`poliza_id`) REFERENCES `poliza` (`poliza_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `siniestro_ramo_fk` FOREIGN KEY (`ramo_id`) REFERENCES `ramo` (`ramo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `siniestro_vehiculo_fk` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculo` (`vehiculo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siniestro`
--

LOCK TABLES `siniestro` WRITE;
/*!40000 ALTER TABLE `siniestro` DISABLE KEYS */;
INSERT INTO `siniestro` VALUES (3,1,'123456789','20/01/1983',957,1,1,'20/01/2016','01-01-2016',1);
/*!40000 ALTER TABLE `siniestro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (1,'ACTIVO'),(2,'INACTIVO'),(3,'DELETE');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
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
INSERT INTO `sub_menu_aplicacion` VALUES (1,'Ver Usuarios','verUsuarios',1),(3,'Ver Polizas','verPolizas',2),(5,'Ver Vehiculos','verVehiculos',3),(7,'Ver Siniestros','verSiniestro',4),(9,'Ver Coberturas','verCobertura',5),(11,'Ver Aseguradoras','verAseguradora',6),(13,'Acceso de Usuarios','accesoUsuario',7);
/*!40000 ALTER TABLE `sub_menu_aplicacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telefonos`
--

DROP TABLE IF EXISTS `telefonos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telefonos` (
  `telefono_id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono_nombre` varchar(45) DEFAULT NULL,
  `telefono_numero` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`telefono_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telefonos`
--

LOCK TABLES `telefonos` WRITE;
/*!40000 ALTER TABLE `telefonos` DISABLE KEYS */;
INSERT INTO `telefonos` VALUES (3,'Movil','324324234'),(4,'Hogar',''),(5,'Oficina',''),(6,'Hogar','4354352222'),(7,'Movil',''),(8,'Oficina','3453453453'),(9,'Hogar','3242340000'),(10,'Movil',''),(11,'Oficina',''),(12,'Hogar','1111111111'),(13,'Movil',''),(14,'Oficina',''),(15,'Hogar','1111111111'),(16,'Movil',''),(17,'Oficina','');
/*!40000 ALTER TABLE `telefonos` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_usuario`
--

LOCK TABLES `tipo_usuario` WRITE;
/*!40000 ALTER TABLE `tipo_usuario` DISABLE KEYS */;
INSERT INTO `tipo_usuario` VALUES (1,'Administrador'),(2,'Suscripcion'),(3,'Siniestros');
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
  `correo` varchar(45) DEFAULT NULL,
  `tipo_usuario_id` int(11) DEFAULT NULL,
  `clave` varchar(45) DEFAULT NULL,
  `status_id` int(11) DEFAULT '1',
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `correo_UNIQUE` (`correo`),
  KEY `usuario_tipo_usuario_fk_idx` (`tipo_usuario_id`),
  KEY `status_id_fk_idx` (`status_id`),
  CONSTRAINT `status_id_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `usuario_tipo_usuario_fk` FOREIGN KEY (`tipo_usuario_id`) REFERENCES `tipo_usuario` (`tipo_usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Yajaira','Vera','yaja.vera21@gmail.com',1,'',1),(2,'Vladimir','Fernandez','vladimir.fernandez21@gmail.com',1,'12345',1),(5,'Luis','Fernandez','luis.fernandez@gmail.com',1,'',1),(8,'Gabriela','Wilches','gwilches@gmail.com',1,NULL,1),(9,'Lenin','Fernandez','mao123@gmail.com',1,NULL,1),(11,'Eduardo','Luttinger','eluttinger@gmail.com',3,'12345',1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios_telefonos`
--

DROP TABLE IF EXISTS `usuarios_telefonos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_telefonos` (
  `usuario_id` int(11) NOT NULL DEFAULT '0',
  `telefono_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`telefono_id`,`usuario_id`),
  UNIQUE KEY `telefono_id_UNIQUE` (`telefono_id`),
  KEY `usuario_id_fk_phone_idx` (`usuario_id`),
  CONSTRAINT `telefono_id_fk_phone` FOREIGN KEY (`telefono_id`) REFERENCES `telefonos` (`telefono_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `usuario_id_fk_phone` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios_telefonos`
--

LOCK TABLES `usuarios_telefonos` WRITE;
/*!40000 ALTER TABLE `usuarios_telefonos` DISABLE KEYS */;
INSERT INTO `usuarios_telefonos` VALUES (5,3),(5,4),(5,5),(8,6),(8,7),(8,8),(9,9),(9,10),(9,11),(11,15),(11,16),(11,17);
/*!40000 ALTER TABLE `usuarios_telefonos` ENABLE KEYS */;
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
  `status_id` varchar(45) NOT NULL DEFAULT '1',
  PRIMARY KEY (`vehiculo_id`),
  KEY `vehiculo_usuario_fk_idx` (`usuario_id`),
  CONSTRAINT `vehiculo_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculo`
--

LOCK TABLES `vehiculo` WRITE;
/*!40000 ALTER TABLE `vehiculo` DISABLE KEYS */;
INSERT INTO `vehiculo` VALUES (1,'4353','Ford','Ka',1,'2000','1','1');
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

-- Dump completed on 2016-09-24 11:12:17
