-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2016 a las 11:34:11
-- Versión del servidor: 10.1.10-MariaDB
-- Versión de PHP: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `mapon_teamName`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuration`
--

CREATE TABLE `configuration` (
  `name` varchar(256) NOT NULL,
  `value` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Volcado de datos para la tabla `configuration`
--

INSERT INTO `configuration` (`name`, `value`) VALUES
('database_version', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datasource`
--

CREATE TABLE `datasource` (
  `id` int(11) NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `type` varchar(256) COLLATE utf8_bin NOT NULL,
  `sqlfile` varchar(256) COLLATE utf8_bin NOT NULL,
  `stringconnection` text COLLATE utf8_bin NOT NULL,
  `xmlfile` varchar(256) COLLATE utf8_bin NOT NULL,
  `basicuri` text COLLATE utf8_bin NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `ontology_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datasource_layout`
--

CREATE TABLE `datasource_layout` (
  `tableid` varchar(256) NOT NULL,
  `layoutX` int(11) NOT NULL,
  `layoutY` int(11) NOT NULL,
  `datasource_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `location` varchar(256) NOT NULL,
  `user_name` varchar(256) NOT NULL,
  `log_message` text NOT NULL,
  `action` varchar(256) NOT NULL,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mappedclass`
--

CREATE TABLE `mappedclass` (
  `id` int(11) NOT NULL,
  `class` varchar(256) COLLATE utf8_bin NOT NULL,
  `sql` text COLLATE utf8_bin NOT NULL,
  `uri` text COLLATE utf8_bin NOT NULL,
  `mappedtablecolumn` varchar(256) COLLATE utf8_bin NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `mappingspace_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mappedclass_layout`
--

CREATE TABLE `mappedclass_layout` (
  `id` int(11) NOT NULL,
  `nodeid` varchar(256) NOT NULL,
  `layoutX` int(11) NOT NULL,
  `layoutY` int(11) NOT NULL,
  `mappedclass_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mappedclass_tableson`
--

CREATE TABLE `mappedclass_tableson` (
  `id` int(11) NOT NULL,
  `tableid` varchar(256) NOT NULL,
  `mappedclass_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mappeddataproperty`
--

CREATE TABLE `mappeddataproperty` (
  `id` int(11) NOT NULL,
  `dataproperty` varchar(256) COLLATE utf8_bin NOT NULL,
  `value` varchar(256) COLLATE utf8_bin NOT NULL,
  `type` varchar(256) COLLATE utf8_bin NOT NULL,
  `mappedclass_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mappedobjectproperty`
--

CREATE TABLE `mappedobjectproperty` (
  `id` int(11) NOT NULL,
  `objectproperty` varchar(256) COLLATE utf8_bin NOT NULL,
  `uri` text COLLATE utf8_bin NOT NULL,
  `mappedclassdomain_id` int(11) NOT NULL,
  `mappedclassrange_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mappingspace`
--

CREATE TABLE `mappingspace` (
  `id` int(11) NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `datasource_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mappingspace_layout`
--

CREATE TABLE `mappingspace_layout` (
  `id` int(11) NOT NULL,
  `nodeid` varchar(256) NOT NULL,
  `layoutX` int(11) NOT NULL,
  `layoutY` int(11) NOT NULL,
  `mappingspace_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ontology`
--

CREATE TABLE `ontology` (
  `id` int(11) NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ontology_layout`
--

CREATE TABLE `ontology_layout` (
  `id` int(11) NOT NULL,
  `nodeid` varchar(256) NOT NULL,
  `layoutX` int(11) NOT NULL,
  `layoutY` int(11) NOT NULL,
  `datasource_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ontology_modules`
--

CREATE TABLE `ontology_modules` (
  `id` int(11) NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `file` text COLLATE utf8_bin NOT NULL,
  `url` text COLLATE utf8_bin NOT NULL,
  `ontology_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prefix`
--

CREATE TABLE `prefix` (
  `id` int(11) NOT NULL,
  `prefix` varchar(256) NOT NULL,
  `iri` varchar(256) NOT NULL,
  `ontology_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `r2rmlparts`
--

CREATE TABLE `r2rmlparts` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `datasource_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sourcecolumn`
--

CREATE TABLE `sourcecolumn` (
  `id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `type` varchar(128) COLLATE utf8_bin NOT NULL,
  `primarykey` int(11) NOT NULL,
  `foreignkey` varchar(128) COLLATE utf8_bin NOT NULL,
  `foreigntable` varchar(128) COLLATE utf8_bin NOT NULL,
  `sourcetable_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sourcetable`
--

CREATE TABLE `sourcetable` (
  `id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `datasource_id` int(11) NOT NULL,
  `layoutX` int(11) NOT NULL,
  `layoutY` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuration`
--
ALTER TABLE `configuration`
  ADD PRIMARY KEY (`name`);

--
-- Indices de la tabla `datasource`
--
ALTER TABLE `datasource`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ontology_id` (`ontology_id`);

--
-- Indices de la tabla `datasource_layout`
--
ALTER TABLE `datasource_layout`
  ADD PRIMARY KEY (`tableid`,`datasource_id`),
  ADD KEY `mappingspace_id` (`datasource_id`);

--
-- Indices de la tabla `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mappedclass`
--
ALTER TABLE `mappedclass`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mappingspace_id` (`mappingspace_id`);

--
-- Indices de la tabla `mappedclass_layout`
--
ALTER TABLE `mappedclass_layout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mappingspace_id` (`mappedclass_id`);

--
-- Indices de la tabla `mappedclass_tableson`
--
ALTER TABLE `mappedclass_tableson`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mappingspace_id` (`mappedclass_id`);

--
-- Indices de la tabla `mappeddataproperty`
--
ALTER TABLE `mappeddataproperty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mappedclass_id` (`mappedclass_id`);

--
-- Indices de la tabla `mappedobjectproperty`
--
ALTER TABLE `mappedobjectproperty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mappedclassdomain_id` (`mappedclassdomain_id`),
  ADD KEY `mappedclassrange_id` (`mappedclassrange_id`);

--
-- Indices de la tabla `mappingspace`
--
ALTER TABLE `mappingspace`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mappingspace_layout`
--
ALTER TABLE `mappingspace_layout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mappingspace_id` (`mappingspace_id`);

--
-- Indices de la tabla `ontology`
--
ALTER TABLE `ontology`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ontology_layout`
--
ALTER TABLE `ontology_layout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mappingspace_id` (`datasource_id`);

--
-- Indices de la tabla `ontology_modules`
--
ALTER TABLE `ontology_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ontology_id` (`ontology_id`);

--
-- Indices de la tabla `prefix`
--
ALTER TABLE `prefix`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ontology_id` (`ontology_id`);

--
-- Indices de la tabla `r2rmlparts`
--
ALTER TABLE `r2rmlparts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `datasource_id` (`datasource_id`,`user_id`);

--
-- Indices de la tabla `sourcecolumn`
--
ALTER TABLE `sourcecolumn`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sourcetable`
--
ALTER TABLE `sourcetable`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `datasource`
--
ALTER TABLE `datasource`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `mappedclass`
--
ALTER TABLE `mappedclass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `mappedclass_layout`
--
ALTER TABLE `mappedclass_layout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `mappedclass_tableson`
--
ALTER TABLE `mappedclass_tableson`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `mappeddataproperty`
--
ALTER TABLE `mappeddataproperty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `mappedobjectproperty`
--
ALTER TABLE `mappedobjectproperty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `mappingspace`
--
ALTER TABLE `mappingspace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `mappingspace_layout`
--
ALTER TABLE `mappingspace_layout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `ontology`
--
ALTER TABLE `ontology`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `ontology_layout`
--
ALTER TABLE `ontology_layout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `ontology_modules`
--
ALTER TABLE `ontology_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `prefix`
--
ALTER TABLE `prefix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `r2rmlparts`
--
ALTER TABLE `r2rmlparts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sourcecolumn`
--
ALTER TABLE `sourcecolumn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sourcetable`
--
ALTER TABLE `sourcetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `mappedclass`
--
ALTER TABLE `mappedclass`
  ADD CONSTRAINT `mappedclass_ibfk_2` FOREIGN KEY (`mappingspace_id`) REFERENCES `mappingspace` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
