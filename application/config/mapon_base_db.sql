-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2016 a las 12:30:37
-- Versión del servidor: 10.1.10-MariaDB
-- Versión de PHP: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mapon`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) COLLATE utf8_bin NOT NULL,
  `description` varchar(100) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator'),
(2, 'members', 'General User');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `database_name` varchar(256) NOT NULL,
  `directory_name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `ip_address` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) COLLATE utf8_bin NOT NULL,
  `password` varchar(40) COLLATE utf8_bin NOT NULL,
  `salt` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `activation_code` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `forgotten_password_code` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `remember_code` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `company` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_bin DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
(1, 2130706433, 'administrator', '59beecdf7fc966e2f17fd8f65a4a9aeb09d4a3d4', '9462e8eee0', 'admin@admin.com', '', NULL, NULL, 1268889823, 1331581662, 1, 'Admin', 'istrator', 'ADMIN', '0'),
(2, 1362921840, 'alvaro sicilia', 'aadb736bf9c808fdc937d8f9aca3b14b2b81aeef', NULL, 'asicilia@salleurl.edu', NULL, NULL, '81ff418c437a32667ef44ab6026c593acd427cce', 1331581806, 1460532708, 1, 'Alvaro', 'Sicilia', 'ARC', '234-234-2343'),
(3, 2886730810, 'andreas nolle', '984078b7e53060a35cfd0150614d8975f911cb42', NULL, 'nolle@hs-albsig.de', NULL, NULL, NULL, 1353425566, 1354866160, 1, 'Andreas', 'Nolle', 'HAS', '666-666-6666'),
(4, 2886730810, 'martin carpenter', '32cad544b8164c7ec349732a7b4baf56eccd2037', NULL, 'm.carpenter@tees.ac.uk', NULL, NULL, NULL, 1353425687, 1353427456, 1, 'Martin', 'Carpenter', 'Teeside', '666-666-6666'),
(5, 2886730810, 'tomas karlsson', '13456ab0dec297d6697d2ba6357bb64988479e7e', NULL, 'tomas.karlsson@agency9.com', NULL, NULL, NULL, 1353425745, 1353425752, 1, 'Tomas', 'Karlsson', 'Agency9', '666-666-6666'),
(6, 2886730972, 'eric ortet', 'fdb1562b6a5fcdf75118bc8fba47ef93c75f644a', NULL, 'netkuup@gmail.com', NULL, NULL, '0a52a796110f15889a654f3a5e0715a6c045049f', 1449828547, 1462954325, 1, 'Eric', 'Ortet', 'ARC', '423-234-2342');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_groups`
--

CREATE TABLE `users_groups` (
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `users_groups`
--

INSERT INTO `users_groups` (`user_id`, `group_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 1),
(6, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_teams`
--

CREATE TABLE `users_teams` (
  `user_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`user_id`,`group_id`);

--
-- Indices de la tabla `users_teams`
--
ALTER TABLE `users_teams`
  ADD PRIMARY KEY (`user_id`,`team_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
