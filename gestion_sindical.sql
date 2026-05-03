-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: proyectouni
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departamentos` (
  `id_departamento` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_departamento`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamentos`
--

LOCK TABLES `departamentos` WRITE;
/*!40000 ALTER TABLE `departamentos` DISABLE KEYS */;
INSERT INTO `departamentos` VALUES (1,'Secretaría de Actas','Registro, control y resguardo de actas y documentos oficiales'),(2,'Tesorería','Gestión de recursos financieros, pagos y apoyos económicos'),(3,'Secretaría de Ajustes','Revisión y regularización de situaciones laborales');
/*!40000 ALTER TABLE `departamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id_notificacion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_tramite` int NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `leida` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_notificacion`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
INSERT INTO `notificaciones` VALUES (1,1,4,'Trámite aprobado','Nos complace informarle que su trámite de \"Registro de acuerdos\" ha sido aprobado exitosamente.','2026-02-25 22:16:07',1),(2,1,3,'Trámite rechazado','Le informamos que su trámite de \"Registro de acuerdos\" ha sido rechazado. Puede revisar los detalles o comunicarse con el departamento correspondiente.','2026-02-25 22:17:17',1),(3,1,3,'Trámite aprobado','Nos complace informarle que su trámite de \"Registro de acuerdos\" ha sido aprobado exitosamente.','2026-02-26 13:02:14',1),(4,1,5,'Su trámite se encuentra en revisión','Le informamos que su trámite de \"Pago de cuotas\" ha pasado a estado \'En revisión\'. Nuestro equipo se encuentra analizándolo.','2026-02-26 13:19:03',1),(5,1,3,'Su trámite se encuentra en revisión','Le informamos que su trámite de \"Copia de actas\" ha pasado a estado \'En revisión\'. Nuestro equipo se encuentra analizándolo.','2026-03-07 12:49:16',1),(6,1,3,'Trámite aprobado','Nos complace informarle que su trámite de \"Copia de actas\" ha sido aprobado exitosamente.','2026-03-19 14:08:16',1),(7,1,3,'Trámite rechazado','Le informamos que su trámite de \"Copia de actas\" ha sido rechazado. Puede revisar los detalles o comunicarse con el departamento correspondiente.','2026-03-19 22:41:11',1),(8,1,3,'Trámite aprobado','Nos complace informarle que su trámite de \"Copia de actas\" ha sido aprobado exitosamente.','2026-03-19 22:41:20',1),(9,1,3,'Trámite rechazado','Le informamos que su trámite de \"Copia de actas\" ha sido rechazado. Puede revisar los detalles o comunicarse con el departamento correspondiente.','2026-03-19 22:41:28',1),(10,1,3,'Su trámite se encuentra en revisión','Le informamos que su trámite de \"Copia de actas\" ha pasado a estado \'En revisión\'. Nuestro equipo se encuentra analizándolo.','2026-03-20 01:29:14',1),(11,1,4,'Trámite aprobado','Nos complace informarle que su trámite de \"Registro de acuerdos\" ha sido aprobado exitosamente.','2026-05-02 12:07:23',1);
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tramites`
--

DROP TABLE IF EXISTS `tramites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tramites` (
  `id_tramite` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_departamento` int NOT NULL,
  `tipo_tramite` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_completo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_ficha` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `turno` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `curp` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos_especificos` json NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Pendiente','En revisión','Aprobado','Rechazado') COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `documento_respaldo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `respuesta_afiliado` text COLLATE utf8mb4_unicode_ci,
  `fecha_respuesta` datetime DEFAULT NULL,
  PRIMARY KEY (`id_tramite`),
  KEY `id_usuario` (`id_usuario`),
  KEY `fk_tramites_departamento` (`id_departamento`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tramites`
--

LOCK TABLES `tramites` WRITE;
/*!40000 ALTER TABLE `tramites` DISABLE KEYS */;
INSERT INTO `tramites` VALUES (1,1,1,'Registro de acuerdos','dep1new','ddd','ddd','dd','ddd@dd.com','ddd','ddd','{\"tema_acuerdo\": \"dddd\", \"tipo_acuerdo\": \"Laboral\", \"fecha_asamblea\": \"2026-03-07\", \"descripcion_acuerdo\": \"dddd\"}','2026-03-07 18:47:59','Pendiente','uploads/tramites/69ac72df41a69_CV Randy Azael.pdf',NULL,NULL),(2,1,1,'Registro de asistencia','dep1new','ddd','ddd','dd','ddd@dd.com','ddd','ddd','{\"observaciones\": \"ddd\", \"tipo_asamblea\": \"Extraordinaria\", \"fecha_asamblea\": \"2026-03-07\", \"lugar_asamblea\": \"ddd\"}','2026-03-07 18:48:23','Pendiente','uploads/tramites/69ac72f7049a0_CV Randy Azael.pdf',NULL,NULL),(3,1,1,'Copia de actas','dep1new','ddd','ddd','dd','ddd@dd.com','ddd','ddd','{\"tipo_acta\": \"Extraordinaria\", \"fecha_acta\": \"2026-03-07\", \"formato_entrega\": \"Digital\", \"motivo_solicitud\": \"ddd\"}','2026-03-07 18:48:34','En revisión',NULL,NULL,NULL),(4,1,1,'Registro de acuerdos','ffff','dsfds','sddds','fsddfs','dsdf@gmai.com','dsdfssd','sdfsdf','{\"tema_acuerdo\": \"asdsa\", \"tipo_acuerdo\": \"Laboral\", \"fecha_asamblea\": \"2026-05-06\", \"descripcion_acuerdo\": \"asddasas\"}','2026-05-02 18:05:21','Aprobado','uploads/tramites/69f63ce1c5cbc_CV Randy Azael Daniel Ruiz.pdf',NULL,NULL);
/*!40000 ALTER TABLE `tramites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_ficha` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `curp` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rfc` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('usuario','afiliado','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_departamento` int DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_acceso` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_departamento` (`id_departamento`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Usuario','999999998','212','RUTY690306MNLZML02','212221122121','2026-02-12','xx','uploads/perfiles/usuario_1_1777411857.png','usuario@gmail.com','usuario','usuario',NULL,'2026-02-13 01:26:50','2026-05-02 18:28:15'),(2,'Afiliado','5512345678',NULL,NULL,NULL,NULL,NULL,NULL,'afiliado@gmail.com','afiliado','afiliado',1,'2026-02-13 01:30:14','2026-05-02 18:26:37'),(3,'Afiliado2','5512345679',NULL,NULL,NULL,NULL,NULL,NULL,'afiliado2@gmail.com','afiliado2','afiliado',2,'2026-02-13 02:05:20','2026-03-07 18:28:19'),(4,'Afiliado3','5512345673',NULL,NULL,NULL,NULL,NULL,NULL,'afiliado3@gmail.com','afiliado3','afiliado',3,'2026-02-13 02:05:33','2026-03-07 18:23:40');
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

-- Dump completed on 2026-05-03 17:07:34
