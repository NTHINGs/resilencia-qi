-- --------------------------------------------------------
-- Host:                         165.227.61.176
-- Versión del servidor:         5.7.24-0ubuntu0.16.04.1 - (Ubuntu)
-- SO del servidor:              Linux
-- HeidiSQL Versión:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando datos para la tabla wordpress.wp_resiliencia_preguntas: ~46 rows (aproximadamente)
/*!40000 ALTER TABLE `wp_resiliencia_preguntas` DISABLE KEYS */;
INSERT INTO `wp_resiliencia_preguntas` (`id`, `pregunta`, `tipo`, `grupo`) VALUES
	(1, 'Tengo personas alrededor en quienes conf&iacute;o y quienes me quieren', 'P', 'Empatía'),
	(2, 'Soy feliz cuando hago algo bueno para los demás y les demuestro mi amor', 'P', 'Empatía'),
	(3, 'Me cuesta mucho entender los sentimientos de los demás', 'N', 'Autoestima'),
	(4, 'Sé como ayudar a alguien que está triste', 'P', 'Autoestima'),
	(5, 'Estoy dispuesta a responsabilizarme de mis actos', 'P', 'Autonomía'),
	(6, 'Puedo buscar maneras de resolver mis problemas', 'P', 'Autonomía'),
	(7, 'Trato de mantener el buen ánimo la mayor parte del tiempo', 'P', 'Humor'),
	(8, 'Me gusta reírme de los problemas que tengo', 'P', 'Humor'),
	(9, 'Cuando tengo un problema hago cosas nuevas para poder solucionarlo', 'P', 'Creatividad'),
	(10, 'Me gusta imaginar formas en la naturaleza, por ejemplo, le doy formas a las nubes', 'P', 'Creatividad'),
	(11, 'Soy una persona por la que los otros sienten aprecio y cariño', 'P', 'Empatía'),
	(12, 'Puedo equivocarme o hacer travesuras sin perder el afecto de mis superiores', 'P', 'Empatía'),
	(13, 'Ayudo a mis compañeros cuando puedo', 'P', 'Autoestima'),
	(14, 'Aunque tenga ganas, puedo evitar hacer algo peligroso o que no está́ bien', 'P', 'Autonomía'),
	(15, 'Me doy cuenta cuando hay peligro y trato de prevenirlo', 'P', 'Autonomía'),
	(16, 'Me gusta estar siempre alegre a pesar de las dificultades que pueda tener', 'P', 'Humor'),
	(17, 'Le encuentro el lado chistoso a las cosas malas que me pasan', 'P', 'Humor'),
	(18, 'Me gusta imaginar situaciones nuevas, como por ejemplo desarrollar una empresa nueva en mi vida', 'P', 'Creatividad'),
	(19, 'Me gusta contar las historia, como solo a mí se me ocurren', 'P', 'Creatividad'),
	(20, 'Aunque me sienta triste o esté molesta, los demás me siguen queriendo', 'P', 'Empatía'),
	(21, 'Soy feliz', 'P', 'Empatía'),
	(22, 'Me entristece ver sufrir a la gente', 'P', 'Autoestima'),
	(23, 'Trato de no herir los sentimientos de los demás', 'P', 'Autoestima'),
	(24, ' Puedo resolver problemas propios de mi edad', 'P', 'Autonomía'),
	(25, 'Puedo tomar decisiones con facilidad', 'P', 'Autonomía'),
	(26, 'Me es fácil reírme aun en los momentos más difíciles y tristes de mi vida', 'P', 'Humor'),
	(27, 'Me gusta reírme de los defectos de los demás', 'N', 'Humor'),
	(28, 'Ante situaciones difíciles, encuentro nuevas soluciones con rapidez y facilidad.', 'P', 'Creatividad'),
	(29, 'Me gusta que las cosas se hagan como siempre', 'N', 'Creatividad'),
	(30, 'Es difícil que me vaya bien, porque no soy buena ni inteligente', 'N', 'Empatía'),
	(31, 'Me doy por vencida fácilmente ante cualquier dificultad', 'N', 'Empatía'),
	(32, 'Cuando una persona tiene algún defecto me burlo de ella', 'N', 'Autoestima'),
	(33, 'Me gusta seguir más las ideas de los demás, que mis propias ideas', 'N', 'Autoestima'),
	(34, 'Prefiero que me digan lo que debo hacer', 'N', 'Autonomía'),
	(35, 'Me gusta seguir más las ideas de los demás, que mis propias ideas', 'N', 'Autonomía'),
	(36, 'Estoy de mal humor casi todo el tiempo', 'N', 'Humor'),
	(37, 'Generalmente no me río', 'N', 'Humor'),
	(38, 'Me cuesta trabajo imaginar situaciones nuevas', 'N', 'Creatividad'),
	(39, 'Cuando hay problemas o dificultades, no se me ocurre nada para poder resolverlos', 'N', 'Creatividad'),
	(40, 'Me cuesta mucho trabajo aceptarme como soy', 'N', 'Empatía'),
	(41, 'Tengo una mala opinión de mi misma', 'N', 'Empatía'),
	(42, 'Sé cuando un amigo está alegre', 'P', 'Autoestima'),
	(43, 'Me fastidia tener que escuchar a los demás', 'N', 'Autoestima'),
	(44, 'Me interesa poco lo que puede sucederle a los demás', 'N', 'Autoestima'),
	(45, 'Me gusta que los demás tomen las decisiones por mí', 'N', 'Autonomía'),
	(46, 'Me siento culpable de los problemas que hay en mi casa.', 'N', 'Autonomía'),
	(47, 'Con tantos problemas que tengo, casi nada me hace reír', 'N', 'Humor'),
	(48, 'Le doy más importancia al lado triste de las cosas que me pasan', 'N', 'Humor');
/*!40000 ALTER TABLE `wp_resiliencia_preguntas` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
