-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para votacion_escolar
CREATE DATABASE IF NOT EXISTS `votacion_escolar` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `votacion_escolar`;

-- Volcando estructura para tabla votacion_escolar.alumnos
CREATE TABLE IF NOT EXISTS `alumnos` (
  `id_alumno` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `dni` varchar(8) NOT NULL,
  `grado` varchar(20) NOT NULL,
  `nivel` varchar(20) NOT NULL DEFAULT 'primaria',
  `seccion` varchar(10) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_alumno`),
  UNIQUE KEY `dni` (`dni`),
  CONSTRAINT `chk_nivel` CHECK ((`nivel` in (_utf8mb4'Primaria',_utf8mb4'Secundaria')))
) ENGINE=InnoDB AUTO_INCREMENT=1720 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla votacion_escolar.alumnos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla votacion_escolar.asignacion_mesa
CREATE TABLE IF NOT EXISTS `asignacion_mesa` (
  `id_alumno` int NOT NULL,
  `id_mesa` int NOT NULL,
  `id_proceso` int NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_alumno`,`id_proceso`),
  KEY `fk_asignacion_mesa` (`id_mesa`),
  KEY `fk_asignacion_proceso` (`id_proceso`),
  CONSTRAINT `fk_asignacion_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  CONSTRAINT `fk_asignacion_mesa_ref` FOREIGN KEY (`id_mesa`) REFERENCES `mesa_sufragio` (`id_mesa`) ON DELETE CASCADE,
  CONSTRAINT `fk_asignacion_proceso_ref` FOREIGN KEY (`id_proceso`) REFERENCES `proceso_electoral` (`id_proceso`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla votacion_escolar.asignacion_mesa: ~0 rows (aproximadamente)

-- Volcando estructura para procedimiento votacion_escolar.asignar_alumnos_a_mesas
DELIMITER //
CREATE PROCEDURE `asignar_alumnos_a_mesas`(IN p_id_proceso INT)
BEGIN
    DECLARE total_mesas INT;
    DECLARE total_alumnos INT;
    DECLARE alumnos_primaria INT;
    DECLARE alumnos_secundaria INT;
    DECLARE alumnos_por_mesa INT;
    DECLARE resto INT;

    -- Borrar asignaciones anteriores para este proceso
    DELETE FROM asignacion_mesa WHERE id_proceso = p_id_proceso;
    
    -- Contar mesas activas para este proceso
    SELECT COUNT(*) INTO total_mesas 
    FROM mesa_sufragio 
    WHERE id_proceso = p_id_proceso AND activo = 1;
    
    IF total_mesas = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay mesas activas para este proceso.';
    END IF;
    
    -- Contar alumnos activos por nivel
    SELECT COUNT(*) INTO total_alumnos 
    FROM alumnos 
    WHERE activo = 1;
    
    SELECT COUNT(*) INTO alumnos_primaria 
    FROM alumnos 
    WHERE activo = 1 AND nivel = 'Primaria';
    
    SELECT COUNT(*) INTO alumnos_secundaria 
    FROM alumnos 
    WHERE activo = 1 AND nivel = 'Secundaria';
    
    IF total_alumnos = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay alumnos activos para este proceso.';
    END IF;
    
    -- Calcular alumnos por mesa
    SET alumnos_por_mesa = FLOOR(total_alumnos / total_mesas);
    SET resto = total_alumnos % total_mesas;
    
    -- Crear tabla temporal para alumnos con orden aleatorio por nivel
    DROP TEMPORARY TABLE IF EXISTS temp_alumnos;
    CREATE TEMPORARY TABLE temp_alumnos AS
    SELECT a.id_alumno, a.nivel,
           @row_num := IF(@prev_nivel = a.nivel, @row_num + 1, 1) AS fila
    FROM alumnos a, (SELECT @row_num := 0, @prev_nivel := NULL) AS vars
    WHERE a.activo = 1
    ORDER BY a.nivel, RAND();
    
    -- Crear tabla temporal para mesas con orden aleatorio
    DROP TEMPORARY TABLE IF EXISTS temp_mesas;
    CREATE TEMPORARY TABLE temp_mesas AS
    SELECT id_mesa, @m_row := @m_row + 1 AS orden
    FROM mesa_sufragio, (SELECT @m_row := 0) AS m_vars
    WHERE id_proceso = p_id_proceso AND activo = 1
    ORDER BY RAND();
    
    -- Asignar alumnos a mesas, evitando mesas previas
    INSERT INTO asignacion_mesa (id_alumno, id_mesa, id_proceso)
    SELECT 
        a.id_alumno,
        m.id_mesa,
        p_id_proceso
    FROM temp_alumnos a
    CROSS JOIN temp_mesas m
    LEFT JOIN (
        SELECT id_alumno, id_mesa
        FROM asignacion_mesa
        WHERE id_proceso < p_id_proceso
        GROUP BY id_alumno, id_mesa
    ) prev ON a.id_alumno = prev.id_alumno AND m.id_mesa = prev.id_mesa
    WHERE prev.id_mesa IS NULL
    AND m.orden = (
        CASE 
            WHEN a.fila <= (alumnos_por_mesa + 1) * resto THEN
                CEIL(a.fila / (alumnos_por_mesa + 1))
            ELSE
                resto + CEIL((a.fila - (alumnos_por_mesa + 1) * resto) / alumnos_por_mesa)
        END
    )
    ORDER BY a.nivel, RAND()
    LIMIT total_alumnos;
    
    -- Limpiar tablas temporales
    DROP TEMPORARY TABLE IF EXISTS temp_alumnos;
    DROP TEMPORARY TABLE IF EXISTS temp_mesas;
END//
DELIMITER ;

-- Volcando estructura para tabla votacion_escolar.candidato
CREATE TABLE IF NOT EXISTS `candidato` (
  `id_candidato` int NOT NULL AUTO_INCREMENT,
  `id_alumno` int NOT NULL,
  `id_proceso` int NOT NULL,
  `foto_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `foto_campaña` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `lema` varchar(200) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_candidato`),
  UNIQUE KEY `uk_candidato_proceso_alumno` (`id_alumno`,`id_proceso`),
  KEY `fk_candidato_alumno` (`id_alumno`),
  KEY `fk_candidato_proceso` (`id_proceso`),
  CONSTRAINT `fk_candidato_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  CONSTRAINT `fk_candidato_proceso` FOREIGN KEY (`id_proceso`) REFERENCES `proceso_electoral` (`id_proceso`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla votacion_escolar.candidato: ~0 rows (aproximadamente)

-- Volcando estructura para función votacion_escolar.generar_usuario_unico
DELIMITER //
CREATE FUNCTION `generar_usuario_unico`(nombre VARCHAR(100), apellido VARCHAR(100)) RETURNS varchar(50) CHARSET utf8mb4
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE base_usuario VARCHAR(10);
    DECLARE nuevo_usuario VARCHAR(50);
    DECLARE contador INT DEFAULT 1;
    
    SET base_usuario = LOWER(CONCAT(LEFT(nombre, 1), LEFT(apellido, 3)));
    SET nuevo_usuario = base_usuario;
    
    WHILE EXISTS (SELECT 1 FROM usuario WHERE usuario = nuevo_usuario) DO
        SET nuevo_usuario = CONCAT(base_usuario, contador);
        SET contador = contador + 1;
    END WHILE;
    
    RETURN nuevo_usuario;
END//
DELIMITER ;

-- Volcando estructura para tabla votacion_escolar.mesa_sufragio
CREATE TABLE IF NOT EXISTS `mesa_sufragio` (
  `id_mesa` int NOT NULL AUTO_INCREMENT,
  `id_proceso` int NOT NULL,
  `numero` varchar(10) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `nivel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Primaria',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_mesa`),
  UNIQUE KEY `uk_mesa_proceso` (`numero`,`id_proceso`),
  KEY `fk_mesa_proceso` (`id_proceso`),
  CONSTRAINT `fk_mesa_proceso` FOREIGN KEY (`id_proceso`) REFERENCES `proceso_electoral` (`id_proceso`) ON DELETE CASCADE,
  CONSTRAINT `chk_mesa_nivel` CHECK ((`nivel` in (_utf8mb4'Primaria',_utf8mb4'Secundaria')))
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla votacion_escolar.mesa_sufragio: ~0 rows (aproximadamente)

-- Volcando estructura para tabla votacion_escolar.proceso_electoral
CREATE TABLE IF NOT EXISTS `proceso_electoral` (
  `id_proceso` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_proceso`),
  CONSTRAINT `proceso_electoral_chk_1` CHECK ((`fecha_fin` >= `fecha_inicio`))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla votacion_escolar.proceso_electoral: ~1 rows (aproximadamente)
REPLACE INTO `proceso_electoral` (`id_proceso`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `activo`) VALUES
	(1, 'Elecciones 2025', 'Elecciones escolares', '2025-09-26', '2025-10-03', 1);

-- Volcando estructura para tabla votacion_escolar.rol
CREATE TABLE IF NOT EXISTS `rol` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla votacion_escolar.rol: ~1 rows (aproximadamente)
REPLACE INTO `rol` (`id_rol`, `nombre`) VALUES
	(1, 'Administrador');

-- Volcando estructura para tabla votacion_escolar.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `id_rol` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `fk_usuario_rol` (`id_rol`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla votacion_escolar.usuario: ~1 rows (aproximadamente)
REPLACE INTO `usuario` (`id_usuario`, `id_rol`, `nombre`, `apellido`, `usuario`, `password`, `estado`, `fecha_creacion`) VALUES
	(6, 1, 'Administrador', 'Administrador', 'aadm', '$2y$10$hOmnBL61nN6Nyggp2qYg7.06.AwEO6atCEGpCxYZ.6PIG0/YSGo8a', 1, '2025-11-22 15:59:26');

-- Volcando estructura para tabla votacion_escolar.voto
CREATE TABLE IF NOT EXISTS `voto` (
  `id_voto` int NOT NULL AUTO_INCREMENT,
  `id_mesa` int NOT NULL,
  `id_candidato` int DEFAULT NULL,
  `id_alumno` int NOT NULL,
  `id_proceso` int NOT NULL,
  `fecha_voto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_voto`),
  UNIQUE KEY `id_alumno` (`id_alumno`,`id_proceso`),
  KEY `id_mesa` (`id_mesa`),
  KEY `id_candidato` (`id_candidato`),
  KEY `id_proceso` (`id_proceso`),
  CONSTRAINT `voto_ibfk_1` FOREIGN KEY (`id_mesa`) REFERENCES `mesa_sufragio` (`id_mesa`),
  CONSTRAINT `voto_ibfk_2` FOREIGN KEY (`id_candidato`) REFERENCES `candidato` (`id_candidato`),
  CONSTRAINT `voto_ibfk_3` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  CONSTRAINT `voto_ibfk_4` FOREIGN KEY (`id_proceso`) REFERENCES `proceso_electoral` (`id_proceso`)
) ENGINE=InnoDB AUTO_INCREMENT=623 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla votacion_escolar.voto: ~0 rows (aproximadamente)

-- Volcando estructura para disparador votacion_escolar.trg_usuario_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `trg_usuario_insert` BEFORE INSERT ON `usuario` FOR EACH ROW BEGIN
    IF NEW.usuario = '' OR NEW.usuario IS NULL THEN
        SET NEW.usuario = generar_usuario_unico(NEW.nombre, NEW.apellido);
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
