<?php
// database.php - Gère la connexion à la base de données et les fonctions d'interaction.

// --- Affichage des erreurs PHP (très utile pour le débogage, à désactiver en production) ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Configuration de la base de données ---
$servername = "localhost"; // L'adresse de votre serveur de base de données (souvent 'localhost')
$username = "root";       // Votre nom d'utilisateur MySQL (par défaut 'root' pour XAMPP/WAMP)
$password = "";           // Votre mot de passe MySQL (par défaut vide pour XAMPP/WAMP, ou votre mot de passe si vous en avez défini un)
$dbname = "Immo_Web";     // Le nom de la base de données que vous avez créée selon votre script SQL

// Crée une nouvelle connexion MySQLi (interface orientée objet pour MySQL)
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie si la connexion a échoué en utilisant la propriété connect_error
if ($conn->connect_error) {
    // Si la connexion échoue, arrête l'exécution du script et affiche le message d'erreur
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

/**
 * Récupère toutes les catégories de biens immobiliers depuis la base de données.
 * Utile pour peupler la barre latérale ou les liens de catégorie.
 * @return array Un tableau associatif où chaque élément représente une catégorie (id_categorie, nom).
 */
function getCategories() {
    global $conn; // Accède à la variable de connexion globale pour l'utiliser dans la fonction
    $sql = "SELECT id_categorie, nom FROM Categorie ORDER BY nom ASC"; // Requête SQL pour sélectionner l'ID et le nom de toutes les catégories
    $result = $conn->query($sql); // Exécute la requête SQL. $result contient un objet mysqli_result.

    $categories = []; // Initialise un tableau vide pour stocker les catégories récupérées
    if ($result->num_rows > 0) { // Vérifie si la requête a retourné au moins une ligne
        while ($row = $result->fetch_assoc()) { // Parcourt chaque ligne du résultat sous forme de tableau associatif
            $categories[] = $row; // Ajoute la ligne (représentant une catégorie) au tableau $categories
        }
    }
    return $categories; // Retourne le tableau de toutes les catégories
}

/**
 * Récupère les biens immobiliers à vendre, en appliquant des filtres optionnels.
 * Inclut l'URL de la première image associée à chaque bien pour l'affichage des cartes.
 *
 * @param array $filters Un tableau associatif contenant les critères de filtrage (category_id, location, adults, children).
 * @return array Un tableau d'objets (biens immobiliers) correspondants aux filtres. Chaque bien inclut 'image_url' et 'short_description'.
 */
function getPropertiesForSale($filters = []) {
    global $conn; // Accède à la connexion globale
    $properties = []; // Tableau pour stocker les biens trouvés

    // Requête SQL de base : sélectionne toutes les colonnes nécessaires de BienImmobilier
    // et utilise une sous-requête corrélée pour récupérer l'URL de la première image.
    $sql = "SELECT
                b.id_property,
                b.titre,
                b.description,
                b.adresse,
                b.ville,
                b.prix,
                b.superficie,
                b.nb_pieces,
                b.nb_lits,
                b.wifi,
                b.climatisation,
                b.cuisine,
                b.parking,
                (SELECT url_image FROM Image WHERE id_property = b.id_property ORDER BY id_image LIMIT 1) AS image_url
            FROM
                BienImmobilier b
            WHERE
                b.type_annonce = 'vente'"; // Condition initiale : seulement les biens à vendre

    $params = []; // Tableau pour stocker les valeurs des paramètres des requêtes préparées
    $types = '';  // Chaîne pour stocker les types des paramètres (i pour int, s pour string, etc.)

    // Ajout de filtres dynamiques basés sur le tableau $filters
    if (isset($filters['category_id']) && $filters['category_id'] !== null && $filters['category_id'] > 0) {
        $sql .= " AND b.id_categorie = ?"; // Ajoute une condition de filtrage par catégorie
        $params[] = $filters['category_id']; // Ajoute la valeur de l'ID de catégorie
        $types .= 'i'; // Spécifie que c'est un entier
    }

    if (isset($filters['location']) && !empty($filters['location'])) {
        $location = '%' . $filters['location'] . '%'; // Ajoute des wildcards pour la recherche LIKE
        $sql .= " AND (b.adresse LIKE ? OR b.ville LIKE ?)"; // Recherche dans l'adresse ou la ville
        $params[] = $location; // Première occurrence du lieu
        $params[] = $location; // Deuxième occurrence du lieu
        $types .= 'ss'; // Spécifie que ce sont deux chaînes de caractères
    }

    if (isset($filters['adults']) && $filters['adults'] > 0) {
        $sql .= " AND b.nb_lits >= ?"; // Assure que le nombre de lits est suffisant
        $params[] = $filters['adults']; // Ajoute la valeur du nombre d'adultes
        $types .= 'i'; // Spécifie que c'est un entier
    }

    // NOTE: L'implémentation complète du filtrage par date (check_in_date, check_out_date) est plus complexe.
    // Elle nécessiterait de vérifier la table `Indisponibilite` pour s'assurer que le bien n'est pas déjà réservé.
    // Cela implique des JOINs ou des sous-requêtes plus avancées qui ne sont pas incluses ici pour la simplicité.
    /*
    if (isset($filters['check_in_date']) && !empty($filters['check_in_date']) &&
        isset($filters['check_out_date']) && !empty($filters['check_out_date'])) {
        // Logique complexe pour vérifier les chevauchements de dates avec Indisponibilite
    }
    */

    $sql .= " ORDER BY b.date_ajout DESC"; // Trie les résultats par date d'ajout (les plus récents en premier)

    // Prépare la requête SQL pour des raisons de sécurité (prévention des injections SQL)
    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        // Lie les paramètres à la requête préparée si des filtres sont appliqués
        // La fonction call_user_func_array est utilisée car bind_param attend des arguments par référence.
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute(); // Exécute la requête préparée
    $result = $stmt->get_result(); // Récupère l'ensemble des résultats

    if ($result->num_rows > 0) { // Si des biens sont trouvés
        while ($row = $result->fetch_assoc()) { // Parcourt chaque ligne du résultat
            // Définit une image par défaut si 'image_url' est vide ou null (par exemple, si le bien n'a pas d'image)
            $row['image_url'] = $row['image_url'] ?: 'https://placehold.co/400x250/E0E0E0/333333?text=Image+Non+Dispo';
            // Tronque la description pour l'aperçu si elle est trop longue, ajoute "..."
            $row['short_description'] = (strlen($row['description']) > 150) ? substr($row['description'], 0, 150) . '...' : $row['description'];
            $properties[] = $row; // Ajoute le bien modifié au tableau de résultats
        }
    }
    $stmt->close(); // Ferme la déclaration préparée pour libérer les ressources
    return $properties; // Retourne le tableau des biens immobiliers
}

/**
 * Récupère les détails complets d'un bien immobilier spécifique par son ID.
 *
 * @param int $propertyId L'ID unique du bien immobilier.
 * @return array|null Un tableau associatif du bien avec toutes ses informations, ou null si le bien n'est pas trouvé.
 */
function getPropertyDetails($propertyId) {
    global $conn; // Accède à la connexion globale
    $sql = "SELECT
                b.id_property,
                b.titre,
                b.description,
                b.type_bien,
                b.type_annonce,
                b.adresse,
                b.ville,
                b.prix,
                b.mode_tarif,
                b.superficie,
                b.nb_pieces,
                b.nb_lits,
                b.wifi,
                b.climatisation,
                b.cuisine,
                b.parking,
                b.date_ajout,
                u.nom AS proprietaire_nom,       -- Nom du propriétaire (jointure avec Utilisateur)
                u.prenom AS proprietaire_prenom  -- Prénom du propriétaire
            FROM
                BienImmobilier b
            JOIN
                Utilisateur u ON b.id_user = u.id_user  -- Jointure pour obtenir les informations du propriétaire
            WHERE
                b.id_property = ?"; // Condition pour sélectionner un bien par son ID

    $stmt = $conn->prepare($sql); // Prépare la requête
    $stmt->bind_param("i", $propertyId); // Lie l'ID du bien comme un entier
    $stmt->execute(); // Exécute la requête
    $result = $stmt->get_result(); // Récupère le résultat
    $property = $result->fetch_assoc(); // Récupère une seule ligne sous forme de tableau associatif
    $stmt->close(); // Ferme la déclaration
    return $property; // Retourne le bien ou null
}

/**
 * Récupère toutes les URL d'images pour un bien immobilier donné, limité à 7 images.
 *
 * @param int $propertyId L'ID du bien immobilier.
 * @return array Un tableau de chaînes de caractères (URL des images).
 */
function getPropertyImages($propertyId) {
    global $conn; // Accède à la connexion globale
    $images = []; // Tableau pour stocker les URL des images
    $sql = "SELECT url_image FROM Image WHERE id_property = ? ORDER BY id_image ASC LIMIT 7"; // Sélectionne les URL, trie par ID et limite à 7
    $stmt = $conn->prepare($sql); // Prépare la requête
    $stmt->bind_param("i", $propertyId); // Lie l'ID du bien
    $stmt->execute(); // Exécute la requête
    $result = $stmt->get_result(); // Récupère le résultat
    while ($row = $result->fetch_assoc()) { // Parcourt chaque ligne
        $images[] = $row['url_image']; // Ajoute l'URL de l'image au tableau
    }
    $stmt->close(); // Ferme la déclaration
    return $images; // Retourne le tableau des URL d'images
}

?>
