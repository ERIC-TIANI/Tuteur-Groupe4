-- le nom de la Base de Données est:  " Immo_Web "

-- Suppression des tables existantes si elles existent (pour un re-déploiement propre)
-- L'ordre de suppression est important à cause des clés étrangères
DROP TABLE IF EXISTS Favori;
DROP TABLE IF EXISTS Signalement;
DROP TABLE IF EXISTS Paiement;
DROP TABLE IF EXISTS Reservation;
DROP TABLE IF EXISTS Avis;
DROP TABLE IF EXISTS Message;
DROP TABLE IF EXISTS TransactionVente;
DROP TABLE IF EXISTS Indisponibilite;
DROP TABLE IF EXISTS Image;
DROP TABLE IF EXISTS BienImmobilier;
DROP TABLE IF EXISTS Categorie;
DROP TABLE IF EXISTS Utilisateur;


-- 1. Utilisateur
CREATE TABLE Utilisateur (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    date_naissance DATE,
    telephone VARCHAR(30),
    pays VARCHAR(60),
    ville VARCHAR(60),
    nationalite VARCHAR(60),
    email VARCHAR(150) UNIQUE,
    mot_de_passe VARCHAR(255),
    role ENUM('client', 'proprietaire', 'admin') NOT NULL DEFAULT 'client'
);

-- 2. Catégorie
CREATE TABLE Categorie (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT
);

-- 3. BienImmobilier
CREATE TABLE BienImmobilier (
    id_property INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200),
    description TEXT,
    type_bien ENUM ('appartement', 'villa' , 'studio', 'chambre') NOT NULL,
    type_annonce ENUM('location', 'vente') NOT NULL,
    adresse VARCHAR(255),
    ville VARCHAR(100),
    prix DECIMAL(10, 2),
    mode_tarif ENUM('fixe', 'par_nuit'),
    superficie FLOAT,
    nb_pieces INT,
    nb_lits INT,
    wifi BOOLEAN DEFAULT FALSE,
    climatisation BOOLEAN DEFAULT FALSE,
    cuisine BOOLEAN DEFAULT FALSE,
    parking BOOLEAN DEFAULT FALSE,
    date_ajout DATE,
    id_user INT,
    id_categorie INT,
    FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_categorie) REFERENCES Categorie(id_categorie)
);

-- 4. Image
CREATE TABLE Image (
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    url_image VARCHAR(255),
    id_property INT,
    FOREIGN KEY (id_property) REFERENCES BienImmobilier(id_property) ON DELETE CASCADE
);

-- 5. Réservation
CREATE TABLE Reservation (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    date_debut DATE,
    date_fin DATE,
    statut ENUM('en attente', 'confirmée', 'annulée') DEFAULT 'en attente',
    id_user INT,
    id_property INT,
    FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user),
    FOREIGN KEY (id_property) REFERENCES BienImmobilier(id_property)
);

-- 6. Avis
CREATE TABLE Avis (
    id_review INT AUTO_INCREMENT PRIMARY KEY,
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    date_review DATE,
    id_user INT,
    id_property INT,
    FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user),
    FOREIGN KEY (id_property) REFERENCES BienImmobilier(id_property)
);

-- 7. Message
CREATE TABLE Message (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT,
    date_envoi DATETIME,
    statut ENUM('lu', 'non lu') DEFAULT 'non lu',
    id_expediteur INT,
    id_destinataire INT,
    FOREIGN KEY (id_expediteur) REFERENCES Utilisateur(id_user),
    FOREIGN KEY (id_destinataire) REFERENCES Utilisateur(id_user)
);

-- 8. TransactionVente
CREATE TABLE TransactionVente (
    id_transaction INT AUTO_INCREMENT PRIMARY KEY,
    id_property INT,
    id_acheteur INT,
    id_proprietaire INT,
    prix_vente DECIMAL(10, 2),
    date_transaction DATE,
    statut ENUM('en attente', 'finalisée', 'annulée') DEFAULT 'en attente',
    FOREIGN KEY (id_property) REFERENCES BienImmobilier(id_property),
    FOREIGN KEY (id_acheteur) REFERENCES Utilisateur(id_user),
    FOREIGN KEY (id_proprietaire) REFERENCES Utilisateur(id_user)
);

-- 9. Paiement
CREATE TABLE Paiement (
    id_paiement INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(10, 2),
    mode_paiement VARCHAR(50),
    statut ENUM('en attente', 'payé', 'échoué') DEFAULT 'en attente',
    date_paiement DATETIME,
    id_user INT,
    id_reservation INT DEFAULT NULL,
    id_transaction INT DEFAULT NULL,
    FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user),
    FOREIGN KEY (id_reservation) REFERENCES Reservation(id_reservation),
    FOREIGN KEY (id_transaction) REFERENCES TransactionVente(id_transaction)
);

-- 10. Favori
CREATE TABLE Favori (
    id_favori INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_property INT,
    date_ajout DATE,
    FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user),
    FOREIGN KEY (id_property) REFERENCES BienImmobilier(id_property)
);

-- 11. Signalement
CREATE TABLE Signalement (
    id_signalement INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_property INT,
    type_probleme VARCHAR(100),
    description TEXT,
    date_signalement DATE,
    statut ENUM('traité', 'en attente', 'rejeté') DEFAULT 'en attente',
    FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user),
    FOREIGN KEY (id_property) REFERENCES BienImmobilier(id_property)
);

-- 12. Indisponibilite
CREATE TABLE Indisponibilite (
    id_indispo INT AUTO_INCREMENT PRIMARY KEY,
    id_property INT,
    date_debut DATE,
    date_fin DATE,
    raison TEXT,
    FOREIGN KEY (id_property) REFERENCES BienImmobilier(id_property)
);

--
-- Données de test pour les tables
--

-- Utilisateurs
INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Dupont', 'Jean', 'jean.dupont@example.com', 'pass123', 'proprietaire'),
('Martin', 'Sophie', 'sophie.martin@example.com', 'pass456', 'client'),
('Durand', 'Paul', 'paul.durand@example.com', 'pass789', 'admin'); -- Changed to admin for diverse roles

-- Catégories
INSERT INTO Categorie (id_categorie, nom, description) VALUES
(1, 'Chambre', 'Chambres privées ou en colocation.'),
(2, 'Studio', 'Petits appartements avec une pièce principale.'),
(3, 'Appartement', 'Appartements de différentes tailles et configurations.'),
(4, 'Villa', 'Grandes maisons avec jardin et équipements.');

-- Biens Immobiliers (Assurez-vous que id_user et id_categorie existent)
-- Les id_user 1 et 3 sont des propriétaires
INSERT INTO BienImmobilier (titre, description, type_bien, type_annonce, adresse, ville, prix, mode_tarif, superficie, nb_pieces, nb_lits, wifi, climatisation, cuisine, parking, date_ajout, id_user, id_categorie) VALUES
('Belle Chambre avec Vue', 'Située au cœur de la ville, cette chambre offre une vue imprenable sur les environs et un accès facile aux transports en commun. Idéal pour les étudiants ou les voyageurs d\'affaires.', 'chambre', 'vente', '10 Rue de la Paix', 'Paris', 250000.00, 'fixe', 20.5, 1, 1, TRUE, FALSE, FALSE, FALSE, '2024-01-15', 1, 1),
('Studio Moderne Proche Gare', 'Un studio entièrement meublé et équipé, parfait pour une personne seule ou un couple. À quelques minutes à pied de la gare principale.', 'studio', 'vente', '5 Avenue des Fleurs', 'Lyon', 180000.00, 'fixe', 30.0, 1, 1, TRUE, TRUE, TRUE, FALSE, '2024-02-01', 1, 2),
('Appartement Familial Spacieux', 'Grand appartement avec trois chambres, salon lumineux et cuisine équipée. Idéal pour une famille, proche des écoles et commerces.', 'appartement', 'vente', '25 Boulevard Victor Hugo', 'Marseille', 450000.00, 'fixe', 90.0, 4, 3, TRUE, TRUE, TRUE, TRUE, '2024-03-10', 3, 3),
('Villa de Luxe avec Piscine', 'Magnifique villa moderne avec piscine privée, grand jardin et vue sur la mer. Parfait pour des vacances inoubliables ou une résidence permanente.', 'villa', 'vente', '1 Impasse du Soleil', 'Nice', 950000.00, 'fixe', 200.0, 7, 5, TRUE, TRUE, TRUE, TRUE, '2024-04-20', 3, 4),
('Chambre Confortable Centre-ville', 'Chambre lumineuse dans un appartement partagé, idéalement située pour explorer la ville. Accès à la cuisine et au salon partagés.', 'chambre', 'vente', '12 Rue Royale', 'Bordeaux', 220000.00, 'fixe', 18.0, 1, 1, TRUE, FALSE, TRUE, FALSE, '2024-05-01', 1, 1),
('Studio Cosy Vue Mer', 'Charmant studio avec balcon offrant une vue partielle sur la mer. Idéal pour un pied-à-terre ou un investissement locatif.', 'studio', 'vente', '8 Chemin Côtier', 'Cannes', 280000.00, 'fixe', 35.0, 1, 1, TRUE, TRUE, TRUE, TRUE, '2024-05-15', 1, 2),
('Appartement T2 Lumineux', 'Appartement deux pièces moderne, entièrement rénové, avec une grande chambre et un séjour cuisine ouvert.', 'appartement', 'vente', '3 Rue des Lilas', 'Toulouse', 290000.00, 'fixe', 50.0, 2, 1, TRUE, TRUE, TRUE, FALSE, '2024-06-01', 3, 3);


-- Images (Associées aux BienImmobilier créés ci-dessus)
-- Chaque id_property doit correspondre à un id_property existant dans BienImmobilier
-- J'ai mis des images génériques (placeholders) pour l'exemple.
INSERT INTO Image (id_property, url_image) VALUES
-- Images pour la Chambre (id_property 1)
(1, 'https://placehold.co/800x600/C8A2C8/FFFFFF?text=Chambre+1'),
(1, 'https://placehold.co/800x600/DDA0DD/FFFFFF?text=Chambre+2'),
(1, 'https://placehold.co/800x600/EE82EE/FFFFFF?text=Chambre+3'),
(1, 'https://placehold.co/800x600/AA82EE/FFFFFF?text=Chambre+4'),
(1, 'https://placehold.co/800x600/BB82EE/FFFFFF?text=Chambre+5'),
(1, 'https://placehold.co/800x600/CC82EE/FFFFFF?text=Chambre+6'),
(1, 'https://placehold.co/800x600/DD82EE/FFFFFF?text=Chambre+7'),

-- Images pour le Studio (id_property 2)
(2, 'https://placehold.co/800x600/ADD8E6/333333?text=Studio+1'),
(2, 'https://placehold.co/800x600/87CEEB/333333?text=Studio+2'),
(2, 'https://placehold.co/800x600/4682B4/FFFFFF?text=Studio+3'),

-- Images pour l'Appartement (id_property 3)
(3, 'https://placehold.co/800x600/F08080/FFFFFF?text=Appartement+1'),
(3, 'https://placehold.co/800x600/CD5C5C/FFFFFF?text=Appartement+2'),
(3, 'https://placehold.co/800x600/B22222/FFFFFF?text=Appartement+3'),

-- Images pour la Villa (id_property 4)
(4, 'https://placehold.co/800x600/98FB98/333333?text=Villa+1'),
(4, 'https://placehold.co/800x600/66CDAA/333333?text=Villa+2'),
(4, 'https://placehold.co/800x600/3CB371/FFFFFF?text=Villa+3'),

-- Images pour la deuxième Chambre (id_property 5)
(5, 'https://placehold.co/800x600/C0C0C0/333333?text=Chambre+Confortable+1'),
(5, 'https://placehold.co/800x600/A9A9A9/FFFFFF?text=Chambre+Confortable+2'),

-- Images pour le deuxième Studio (id_property 6)
(6, 'https://placehold.co/800x600/D3D3D3/333333?text=Studio+Cosy+1'),
(6, 'https://placehold.co/800x600/BEBEBE/FFFFFF?text=Studio+Cosy+2'),

-- Images pour le deuxième Appartement (id_property 7)
(7, 'https://placehold.co/800x600/FFD700/333333?text=Appartement+T2+1'),
(7, 'https://placehold.co/800x600/DAA520/FFFFFF?text=Appartement+T2+2');


-- Autres tables (laissez vides pour l'instant ou ajoutez des données de test si nécessaire)
-- INSERT INTO Reservation ...
-- INSERT INTO Avis ...
-- INSERT INTO Message ...
-- INSERT INTO TransactionVente ...
-- INSERT INTO Paiement ...
-- INSERT INTO Favori ...
-- INSERT INTO Signalement ...
-- INSERT INTO Indisponibilite ...
