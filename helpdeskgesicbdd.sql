-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 09 août 2026 à 11:16
-- Version du serveur : 8.3.0
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `helpdeskgesicbdd`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie_ticket`
--

DROP TABLE IF EXISTS `categorie_ticket`;
CREATE TABLE IF NOT EXISTS `categorie_ticket` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) NOT NULL,
  `valeur_bloquant` int NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie_ticket`
--

INSERT INTO `categorie_ticket` (`id_categorie`, `libelle`, `valeur_bloquant`) VALUES
(1, 'Le logiciel ne fonctionneplus/ est bloqué', 4),
(2, 'Une fonctionnalité ne marche pas correctemen3', 3),
(3, 'Problème d\'installation ou de mise à jour', 2),
(4, 'J\'ai besoin d\'une nouvelle fonctionnalité', 1),
(5, 'J\'ai une question sur l\'utilisation', 1);

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id_client` int NOT NULL AUTO_INCREMENT,
  `raison_social` varchar(150) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `num_tel` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `positionX` decimal(10,7) DEFAULT NULL,
  `positionY` decimal(10,7) DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `top_actif` tinyint NOT NULL,
  PRIMARY KEY (`id_client`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id_client`, `raison_social`, `adresse`, `ville`, `code_postal`, `num_tel`, `email`, `positionX`, `positionY`, `date_creation`, `top_actif`) VALUES
(1, 'Urcoopa', '36 Avenue Du Grand Piton, Cambaie', 'Saint-Paul', '97460', '0262453710', 'urcoopa@urcoopa.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(2, 'Proval', '6 Rue Claude Chappe, ZI de Cambaie', 'Le Port', '97420', '0262453700', 'contact@proval.re', NULL, NULL, '2026-06-09 22:29:33', 1),
(3, 'Couvée d\'or', '36 Rue Montaigne, Les Trois Mares', 'Le Tampon', '97430', '0262395574', 'sa.couvee.d.or@wanadoo.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(4, 'Nutrima Production', 'ZAC 2000, 8 Rue Claude Chappe', 'Le Port', '97420', '0262453705', 'contact@nutrima.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(5, 'Petfood Run', 'ZI de Cambaie, CS 70003', 'Saint-Paul', '97460', '0262453707', 'petfoodrun@petfoodrun.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(6, 'Gésic', '21b Rue des Baies Roses, Cambaie', 'Saint-Paul', '97419', '0262457328', 'contact@gesic.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(7, 'PLR', 'Zone Industrielle n°1', 'Le Port', '97420', '0262551234', 'contact@plr-transport.re', NULL, NULL, '2026-06-09 22:29:33', 1),
(8, 'Cogedal', 'Zone Industrielle n°2, BP 188', 'Saint-Pierre', '97410', '0262961656', 'contact@cogedal.re', NULL, NULL, '2026-06-09 22:29:33', 1);

-- --------------------------------------------------------

--
-- Structure de la table `compte_rendu`
--

DROP TABLE IF EXISTS `compte_rendu`;
CREATE TABLE IF NOT EXISTS `compte_rendu` (
  `id_compte_rendu` int NOT NULL AUTO_INCREMENT,
  `contenu` longtext NOT NULL,
  `temps_traitement_minutes` int DEFAULT NULL,
  `date_redaction` datetime NOT NULL,
  `id_ticket` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_compte_rendu`),
  UNIQUE KEY `UNIQ_D39E69D2B197184E` (`id_ticket`),
  KEY `IDX_D39E69D250EAE44` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260603121023', '2026-06-03 12:11:30', 999),
('DoctrineMigrations\\Version20260603092459', NULL, NULL),
('DoctrineMigrations\\Version20260605092333', NULL, NULL),
('DoctrineMigrations\\Version20260605093053', '2026-06-05 09:31:12', 1332),
('DoctrineMigrations\\Version20260609120733', '2026-06-09 12:07:45', 1152),
('DoctrineMigrations\\Version20260609185314', '2026-06-09 18:53:23', 1870),
('DoctrineMigrations\\Version20260609185457', '2026-06-09 18:55:06', 1985),
('DoctrineMigrations\\Version20260609191026', '2026-06-09 19:10:38', 1543),
('DoctrineMigrations\\Version20260609202003', '2026-06-09 20:20:27', 1433),
('DoctrineMigrations\\Version20260615080437', '2026-06-15 08:04:46', 1051),
('DoctrineMigrations\\Version20260618072050', '2026-06-18 07:21:07', 1022),
('DoctrineMigrations\\Version20260706052926', '2026-07-06 05:29:45', 962);

-- --------------------------------------------------------

--
-- Structure de la table `histo_statut_ticket`
--

DROP TABLE IF EXISTS `histo_statut_ticket`;
CREATE TABLE IF NOT EXISTS `histo_statut_ticket` (
  `id_historique` int NOT NULL AUTO_INCREMENT,
  `date_changement` datetime NOT NULL,
  `commentaire` varchar(255) DEFAULT NULL,
  `id_ticket` int NOT NULL,
  `id_statut_avt` int NOT NULL,
  `id_statut_ap` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_historique`),
  KEY `IDX_D3E64762B197184E` (`id_ticket`),
  KEY `IDX_D3E64762519C1F35` (`id_statut_avt`),
  KEY `IDX_D3E64762A416DA56` (`id_statut_ap`),
  KEY `IDX_D3E6476250EAE44` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `impact`
--

DROP TABLE IF EXISTS `impact`;
CREATE TABLE IF NOT EXISTS `impact` (
  `id_impact` int NOT NULL AUTO_INCREMENT,
  `niveau` varchar(10) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `prompt` longtext NOT NULL,
  `note` smallint NOT NULL,
  PRIMARY KEY (`id_impact`),
  UNIQUE KEY `UNIQ_C409C0074BDFF36B` (`niveau`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `impact`
--

INSERT INTO `impact` (`id_impact`, `niveau`, `libelle`, `prompt`, `note`) VALUES
(1, 'I1', 'Majeur', 'Toute l\'entreprise ou service(s) critiques', 4),
(2, 'I2', 'Important', 'Un département entier', 3),
(3, 'I3', 'Modéré', 'Une équipe ou plusieurs Utilisateurs', 2),
(4, 'I4', 'Mineur', 'Un seul Utilisateur', 1);

-- --------------------------------------------------------

--
-- Structure de la table `logiciel`
--

DROP TABLE IF EXISTS `logiciel`;
CREATE TABLE IF NOT EXISTS `logiciel` (
  `id_logiciel` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `description` longtext,
  `fk_type_logiciel` varchar(80) DEFAULT NULL,
  `coeff_criticite` decimal(3,1) NOT NULL,
  `top_actif` tinyint NOT NULL,
  PRIMARY KEY (`id_logiciel`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `logiciel`
--

INSERT INTO `logiciel` (`id_logiciel`, `libelle`, `description`, `fk_type_logiciel`, `coeff_criticite`, `top_actif`) VALUES
(1, 'Progiplus', 'description de progiplus', 'ERP', 1.0, 1),
(2, 'ComptoirPro', 'ERP', 'ERP', 1.0, 1),
(3, 'Geswin', 'Logiciel de gestion', 'ERP', 1.0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `logiciel_client`
--

DROP TABLE IF EXISTS `logiciel_client`;
CREATE TABLE IF NOT EXISTS `logiciel_client` (
  `id_client_logiciel` int NOT NULL AUTO_INCREMENT,
  `date_installation` date DEFAULT NULL,
  `version_logiciel` varchar(20) DEFAULT NULL,
  `notes` longtext,
  `date_creation` datetime NOT NULL,
  `id_client` int NOT NULL,
  `id_logiciel` int NOT NULL,
  PRIMARY KEY (`id_client_logiciel`),
  KEY `IDX_1A45CC94E173B1B8` (`id_client`),
  KEY `IDX_1A45CC947C8B8A09` (`id_logiciel`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `logiciel_client`
--

INSERT INTO `logiciel_client` (`id_client_logiciel`, `date_installation`, `version_logiciel`, `notes`, `date_creation`, `id_client`, `id_logiciel`) VALUES
(1, NULL, 'V1.0', NULL, '2026-06-09 20:31:12', 1, 1),
(2, NULL, 'V1.0', NULL, '2026-06-09 20:31:12', 2, 1),
(3, NULL, 'V1.0', NULL, '2026-06-09 20:31:12', 5, 1),
(4, NULL, 'V1.0', NULL, '2026-06-09 21:02:49', 1, 2),
(5, NULL, 'V1.0', NULL, '2026-06-09 21:02:49', 2, 2),
(6, NULL, 'V1.0', NULL, '2026-06-09 21:02:49', 7, 2),
(7, NULL, 'V1.1', NULL, '2026-06-09 21:03:22', 2, 1),
(8, NULL, 'V1.1', NULL, '2026-06-09 21:03:22', 3, 1),
(9, NULL, 'V1.1', NULL, '2026-06-09 21:03:22', 5, 1),
(10, NULL, 'V1.1', NULL, '2026-06-09 21:03:22', 6, 1),
(11, NULL, 'V1.5', NULL, '2026-06-15 07:16:49', 7, 1);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `contenu` longtext NOT NULL,
  `top_actif` tinyint NOT NULL,
  `date_envoi` datetime NOT NULL,
  `id_ticket` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `id_message_parent` int DEFAULT NULL,
  PRIMARY KEY (`id_message`),
  KEY `IDX_B6BD307FB197184E` (`id_ticket`),
  KEY `IDX_B6BD307F50EAE44` (`id_utilisateur`),
  KEY `IDX_B6BD307F37CF4046` (`id_message_parent`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id_message`, `contenu`, `top_actif`, `date_envoi`, `id_ticket`, `id_utilisateur`, `id_message_parent`) VALUES
(1, 'Ou en est l\'avancement du problève svp ?', 1, '2026-06-24 06:39:32', 8, 8, NULL),
(2, 'je termine la modification de la tare dans la matinée et je vous apel', 1, '2026-06-24 06:40:50', 8, 6, NULL),
(3, 'je m\'en occupe', 1, '2026-06-26 10:59:27', 6, 6, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `priorite`
--

DROP TABLE IF EXISTS `priorite`;
CREATE TABLE IF NOT EXISTS `priorite` (
  `id_priorite` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  `niveau_criticite` int NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_priorite`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `priorite`
--

INSERT INTO `priorite` (`id_priorite`, `libelle`, `niveau_criticite`, `description`) VALUES
(1, 'URGENTE', 1, '>=17'),
(2, 'HAUTE', 2, '12 à 16'),
(3, 'NORMALE', 3, '8 à 11'),
(4, 'BASSE', 4, '3 à 7'),
(5, 'TRÈS BASSE', 5, '1 à 2');

-- --------------------------------------------------------

--
-- Structure de la table `statut_ticket`
--

DROP TABLE IF EXISTS `statut_ticket`;
CREATE TABLE IF NOT EXISTS `statut_ticket` (
  `id_statut` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  `couleur_lib` varchar(7) NOT NULL,
  `top_actif` tinyint NOT NULL,
  PRIMARY KEY (`id_statut`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tache`
--

DROP TABLE IF EXISTS `tache`;
CREATE TABLE IF NOT EXISTS `tache` (
  `id_tache` int NOT NULL AUTO_INCREMENT,
  `date_creation` datetime NOT NULL,
  `libelle` varchar(200) NOT NULL,
  `description` longtext,
  `date_realisation` datetime DEFAULT NULL,
  `statut` varchar(255) NOT NULL,
  `id_ticket` int DEFAULT NULL,
  `id_utilisateur_assign` int DEFAULT NULL,
  `id_utilisateur_creat` int NOT NULL,
  PRIMARY KEY (`id_tache`),
  KEY `IDX_93872075B197184E` (`id_ticket`),
  KEY `IDX_9387207590AD2185` (`id_utilisateur_assign`),
  KEY `IDX_938720758BE64456` (`id_utilisateur_creat`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `tache`
--

INSERT INTO `tache` (`id_tache`, `date_creation`, `libelle`, `description`, `date_realisation`, `statut`, `id_ticket`, `id_utilisateur_assign`, `id_utilisateur_creat`) VALUES
(1, '2026-07-06 09:45:50', 'test', 'test', NULL, 'realisee', 1, 1, 1),
(2, '2026-07-06 09:54:36', 'Redémarrage server-Db8', 'redemarrage', NULL, 'realisee', 1, 1, 1),
(3, '2026-07-06 11:33:00', 'Changement cartouche', 'Concerne serveur AS400', NULL, 'realisee', 1, 1, 1),
(4, '2026-07-06 11:33:47', 'Sauvegarde', 'transfert des fichiers de sauvegarde', NULL, 'realisee', 1, 1, 1),
(5, '2026-07-07 08:35:17', 'Inventaire stock materiels', 'inventaire dczedcz dezdczedc edczedcedczdc zdczdcze zdcdc zdcdczdc', NULL, 'realisee', 1, 1, 1),
(6, '2026-07-07 08:39:52', 'tache test', 'nouvelle tache hebdomadaire', NULL, 'a_faire', 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
CREATE TABLE IF NOT EXISTS `ticket` (
  `id_ticket` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `description` longtext NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `id_utilisateur` int NOT NULL,
  `id_assigne` int DEFAULT NULL,
  `id_destinataire` int DEFAULT NULL,
  `id_logiciel_client` int NOT NULL,
  `priorite_calculee` smallint NOT NULL,
  `id_impact` int NOT NULL,
  `id_urgence` int NOT NULL,
  `statut` varchar(255) NOT NULL,
  PRIMARY KEY (`id_ticket`),
  KEY `IDX_97A0ADA350EAE44` (`id_utilisateur`),
  KEY `IDX_97A0ADA390E462FD` (`id_assigne`),
  KEY `IDX_97A0ADA3DD688AE0` (`id_destinataire`),
  KEY `IDX_97A0ADA3F31B0F1C` (`id_logiciel_client`),
  KEY `IDX_97A0ADA3E23E75EA` (`id_impact`),
  KEY `IDX_97A0ADA3ADE0C2BD` (`id_urgence`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `ticket`
--

INSERT INTO `ticket` (`id_ticket`, `titre`, `description`, `date_creation`, `date_cloture`, `id_utilisateur`, `id_assigne`, `id_destinataire`, `id_logiciel_client`, `priorite_calculee`, `id_impact`, `id_urgence`, `statut`) VALUES
(1, 'module Achat', 'Vestibulum volutpat purus cursus, pharetra leo et, tincidunt nisl. Integer congue sagittis lorem. In tempor venenatis neque, eu consectetur leo condimentum vitae. Sed lobortis nisl auctor ipsum vulputate pulvinar. Nullam venenatis lacinia risus, sed aliquam magna tempor eu. Sed molestie venenatis ex non rhoncus. Ut eleifend consequat aliquet.', '2026-06-15 08:14:59', NULL, 2, 6, NULL, 1, 4, 1, 2, 'en_cours'),
(2, 'Gestion stock', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus dignissim, nulla et efficitur vestibulum, dui metus vulputate felis, nec mollis mi nisi eu magna.', '2026-06-16 08:52:53', NULL, 0, 6, NULL, 2, 0, 2, 3, 'en_cours'),
(3, 'problème validation stock', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus dignissim, nulla et efficitur vestibulum, dui metus vulputate felis, nec mollis mi nisi eu magna.', '2026-06-16 10:03:49', NULL, 1, 1, NULL, 2, 0, 2, 2, 'en_cours'),
(4, 'Gestion stock non controlé', 'test', '2026-06-16 10:32:42', NULL, 1, 1, NULL, 2, 17, 4, 1, 'en_cours'),
(5, 'oubli mot de passe', 'test mdp', '2026-06-16 10:33:34', NULL, 1, 6, NULL, 2, 3, 4, 3, 'en_cours'),
(6, 'echec de connexion', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam interdum diam ut nibh porta, eu rhoncus nisi finibus. Proin urna lorem, dictum nec rutrum et, ullamcorper nec metus.', '2026-06-18 12:00:23', NULL, 6, 6, NULL, 10, 12, 2, 2, 'fermé'),
(7, 'test mercure', 'test mercure', '2026-06-18 19:14:14', NULL, 6, 6, NULL, 10, 6, 2, 4, 'en_cours'),
(8, 'tonnage incorrect', 'Il y a un écart entre la quantité pesé et la quantité affiché', '2026-06-24 05:44:00', NULL, 8, 6, NULL, 2, 8, 3, 2, 'en_cours'),
(9, 'Problème de mise à jour', 'Mise à jour bloqué sur chargement', '2026-07-07 05:40:04', NULL, 6, NULL, NULL, 10, 4, 4, 2, 'nouveau');

-- --------------------------------------------------------

--
-- Structure de la table `urgence`
--

DROP TABLE IF EXISTS `urgence`;
CREATE TABLE IF NOT EXISTS `urgence` (
  `id_urgence` int NOT NULL AUTO_INCREMENT,
  `niveau` varchar(10) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `prompt` longtext NOT NULL,
  `note` smallint NOT NULL,
  PRIMARY KEY (`id_urgence`),
  UNIQUE KEY `UNIQ_737D6BCD4BDFF36B` (`niveau`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `urgence`
--

INSERT INTO `urgence` (`id_urgence`, `niveau`, `libelle`, `prompt`, `note`) VALUES
(1, 'U1', 'Critique métier', 'Production arrêtée, risque financier important', 17),
(2, 'U2', 'Immédiat', 'Fonction essentiel bloquée', 4),
(3, 'U3', 'À traiter rapidement', 'Fonction secondaire indisponible', 3),
(4, 'U4', 'À traiter prochainement', 'Demande de confort ou d\'amélioration', 2),
(5, 'U5', 'Peut attendre', 'Non urgente, à planifier', 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(80) NOT NULL,
  `prenom` varchar(80) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_creation` datetime NOT NULL,
  `top_actif` tinyint NOT NULL,
  `num_tel` varchar(20) DEFAULT NULL,
  `id_client` int DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `UNIQ_1D1C63B3E7927C74` (`email`),
  KEY `IDX_1D1C63B3E173B1B8` (`id_client`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `nom`, `prenom`, `email`, `role`, `mot_de_passe`, `date_creation`, `top_actif`, `num_tel`, `id_client`) VALUES
(1, 'Perny', 'Yannick', 'yannick.pernygesic.fr', 'ROLE_DEVELOPPEUR', '123456', '2026-06-09 18:58:42', 1, '0606060606', 6),
(2, 'Noel', 'Thomy', 'thomy.noel@gesic.fr', 'ROLE_DEVELOPPEUR', '123456', '2026-06-09 19:11:46', 1, '0698556622', 6),
(3, 'Gence', 'Patrice', 'patrice.gence@gesic.fr', 'ROLE_ADMIN', '123456', '2026-06-09 19:16:42', 1, '0698556622', 6),
(4, 'Tang', 'Eric', 'eric.tang@gesic.fr', 'ROLE_DEVELOPPEUR', '$2y$13$JAjJVpHbjpmF2Gthq3yVhOZaxl12I.GVYxT1pBHOb/w/SVYOtgoSS', '2026-06-09 19:17:57', 1, '0689556655', 6),
(5, 'Payet', 'Myrela', 'myrela.payet@urcoopa.fr', 'ROLE_CLIENT', '123456', '2026-06-09 19:20:19', 1, '0635998855', 1),
(6, 'Erick', 'Grondin', 'admin@admin.fr', 'ROLE_DEVELOPPEUR', '$2y$13$JAjJVpHbjpmF2Gthq3yVhOZaxl12I.GVYxT1pBHOb/w/SVYOtgoSS', '2026-06-18 07:41:29', 0, '0699332211', 6),
(7, 'Jean', 'dupont', 'jd@jd.fr', 'ROLE_CLIENT', '$2y$13$xyZqzPnhDzcqID3Klb9lzO0YA45Q3kUKv1GzT4Op0XYFA6H9gmzEm', '2026-06-18 09:48:08', 1, '0265998877', 8),
(8, 'jean', 'blond', 'jean@client.fr', 'ROLE_CLIENT', '$2y$13$o3RLDkXaL9dlQ0sZ1SPvGuufmekJnhra7lNEUEJEiokI0GA0g908y', '2026-06-24 05:35:58', 1, '0699332211', 2),
(9, 'Fontaine', 'Arnauld', 'arnauld.fontaine@gesic.fr', 'ROLE_DEVELOPPEUR', '$2y$13$XhBUGy7bOhELrIjZK6N7CuCFXKqghEQtt.6vP.p/AgipSgKMuN2j2', '2026-06-26 11:31:27', 1, '0666998877', 6);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
