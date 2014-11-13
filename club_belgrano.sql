-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 12-11-2014 a las 21:58:32
-- Versión del servidor: 5.5.40-0ubuntu0.14.04.1
-- Versión de PHP: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `club_belgrano`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Answer`
--

CREATE TABLE IF NOT EXISTS `Answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_poll` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `Answer`
--

INSERT INTO `Answer` (`id`, `id_poll`, `name`) VALUES
(1, 1, 'Argentina'),
(2, 1, 'Exterior'),
(3, 1, 'Cualquier lugar mientras sea lindo'),
(5, 2, 'Maradona'),
(6, 2, 'Messi'),
(7, 3, 'Excelente'),
(8, 3, 'Aburrido'),
(9, 3, 'Pasable');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Associate`
--

CREATE TABLE IF NOT EXISTS `Associate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `Associate`
--

INSERT INTO `Associate` (`id`, `name`) VALUES
(1, 'Activo sin ubicación'),
(2, 'Gasparini'),
(3, 'Artime'),
(4, 'Celeste'),
(5, 'Popular');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Poll`
--

CREATE TABLE IF NOT EXISTS `Poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `Poll`
--

INSERT INTO `Poll` (`id`, `name`, `slug`, `status`, `created`, `modified`) VALUES
(1, '¿Dónde Preferís Vacacionar?', 'dnde-prefers-vacacionar', 'active', '2014-11-12 15:56:51', '2014-11-13 00:07:06'),
(2, '¿Quién es el mejor jugador de fútbol?', 'quin-es-el-mejor-jugador-de-ftbol', 'active', '2014-11-12 23:08:12', '2014-11-13 00:29:11'),
(3, '¿Qué te pareció el partido de la selección ante Portugal?', 'qu-te-pareci-el-partido-de-la-seleccin-ante-portugal', 'inactive', '2014-11-13 00:30:05', '2014-11-13 00:30:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dni` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `associate_id` int(11) DEFAULT NULL,
  `role` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `associate_id` (`associate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `User`
--

INSERT INTO `User` (`id`, `name`, `email`, `password`, `salt`, `created`, `modified`, `last_name`, `dni`, `associate_id`, `role`) VALUES
(1, 'Esteban22', 'esteban.negri@gmail.com', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', '744212334546363444f72b', '2014-11-05 00:00:00', '2014-11-12 10:40:20', 'iii', '29684090', NULL, 'admin'),
(2, 'Juan', 'e.stebannegri@gmail.com', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', '1803141800546250ae464ce', '2014-11-05 00:00:00', '2014-11-11 15:08:46', 'Perez', '29999999', NULL, 'admin'),
(3, 'Juancito', 'e.st.ebannegri@gmail.com', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', '998458114546253b517b18', '2014-11-11 15:21:41', '2014-11-11 15:21:41', 'perez', '123123123', 2, 'associate');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Vote`
--

CREATE TABLE IF NOT EXISTS `Vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_poll` int(11) NOT NULL,
  `id_answer` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `Vote`
--

INSERT INTO `Vote` (`id`, `id_poll`, `id_answer`, `id_user`, `created`) VALUES
(1, 1, 2, 1, '2014-11-13 00:28:38');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `User_ibfk_1` FOREIGN KEY (`associate_id`) REFERENCES `Associate` (`id`) ON DELETE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
