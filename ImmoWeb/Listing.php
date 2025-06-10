<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImmoWeb - Résultats de recherche</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    // Inclut le fichier de connexion et de fonctions de la base de données
    include 'database.php';

    // Récupère les paramètres de recherche et de filtre depuis l'URL (via GET)
    $filters = [
        'category_id' => isset($_GET['category_id']) ? (int)$_GET['category_id'] : null,
        'location' => isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '',
        'adults' => isset($_GET['adults']) ? (int)$_GET['adults'] : 1,
        'children' => isset($_GET['children']) ? (int)$_GET['children'] : 0,
        // Les dates sont incluses dans le formulaire mais non gérées par la fonction getPropertiesForSale pour le moment
        // 'check_in_date' => isset($_GET['check_in_date']) ? htmlspecialchars($_GET['check_in_date']) : '',
        // 'check_out_date' => isset($_GET['check_out_date']) ? htmlspecialchars($_GET['check_out_date']) : '',
    ];

    // Récupère toutes les catégories pour la barre latérale
    $categories = getCategories();

    // Récupère les biens immobiliers à vendre en fonction des filtres appliqués
    $properties = getPropertiesForSale($filters);
    ?>

    <!-- En-tête de la page -->
    <header class="header">
        <div class="container">
            <a href="index.html" class="logo">ImmoWeb</a>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="listings.php?type=vente" class="active">Vente</a></li>
                    <li><a href="listings.php?type=location">Location</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <a href="#" class="btn-login">S'identifier</a>
                <a href="#" class="btn-register">S'inscrire</a>
            </div>
        </div>
    </header>

    <!-- La barre de recherche est affichée ici aussi pour permettre le filtrage sur la page de résultats -->
    <div class="search-form-container">
        <div class="container">
            <form action="listings.php" method="GET" class="detailed-search-form">
                <input type="hidden" name="type" value="vente">
                <div class="form-group">
                    <label for="location"><i class="fas fa-map-marker-alt"></i> Lieu</label>
                    <input type="text" id="location" name="location" placeholder="Ville, adresse ou région" value="<?php echo htmlspecialchars($filters['location']); ?>">
                </div>
                <div class="form-group">
                    <label for="check-in-date"><i class="fas fa-calendar-alt"></i> Dates</label>
                    <input type="date" id="check-in-date" name="check_in_date" value=""> <!-- À implémenter plus tard pour le filtrage par date -->
                    <span>-</span>
                    <input type="date" id="check-out-date" name="check_out_date" value=""> <!-- À implémenter plus tard pour le filtrage par date -->
                </div>
                <div class="form-group">
                    <label for="adults"><i class="fas fa-user-friends"></i> Personnes</label>
                    <div class="guest-counter">
                        <span class="guest-label">Adultes:</span>
                        <input type="number" id="adults" name="adults" min="1" value="<?php echo htmlspecialchars($filters['adults']); ?>">
                    </div>
                    <div class="guest-counter">
                        <span class="guest-label">Enfants:</span>
                        <input type="number" id="children" name="children" min="0" value="<?php echo htmlspecialchars($filters['children']); ?>">
                    </div>
                </div>
                <button type="submit" class="search-submit-button">Rechercher</button>
            </form>
        </div>
    </div>

    <div class="container main-content">
        <!-- Barre latérale pour les catégories -->
        <aside class="sidebar">
            <h2>Catégories</h2>
            <ul class="category-list">
                <!-- Lien pour afficher toutes les catégories -->
                <li>
                    <!-- Utilise http_build_query pour conserver les autres filtres si présents -->
                    <a href="listings.php?<?php echo http_build_query(array_merge($_GET, ['category_id' => null])); ?>"
                       class="<?php echo ($filters['category_id'] === null) ? 'active' : ''; ?>">Toutes les catégories</a>
                </li>
                <!-- Boucle PHP pour afficher chaque catégorie de la base de données -->
                <?php foreach ($categories as $category): ?>
                    <li>
                        <!-- Utilise http_build_query pour ajouter l'ID de la catégorie tout en conservant les autres filtres -->
                        <a href="listings.php?<?php echo http_build_query(array_merge($_GET, ['category_id' => $category['id_categorie']])); ?>"
                           class="<?php echo ($filters['category_id'] === $category['id_categorie']) ? 'active' : ''; ?>">
                           <?php echo htmlspecialchars($category['nom']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- Section principale pour l'affichage des biens immobiliers -->
        <section class="property-listings">
            <h2>Biens à Vendre</h2>
            <?php if (empty($properties)): ?>
                <!-- Message si aucun bien n'est trouvé -->
                <p class="no-properties">Aucun bien trouvé correspondant à vos critères de recherche.</p>
            <?php else: ?>
                <!-- Grille d'affichage des biens -->
                <div class="property-grid">
                    <!-- Boucle PHP pour afficher chaque bien immobilier -->
                    <?php foreach ($properties as $property): ?>
                        <!-- La carte entière est cliquable pour ouvrir la page de détails -->
                        <div class="property-card" onclick="window.location.href='property_detail.php?id=<?php echo htmlspecialchars($property['id_property']); ?>'">
                            <!-- Image du bien -->
                            <img src="<?php echo htmlspecialchars($property['image_url']); ?>"
                                 alt="Image du bien: <?php echo htmlspecialchars($property['titre']); ?>"
                                 class="property-image"
                                 onerror="this.onerror=null;this.src='https://placehold.co/400x250/E0E0E0/333333?text=Image+Non+Dispo';">
                            <div class="property-info">
                                <!-- Titre du bien -->
                                <h3><?php echo htmlspecialchars($property['titre']); ?></h3>
                                <!-- Localisation du bien -->
                                <p class="property-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['adresse']); ?>, <?php echo htmlspecialchars($property['ville']); ?>
                                </p>
                                <!-- Aperçu de la description -->
                                <p class="property-description-preview">
                                    <?php echo htmlspecialchars($property['short_description']); ?>
                                </p>
                                <!-- Prix du bien -->
                                <p class="property-price">
                                    <?php echo number_format($property['prix'], 2, ',', ' '); ?> €
                                </p>
                                <!-- Caractéristiques principales du bien -->
                                <div class="property-features">
                                    <span><i class="fas fa-bed"></i> <?php echo htmlspecialchars($property['nb_lits']); ?> lits</span>
                                    <span><i class="fas fa-door-open"></i> <?php echo htmlspecialchars($property['nb_pieces']); ?> pièces</span>
                                    <span><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($property['superficie']); ?> m²</span>
                                </div>
                                <!-- Le clic sur la carte entière redirige vers la page de détails -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Pied de page du site -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> ImmoWeb. Tous droits réservés.</p>
            <div class="footer-links">
                <a href="#">Politique de confidentialité</a>
                <a href="#">Conditions d'utilisation</a>
            </div>
        </div>
    </footer>
</body>
</html>
