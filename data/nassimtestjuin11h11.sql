-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 24 Juin 2015 à 11:11
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
-- Structure de la table `droit`
--

CREATE TABLE IF NOT EXISTS `droit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lenom` varchar(45) DEFAULT NULL,
  `laperm` smallint(5) unsigned DEFAULT '2',
  PRIMARY KEY (`id`),
  UNIQUE KEY `lenom_UNIQUE` (`lenom`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `droit`
--

INSERT INTO `droit` (`id`, `lenom`, `laperm`) VALUES
(1, 'Administrateur', 0),
(2, 'Modérateur', 1),
(3, 'Utilisateur', 2);

-- --------------------------------------------------------

--
-- Structure de la table `photo`
--

CREATE TABLE IF NOT EXISTS `photo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lenom` varchar(50) DEFAULT NULL,
  `lextension` char(3) DEFAULT NULL,
  `lepoids` int(10) unsigned DEFAULT NULL,
  `lalargeur` int(10) unsigned DEFAULT NULL,
  `lahauter` int(10) unsigned DEFAULT NULL,
  `letitre` varchar(45) DEFAULT NULL,
  `ladesc` smallint(6) DEFAULT NULL,
  `affiche` int(10) unsigned DEFAULT '2',
  `utilisateur_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lenom_UNIQUE` (`lenom`),
  KEY `fk_photo_utilisateur1_idx` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `rubriques`
--

CREATE TABLE IF NOT EXISTS `rubriques` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lintitule` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

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
(8, 'Transports'),
(9, 'Divers');

-- --------------------------------------------------------

--
-- Structure de la table `rubriques_has_photo`
--

CREATE TABLE IF NOT EXISTS `rubriques_has_photo` (
  `rubriques_id` int(10) unsigned NOT NULL,
  `photo_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`rubriques_id`,`photo_id`),
  KEY `fk_rubriques_has_photo_photo1_idx` (`photo_id`),
  KEY `fk_rubriques_has_photo_rubriques1_idx` (`rubriques_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lelogin` varchar(100) DEFAULT NULL,
  `lepass` varchar(45) DEFAULT NULL,
  `lemail` varchar(150) DEFAULT NULL,
  `lenom` varchar(80) DEFAULT NULL,
  `valide` tinyint(3) unsigned DEFAULT '1',
  `droit_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lelogin_UNIQUE` (`lelogin`),
  KEY `fk_utilisateur_droit_idx` (`droit_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `lelogin`, `lepass`, `lemail`, `lenom`, `valide`, `droit_id`) VALUES
(1, 'admin', 'admin', 'nassimelhadj@gmail.com', 'Super Admin', 1, 1),
(2, 'modo', 'modo', 'modo@truc.be', 'MR le modo', 1, 2),
(3, 'util1', 'util1', 'util1@truc.be', 'utilisateur1', 1, 3),
(4, 'util2', 'util2', 'util2@truc.be', 'utilisateur2', 1, 3);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `fk_photo_utilisateur1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `rubriques_has_photo`
--
ALTER TABLE `rubriques_has_photo`
  ADD CONSTRAINT `fk_rubriques_has_photo_rubriques1` FOREIGN KEY (`rubriques_id`) REFERENCES `rubriques` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rubriques_has_photo_photo1` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `fk_utilisateur_droit` FOREIGN KEY (`droit_id`) REFERENCES `droit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
