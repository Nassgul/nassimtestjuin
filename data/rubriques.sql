-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 25 Juin 2015 à 12:34
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `nassimtestjuin`
--

-- --------------------------------------------------------

--
-- Structure de la table `rubriques`
--

CREATE TABLE IF NOT EXISTS `rubriques` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lintitule` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `rubriques`
--

INSERT INTO `rubriques` (`id`, `lintitule`) VALUES
(1, 'Animaux'),
(2, 'Architectures'),
(3, 'Artistiques'),
(4, 'Personnes'),
(5, 'Paysages'),
(6, 'Sports'),
(7, 'Technologies'),
(8, 'transports'),
(9, 'Divers');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
