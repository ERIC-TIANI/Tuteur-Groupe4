-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 13 juin 2025 à 11:52
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `immo_web`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id_review` int NOT NULL AUTO_INCREMENT,
  `note` int DEFAULT NULL,
  `commentaire` text,
  `date_review` date DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `id_property` int DEFAULT NULL,
  PRIMARY KEY (`id_review`),
  KEY `id_user` (`id_user`),
  KEY `id_property` (`id_property`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id_review`, `note`, `commentaire`, `date_review`, `id_user`, `id_property`) VALUES
(5, 1, 'trop naze', '2025-06-12', 4, 1),
(6, 1, 'Vraiment trop Zazz', '2025-06-12', 4, 2);

-- --------------------------------------------------------

--
-- Structure de la table `bienimmobilier`
--

DROP TABLE IF EXISTS `bienimmobilier`;
CREATE TABLE IF NOT EXISTS `bienimmobilier` (
  `id_property` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) DEFAULT NULL,
  `description` text,
  `type_bien` enum('appartement','villa','studio','chambre') NOT NULL,
  `type_annonce` enum('location','vente') NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `mode_tarif` enum('fixe','par_nuit') DEFAULT NULL,
  `superficie` float DEFAULT NULL,
  `nb_pieces` int DEFAULT NULL,
  `nb_lits` int DEFAULT NULL,
  `wifi` tinyint(1) DEFAULT '0',
  `climatisation` tinyint(1) DEFAULT '0',
  `cuisine` tinyint(1) DEFAULT '0',
  `parking` tinyint(1) DEFAULT '0',
  `date_ajout` date DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `id_categorie` int DEFAULT NULL,
  PRIMARY KEY (`id_property`),
  KEY `id_user` (`id_user`),
  KEY `id_categorie` (`id_categorie`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `bienimmobilier`
--

INSERT INTO `bienimmobilier` (`id_property`, `titre`, `description`, `type_bien`, `type_annonce`, `adresse`, `ville`, `pays`, `prix`, `mode_tarif`, `superficie`, `nb_pieces`, `nb_lits`, `wifi`, `climatisation`, `cuisine`, `parking`, `date_ajout`, `id_user`, `id_categorie`) VALUES
(1, 'Appartement meublé à Bonapriso', 'Bel appartement 2 chambres climatisées, idéal pour un court séjour à Douala.', 'appartement', 'location', 'Rue Koloko, Bonapriso', 'Douala', 'Cameroun', 30000.00, 'par_nuit', 85.5, 3, 2, 1, 1, 1, 1, '2025-06-12', 3, 2),
(2, 'Studio moderne à Bastos', 'Studio cosy avec coin cuisine, proche des ambassades à Yaoundé.', 'studio', 'location', 'Avenue Germaine, Bastos', 'Yaoundé', 'Cameroun', 20000.00, 'par_nuit', 45, 3, 3, 1, 0, 1, 0, '2025-06-12', 3, 1),
(3, 'Grande villa à vendre à Tamdja', 'Villa spacieuse avec jardin, 5 chambres et 3 salles de bain. Quartier calme à Bafoussam.', '', 'vente', 'Rue Tamdja, Quartier Tamdja', 'Bafoussam', 'Cameroun', 45000000.00, '', 220, 7, 5, 1, 1, 1, 1, '2025-06-12', 3, 3),
(6, 'Studion Ekounou S.A', 'Studio moderne situé dans un quartier calme de Yaoundé. un grand salon pour faire des receptions', '', 'location', 'Ekounou', 'yaounde', NULL, 50000.00, '', 400, 3, 0, 0, 0, 1, 0, '2025-06-13', 5, 1),
(7, 'wxcghvjbknl,mù', 'xc n,;:!;;:;,nbcvcxcn ,;,nkbjvhcgxffcgvhbn,jhcghxfwdfxcgvhjbknl,lkjhgfwdxcvbn,hjgxfvc', '', 'location', 'ekounou', 'yaounde', NULL, 50000.00, NULL, 400, 3, NULL, 0, 0, 1, 0, '2025-06-13', 5, 1),
(8, 'Villla tians titi-garage', '', 'villa', 'location', 'titi garage', 'yaounde', NULL, 100000.00, 'par_nuit', 1000, 6, 8, 1, 1, 1, 1, '2025-06-13', 1, 2),
(9, 'chambre Etudiant Essos', 'Idéal pour les étudiants qui veulent un coin propice pour leur études sans bruit', 'chambre', 'location', 'Essos', 'yaounde', NULL, 25000.00, 'fixe', 50, 1, 0, 0, 0, 1, 0, '2025-06-13', 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom`, `description`) VALUES
(1, 'Appartement', 'Logement situé dans un immeuble, adapté à la location ou à la vente'),
(2, 'Villa', 'Maison individuelle avec jardin et souvent une piscine, adaptée aux familles'),
(3, 'Studio', 'Petit appartement généralement constitué d’une seule pièce'),
(4, 'Bureau', 'Espace professionnel à usage commercial ou administratif'),
(5, 'chambre étudiante', 'résidence pour les  étudiants');

-- --------------------------------------------------------

--
-- Structure de la table `contrats`
--

DROP TABLE IF EXISTS `contrats`;
CREATE TABLE IF NOT EXISTS `contrats` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `id_client` int NOT NULL,
  `id_property` int NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--

DROP TABLE IF EXISTS `demandes`;
CREATE TABLE IF NOT EXISTS `demandes` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `id_property` int NOT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en attente','acceptée','refusée') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `favori`
--

DROP TABLE IF EXISTS `favori`;
CREATE TABLE IF NOT EXISTS `favori` (
  `id_favori` int NOT NULL AUTO_INCREMENT,
  `id_user` int DEFAULT NULL,
  `id_property` int DEFAULT NULL,
  `date_ajout` date DEFAULT NULL,
  PRIMARY KEY (`id_favori`),
  KEY `id_user` (`id_user`),
  KEY `id_property` (`id_property`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `favori`
--

INSERT INTO `favori` (`id_favori`, `id_user`, `id_property`, `date_ajout`) VALUES
(17, 4, 1, '2025-06-13'),
(18, 4, 7, '2025-06-13'),
(19, 4, 8, '2025-06-13');

-- --------------------------------------------------------

--
-- Structure de la table `historiquereservation`
--

DROP TABLE IF EXISTS `historiquereservation`;
CREATE TABLE IF NOT EXISTS `historiquereservation` (
  `id_historique` int NOT NULL AUTO_INCREMENT,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('confirmée','annulée') DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `id_property` int DEFAULT NULL,
  `date_archivage` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historique`),
  KEY `id_user` (`id_user`),
  KEY `id_property` (`id_property`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `historiquereservation`
--

INSERT INTO `historiquereservation` (`id_historique`, `date_debut`, `date_fin`, `statut`, `id_user`, `id_property`, `date_archivage`) VALUES
(1, '2025-06-10', '2025-06-12', 'confirmée', 3, 3, '2025-06-11 00:58:55'),
(2, '2025-06-05', '2025-06-10', 'confirmée', 3, 3, '2025-06-12 17:22:11');

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `id_image` int NOT NULL AUTO_INCREMENT,
  `url_image` varchar(255) DEFAULT NULL,
  `id_property` int DEFAULT NULL,
  PRIMARY KEY (`id_image`),
  KEY `id_property` (`id_property`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `image`
--

INSERT INTO `image` (`id_image`, `url_image`, `id_property`) VALUES
(1, 'images/img_68488cea8f286.jpg', 3),
(2, 'images/1749587189_appart bastos 5.jpg', 2),
(3, 'images/1749587189_appart bastos1.jpg', 2),
(4, 'images/1749589201_appart bastos 5.jpg', 1),
(5, 'images/1749589729_appart 10.7.jpg', 1),
(6, 'images/appart 10.1.jpg', 1),
(7, 'images/villa2.2.jpg\r\n', 2),
(51, 'images/1749809576_appart11.4.jpg', 3),
(50, 'images/1749809576_appart11.2.jpg', 3),
(40, 'images/1749806493_studio1.jpg', 7),
(41, 'images/1749806493_studio2.1.jpg', 7),
(42, 'images/1749806493_studio2.2.jpg', 7),
(43, 'images/1749806493_studio2.3.jpg', 7),
(47, 'images/img_684bf3e0de448.jpg', 8),
(44, 'images/img_684bf3e0ddb34.jpg', 8),
(45, 'images/img_684bf3e0ddf93.jpg', 8),
(46, 'images/img_684bf3e0de1fa.jpg', 8),
(26, 'images/1749804609_studio4.4.jpg', 4),
(27, 'images/1749804609_studio4.5.jpg', 4),
(28, 'images/1749805315_studio1.1.jpg', 5),
(29, 'images/1749805315_studio4.0.jpg', 5),
(30, 'images/1749805315_studio4.1.jpg', 5),
(31, 'images/1749805315_studio4.3.jpg', 5),
(32, 'images/1749805315_studio4.4.jpg', 5),
(33, 'images/1749805315_studio4.5.jpg', 5),
(34, 'images/1749805743_studio4.0.jpg', 6),
(35, 'images/1749805743_studio4.1.jpg', 6),
(36, 'images/1749805743_studio4.3 - Copie.jpg', 6),
(37, 'images/1749805743_studio4.3.jpg', 6),
(38, 'images/1749805743_studio4.4.jpg', 6),
(39, 'images/1749805743_studio4.5.jpg', 6),
(48, 'images/img_684bf3e0de64e.jpg', 8),
(49, 'images/img_684bf7afeb946.jpg', 9);

-- --------------------------------------------------------

--
-- Structure de la table `indisponibilite`
--

DROP TABLE IF EXISTS `indisponibilite`;
CREATE TABLE IF NOT EXISTS `indisponibilite` (
  `id_indispo` int NOT NULL AUTO_INCREMENT,
  `id_property` int DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `raison` text,
  PRIMARY KEY (`id_indispo`),
  KEY `id_property` (`id_property`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `contenu` text,
  `date_envoi` datetime DEFAULT NULL,
  `statut` enum('lu','non lu') DEFAULT 'non lu',
  `id_expediteur` int DEFAULT NULL,
  `id_destinataire` int DEFAULT NULL,
  PRIMARY KEY (`id_message`),
  KEY `id_expediteur` (`id_expediteur`),
  KEY `id_destinataire` (`id_destinataire`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id_message`, `contenu`, `date_envoi`, `statut`, `id_expediteur`, `id_destinataire`) VALUES
(8, 'Bonjour, je suis intéressé par le bien que vous proposez.', '2025-06-12 23:27:00', 'lu', 4, 3),
(6, 'Bonjour, je suis intéressé par le bien que vous proposez.', '2025-06-12 21:24:12', 'non lu', 4, 5),
(16, 'hello c\'est eric', '2025-06-13 09:35:32', 'lu', 4, 3),
(17, 'Salut c\'est merveille', '2025-06-13 12:45:55', 'lu', 4, 3),
(15, 'salut je suis sur crome', '2025-06-13 00:13:46', 'lu', 4, 3),
(18, 'Yo bro', '2025-06-13 12:52:57', 'lu', 4, 3);

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

DROP TABLE IF EXISTS `paiement`;
CREATE TABLE IF NOT EXISTS `paiement` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `montant` decimal(10,2) DEFAULT NULL,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `statut` enum('en attente','payé','échoué') DEFAULT 'en attente',
  `date_paiement` datetime DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `id_reservation` int DEFAULT NULL,
  `id_transaction` int DEFAULT NULL,
  PRIMARY KEY (`id_paiement`),
  KEY `id_user` (`id_user`),
  KEY `id_reservation` (`id_reservation`),
  KEY `id_transaction` (`id_transaction`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `id_reservation` int NOT NULL AUTO_INCREMENT,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('en attente','confirmée','annulée') DEFAULT 'en attente',
  `id_user` int DEFAULT NULL,
  `id_property` int DEFAULT NULL,
  `date_demande` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reservation`),
  KEY `id_user` (`id_user`),
  KEY `id_property` (`id_property`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id_reservation`, `date_debut`, `date_fin`, `statut`, `id_user`, `id_property`, `date_demande`) VALUES
(3, '2025-06-13', '2025-06-14', 'en attente', 4, 1, '2025-06-13 05:27:00'),
(4, '2025-06-14', '2025-06-20', 'en attente', 4, 8, '2025-06-13 12:49:04');

-- --------------------------------------------------------

--
-- Structure de la table `signalement`
--

DROP TABLE IF EXISTS `signalement`;
CREATE TABLE IF NOT EXISTS `signalement` (
  `id_signalement` int NOT NULL AUTO_INCREMENT,
  `id_user` int DEFAULT NULL,
  `id_property` int DEFAULT NULL,
  `type_probleme` varchar(100) DEFAULT NULL,
  `description` text,
  `date_signalement` date DEFAULT NULL,
  `statut` enum('traité','en attente','rejeté') DEFAULT 'en attente',
  PRIMARY KEY (`id_signalement`),
  KEY `id_user` (`id_user`),
  KEY `id_property` (`id_property`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `signalement`
--

INSERT INTO `signalement` (`id_signalement`, `id_user`, `id_property`, `type_probleme`, `description`, `date_signalement`, `statut`) VALUES
(1, 3, 3, 'Problème d\'adresse', 'L\'adresse du bien semble incorrecte ou incomplète.', '2025-06-11', 'en attente'),
(2, 3, 2, 'Photos trompeuses', 'Les photos affichées ne correspondent pas à la réalité.', '2025-06-12', 'traité');

-- --------------------------------------------------------

--
-- Structure de la table `transactionvente`
--

DROP TABLE IF EXISTS `transactionvente`;
CREATE TABLE IF NOT EXISTS `transactionvente` (
  `id_transaction` int NOT NULL AUTO_INCREMENT,
  `id_property` int DEFAULT NULL,
  `id_acheteur` int DEFAULT NULL,
  `id_proprietaire` int DEFAULT NULL,
  `prix_vente` decimal(10,2) DEFAULT NULL,
  `date_transaction` date DEFAULT NULL,
  `statut` enum('en attente','finalisée','annulée') DEFAULT 'en attente',
  PRIMARY KEY (`id_transaction`),
  KEY `id_property` (`id_property`),
  KEY `id_acheteur` (`id_acheteur`),
  KEY `id_proprietaire` (`id_proprietaire`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `transactionvente`
--

INSERT INTO `transactionvente` (`id_transaction`, `id_property`, `id_acheteur`, `id_proprietaire`, `prix_vente`, `date_transaction`, `statut`) VALUES
(8, 3, 1, 3, 45000000.00, '2025-06-13', 'finalisée'),
(9, 3, 1, 3, 45000000.00, '2025-06-13', 'finalisée'),
(10, 3, 1, 3, 45000000.00, '2025-06-13', 'finalisée');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `pays` varchar(60) DEFAULT NULL,
  `ville` varchar(60) DEFAULT NULL,
  `nationalite` varchar(60) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `role` enum('client','proprietaire','admin') NOT NULL DEFAULT 'client',
  `photo_profil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `nom`, `prenom`, `date_naissance`, `telephone`, `pays`, `ville`, `nationalite`, `email`, `mot_de_passe`, `role`, `photo_profil`) VALUES
(3, 'Tiani', 'Eric', '2004-05-14', '656245579', 'Cameroun', 'Yaounde', 'Camerounaise', 'tianieric3@gmail.com', '$2y$10$FPQYf0L0gUHD1T9iCe9h6e1v88KWhwTs4/4kRvt11m/T9oPv8W4Ju', 'admin', NULL),
(4, 'KADJIE', 'Dane', '2007-03-02', '687423565', 'Cameroun', 'Kribi', 'Camerounaise', 'tchankamdane@gmail.com', '$2y$10$nFv3HHoiLtBVNr5.cOxOSeepLk7nrLhcKq1cfgtc06UaAqOAG3Wb6', 'client', NULL),
(5, 'Ngeuleu', 'steve', '0000-00-00', '656245579', 'Cameroun', 'yaounde', 'Camerounaise', 'bamounsteve3@gmail.com', '$2y$10$p9tu8.5v.qsr6pkAI3.KOOdo0e8UD0bnFHlvTh1r468iFqom8A4H2', 'proprietaire', 'uploads/profils/684bdeaf2ac2f_cfeVf2-uV0hUo3ToTbLjztuomWk.jpg'),
(6, 'Cabrel', 'Kenne', '2007-10-30', '699606099', 'Cameroun', 'Yaounde', 'Camerounais', 'tropcontropbon93@gmail.com', '$2y$10$FATNctsCPK7Ecl/ph9ai4en2sXfFYOkJ7t1nrqS6QybXpS3ksvtae', 'proprietaire', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
