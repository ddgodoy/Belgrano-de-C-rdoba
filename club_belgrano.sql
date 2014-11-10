-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 10-11-2014 a las 09:41:09
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
-- Estructura de tabla para la tabla `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dni` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `associate_id` int(11) DEFAULT NULL,
  `role` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `associate_id` (`associate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `User`
--

INSERT INTO `User` (`id`, `name`, `email`, `password`, `created`, `modified`, `last_name`, `dni`, `associate_id`, `role`) VALUES
(1, 'Esteban', 'esteban.negri@gmail.com', '', '2014-11-05 00:00:00', '2014-11-05 00:00:00', 'Negri', '2968409822', 1, 'admin'),
(2, 'Juan', 'e.stebannegri@gmail.com', 'd7683e52af93b105a44fcef5bd668a77fafd49f9', '2014-11-05 00:00:00', '2014-11-05 00:00:00', 'Perez', '2435456', 1, 'socio');

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
