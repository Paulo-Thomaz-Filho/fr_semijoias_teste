-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: loja
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `avaliacao`
--

DROP TABLE IF EXISTS `avaliacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `avaliacao` (
  `avaliacao_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `nota` int(11) NOT NULL CHECK (`nota` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `data_avaliacao` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`avaliacao_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `avaliacao_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`),
  CONSTRAINT `avaliacao_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avaliacao`
--

LOCK TABLES `avaliacao` WRITE;
/*!40000 ALTER TABLE `avaliacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `avaliacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categoria` (
  `categoria_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (1,'Colares','Acessórios para o pescoço com diferentes estilos e comprimentos.'),(2,'Pulseiras','Peças para o pulso, disponíveis em vários materiais.'),(3,'Brincos','Acessórios para as orelhas, de modelos discretos a ousados.'),(4,'Anéis','Anéis ajustáveis e tamanhos variados para diversas ocasiões.'),(5,'Conjuntos','Combinações de peças coordenadas, como colar e brinco.');
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estoque`
--

DROP TABLE IF EXISTS `estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estoque` (
  `estoque_id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`estoque_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `estoque_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque`
--

LOCK TABLES `estoque` WRITE;
/*!40000 ALTER TABLE `estoque` DISABLE KEYS */;
/*!40000 ALTER TABLE `estoque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itempedido`
--

DROP TABLE IF EXISTS `itempedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itempedido` (
  `itempedido_id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`itempedido_id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `itempedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`pedido_id`),
  CONSTRAINT `itempedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itempedido`
--

LOCK TABLES `itempedido` WRITE;
/*!40000 ALTER TABLE `itempedido` DISABLE KEYS */;
/*!40000 ALTER TABLE `itempedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marca`
--

DROP TABLE IF EXISTS `marca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marca` (
  `marca_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`marca_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marca`
--

LOCK TABLES `marca` WRITE;
/*!40000 ALTER TABLE `marca` DISABLE KEYS */;
INSERT INTO `marca` VALUES (1,'LuxJewels','Especializada em peças sofisticadas com acabamento premium.'),(2,'Brilho Vivo','Bijuterias modernas e acessíveis para o dia a dia.'),(3,'Encanto Dourado','Foco em colares e brincos com design clássico.'),(4,'Estilo Urbano','Pulseiras e acessórios com visual masculino e despojado.'),(5,'Pérola Rosa','Linha voltada ao público feminino infantil e teen.');
/*!40000 ALTER TABLE `marca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedido` (
  `pedido_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `data_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Pendente',
  `valor_total` decimal(10,2) NOT NULL,
  `endereco_entrega` varchar(255) NOT NULL,
  PRIMARY KEY (`pedido_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produto` (
  `produto_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `avaliacao` decimal(3,2) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT 1,
  `imagem_url` varchar(255) DEFAULT NULL,
  `unidade_estoque` int(11) DEFAULT 0,
  `promocao_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`produto_id`),
  KEY `marca_id` (`marca_id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `promocao_id` (`promocao_id`),
  CONSTRAINT `produto_ibfk_1` FOREIGN KEY (`marca_id`) REFERENCES `marca` (`marca_id`),
  CONSTRAINT `produto_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`categoria_id`),
  CONSTRAINT `produto_ibfk_3` FOREIGN KEY (`promocao_id`) REFERENCES `promocao` (`promocao_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` VALUES (11,'Colar Luxo Cristal Azul',89.90,4.80,1,1,1,'imagens/colar_cristal.jpg',12,NULL),(12,'Pulseira Couro Preto',49.90,4.50,4,2,1,'imagens/pulseira_couro.jpg',20,NULL),(13,'Brinco Argola Dourada',34.50,4.60,3,3,1,'imagens/brinco_argola.jpg',25,NULL),(14,'Anel Prata Coração Vazado',59.00,4.90,2,4,1,'imagens/anel_coracao.jpg',10,NULL),(15,'Conjunto Brilho da Lua',129.90,5.00,1,5,1,'imagens/conjunto_lua.jpg',8,NULL),(16,'Brinco Estrelinha Infantil',19.90,4.20,5,3,1,'imagens/brinco_kids.jpg',30,NULL),(17,'Anel Ajustável Floral',39.99,4.30,3,4,1,'imagens/anel_floral.jpg',18,NULL),(18,'Colar Dente de Tigre',69.90,4.40,4,1,1,'imagens/colar_tigre.jpg',15,NULL),(19,'Pulseira Pérolas Clássicas',55.00,4.70,2,2,1,'imagens/pulseira_perolas.jpg',22,NULL),(20,'Conjunto Teen Rosa Chic',79.90,4.50,5,5,1,'imagens/conjunto_teen.jpg',16,NULL);
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promocao`
--

DROP TABLE IF EXISTS `promocao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promocao` (
  `promocao_id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) DEFAULT NULL,
  `tipo` enum('valor','percentual') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  PRIMARY KEY (`promocao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promocao`
--

LOCK TABLES `promocao` WRITE;
/*!40000 ALTER TABLE `promocao` DISABLE KEYS */;
/*!40000 ALTER TABLE `promocao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `usuario_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `tipo_acesso` enum('admin','cliente') NOT NULL DEFAULT 'cliente',
  `nome` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cpf` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'joao@gmail.com','8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92','cliente','João Silva','12345678901','01001000','Rua das Flores, 123','Centro','São Paulo','SP','(11) 91234-5678','img/joao.jpg',1);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendas`
--

DROP TABLE IF EXISTS `vendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendas` (
  `venda_id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_venda` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`venda_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendas`
--

LOCK TABLES `vendas` WRITE;
/*!40000 ALTER TABLE `vendas` DISABLE KEYS */;
/*!40000 ALTER TABLE `vendas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-19 13:37:21
