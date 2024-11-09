-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : sam. 09 nov. 2024 à 10:11
-- Version du serveur : 10.5.23-MariaDB-0+deb11u1
-- Version de PHP : 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dolibarrdebian`
--

-- --------------------------------------------------------

--
-- Structure de la table `llx_custom_urssaf`
--

DROP TABLE IF EXISTS `llx_custom_urssaf`;
CREATE TABLE `llx_custom_urssaf` (
  `dates` datetime NOT NULL DEFAULT current_timestamp(),
  `periode` varchar(6) NOT NULL,
  `tx_518` float NOT NULL,
  `tx_508` float NOT NULL,
  `tx_520` float NOT NULL,
  `tx_510` float NOT NULL,
  `tx_572` float NOT NULL,
  `tx_060` float NOT NULL,
  `tx_061` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `llx_custom_urssaf`
--

INSERT INTO `llx_custom_urssaf` (`dates`, `periode`, `tx_518`, `tx_508`, `tx_520`, `tx_510`, `tx_572`, `tx_060`, `tx_061`) VALUES
('2024-11-06 14:21:41', '2008-1', 23, 12.3, 0, 0, 0, 0.22, 0),
('2024-11-06 14:21:41', '2011-1', 23, 12.3, 0, 0, 0.3, 0.22, 0),
('2024-11-06 14:21:41', '2013-1', 26.3, 12.3, 0, 0, 0.3, 0.22, 0),
('2024-11-06 14:21:41', '2015-1', 24.6, 12.3, 0, 0, 0.3, 0.22, 0.48),
('2024-11-06 14:21:41', '2016-1', 24.8, 12.3, 0, 0, 0.3, 0.22, 0.48),
('2024-11-06 14:21:41', '2017-1', 24.4, 14.1, 0, 0, 0.3, 0.22, 0.48),
('2024-11-06 14:21:41', '2018-1', 23.7, 13.8, 0, 0, 0.3, 0.22, 0.48),
('2024-11-06 14:21:41', '2018-2', 22, 12.8, 0, 0, 0.3, 0.22, 0),
('2024-11-06 14:21:41', '2018-3', 22, 12.8, 0, 0, 0.3, 0.22, 0.48),
('2024-11-06 14:21:41', '2018-4', 23.7, 13.8, 0, 0, 0.3, 0.22, 0.48),
('2024-11-06 14:21:41', '2019-1', 23.7, 13.8, 0, 0, 0.3, 0.22, 0),
('2024-11-06 14:21:41', '2020-1', 23.7, 13.8, 0, 0, 0.3, 0.22, 0),
('2024-11-06 14:21:41', '2021-1', 23.7, 13.8, 0, 0, 0.3, 0.22, 0.48),
('2024-11-06 14:21:41', '2022-1', 22, 12.8, 1.7, 1, 0.3, 0.22, 0.48),
('2024-11-06 14:21:41', '2022-4', 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48),
('2024-11-06 14:21:41', '2023-1', 21.2, 12.3, 1.7, 1, 0.3, 0.22, 0.48);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `llx_custom_urssaf`
--
ALTER TABLE `llx_custom_urssaf`
  ADD PRIMARY KEY (`periode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
