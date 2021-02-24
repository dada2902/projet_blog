-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mer. 16 déc. 2020 à 14:00
-- Version du serveur :  5.7.21
-- Version de PHP :  7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projet_blog`
--
CREATE DATABASE IF NOT EXISTS `projet_blog` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `projet_blog`;

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id_article` int(3) NOT NULL AUTO_INCREMENT,
  `id_membre` int(3) DEFAULT NULL,
  `titre` varchar(255) NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  `contenu` text NOT NULL,
  `image_principale` varchar(255) NOT NULL,
  `etat` enum('public','brouillon','archive') NOT NULL DEFAULT 'public',
  PRIMARY KEY (`id_article`),
  KEY `id_membre` (`id_membre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `avatar`
--

DROP TABLE IF EXISTS `avatar`;
CREATE TABLE IF NOT EXISTS `avatar` (
  `id_avatar` int(3) NOT NULL AUTO_INCREMENT,
  `avatar_src` varchar(255) NOT NULL,
  PRIMARY KEY (`id_avatar`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int(3) NOT NULL AUTO_INCREMENT,
  `titre_categorie` varchar(255) NOT NULL,
  `description_categorie` text NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

DROP TABLE IF EXISTS `commentaire`;
CREATE TABLE IF NOT EXISTS `commentaire` (
  `id_commentaire` int(3) NOT NULL AUTO_INCREMENT,
  `id_membre` int(3) DEFAULT NULL,
  `id_article` int(3) NOT NULL,
  `message` text NOT NULL,
  `date_commentaire` datetime NOT NULL,
  PRIMARY KEY (`id_commentaire`),
  KEY `id_membre` (`id_membre`),
  KEY `id_article` (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

DROP TABLE IF EXISTS `membre`;
CREATE TABLE IF NOT EXISTS `membre` (
  `id_membre` int(3) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_avatar` int(3) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `sexe` enum('m','f') NOT NULL,
  `statut` int(3) NOT NULL,
  PRIMARY KEY (`id_membre`),
  UNIQUE KEY `pseudo` (`pseudo`),
  UNIQUE KEY `email` (`email`),
  KEY `avatar` (`id_avatar`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `mot_cle`
--

DROP TABLE IF EXISTS `mot_cle`;
CREATE TABLE IF NOT EXISTS `mot_cle` (
  `id_mot_cle` int(3) NOT NULL AUTO_INCREMENT,
  `titre_mot_cle` varchar(255) NOT NULL,
  `description_mot_cle` text NOT NULL,
  PRIMARY KEY (`id_mot_cle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `relation_article_categorie`
--

DROP TABLE IF EXISTS `relation_article_categorie`;
CREATE TABLE IF NOT EXISTS `relation_article_categorie` (
  `id_relation_article_categorie` int(3) NOT NULL AUTO_INCREMENT,
  `id_categorie` int(3) NOT NULL,
  `id_article` int(3) NOT NULL,
  PRIMARY KEY (`id_relation_article_categorie`),
  KEY `id_categorie` (`id_categorie`),
  KEY `id_article` (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `relation_article_mot_cle`
--

DROP TABLE IF EXISTS `relation_article_mot_cle`;
CREATE TABLE IF NOT EXISTS `relation_article_mot_cle` (
  `id_relation_article_mot_cle` int(3) NOT NULL AUTO_INCREMENT,
  `id_mot_cle` int(3) NOT NULL,
  `id_article` int(3) NOT NULL,
  PRIMARY KEY (`id_relation_article_mot_cle`),
  KEY `id_mot_cle` (`id_mot_cle`),
  KEY `id_article` (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`id_membre`) REFERENCES `membre` (`id_membre`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `commentaire_ibfk_1` FOREIGN KEY (`id_membre`) REFERENCES `membre` (`id_membre`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `commentaire_ibfk_2` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `membre`
--
ALTER TABLE `membre`
  ADD CONSTRAINT `membre_ibfk_1` FOREIGN KEY (`id_avatar`) REFERENCES `avatar` (`id_avatar`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `relation_article_categorie`
--
ALTER TABLE `relation_article_categorie`
  ADD CONSTRAINT `relation_article_categorie_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relation_article_categorie_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `relation_article_mot_cle`
--
ALTER TABLE `relation_article_mot_cle`
  ADD CONSTRAINT `relation_article_mot_cle_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relation_article_mot_cle_ibfk_2` FOREIGN KEY (`id_mot_cle`) REFERENCES `mot_cle` (`id_mot_cle`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
