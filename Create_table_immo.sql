-- le nom de la Base de Données est:  " Immo_Web "

-- 1. Utilisateur
CREATE TABLE Utilisateur (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    date_naissance date,
    telephone varchar(30) ,
    pays varchar (60),
    ville varchar (60),
    nationalite varchar (60),
    email VARCHAR(150) UNIQUE,
    mot_de_passe VARCHAR(255),
    role ENUM('client', 'proprietaire', 'admin') NOT NULL DEFAULT "client"
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
    type_bien  ENUM ('appartement', 'villa' , 'studio', 'chambre')NOT NULL,
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
--5.1 Historiques des reservations
CREATE TABLE HistoriqueReservation (
    id_historique INT AUTO_INCREMENT PRIMARY KEY,
    date_debut DATE,
    date_fin DATE,
    statut ENUM('confirmée', 'annulée'),
    id_user INT,
    id_property INT,
    date_archivage DATETIME DEFAULT CURRENT_TIMESTAMP,
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
