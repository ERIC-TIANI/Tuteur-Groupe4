<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Bien - ImmoWeb</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    // Inclut le fichier de connexion et de fonctions de la base de données
    include 'database.php';

    // Récupère l'ID du bien depuis l'URL (via GET)
    $property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Redirige vers la page des listings si aucun ID de bien n'est fourni ou si l'ID est invalide
    if ($property_id === 0) {
        header("Location: listings.php"); // Ou index.html, selon le point de départ préféré
        exit(); // Arrête l'exécution du script après la redirection
    }

    // Récupère les détails complets du bien immobilier et ses images associées
    $property = getPropertyDetails($property_id);
    $images = getPropertyImages($property_id);

    // Redirige si le bien n'est pas trouvé dans la base de données
    if (!$property) {
        header("Location: listings.php");
        exit(); // Arrête l'exécution du script après la redirection
    }
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

    <div class="container property-detail-content">
        <!-- Chemin de navigation (Breadcrumb) -->
        <div class="breadcrumb">
            <a href="index.html">Accueil</a> &gt; <a href="listings.php?type=vente">Vente</a> &gt; <?php echo htmlspecialchars($property['titre']); ?>
        </div>

        <!-- Titre principal du bien et sa localisation -->
        <h1 class="property-title"><?php echo htmlspecialchars($property['titre']); ?></h1>
        <p class="property-location-detail">
            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['adresse']); ?>, <?php echo htmlspecialchars($property['ville']); ?>
        </p>

        <!-- Section de la galerie d'images -->
        <div class="image-gallery">
            <?php if (!empty($images)): ?>
                <!-- Conteneur de l'image principale -->
                <div class="main-image-display">
                    <img src="<?php echo htmlspecialchars($images[0]); ?>" alt="Image principale du bien" id="mainImage" onerror="this.onerror=null;this.src='https://placehold.co/800x600/E0E0E0/333333?text=Image+Non+Dispo';">
                </div>
                <!-- Grille des miniatures pour la sélection d'image -->
                <div class="thumbnail-grid">
                    <?php for ($i = 0; $i < count($images); $i++): ?>
                        <img src="<?php echo htmlspecialchars($images[$i]); ?>" alt="Miniature <?php echo $i + 1; ?>" class="thumbnail <?php echo ($i === 0) ? 'active' : ''; ?>" onclick="changeMainImage(this)" onerror="this.onerror=null;this.src='https://placehold.co/150x100/E0E0E0/333333?text=N/A';">
                    <?php endfor; ?>
                </div>
            <?php else: ?>
                <!-- Message et image par défaut si aucune image n'est disponible -->
                <div class="no-images">
                    <img src="https://placehold.co/800x600/E0E0E0/333333?text=Aucune+Image+Disponible" alt="Aucune image disponible">
                </div>
            <?php endif; ?>
        </div>

        <!-- Section des détails principaux (description, commodités, prix) -->
        <div class="property-main-details">
            <!-- Section de description détaillée -->
            <div class="description-section">
                <h2>Description du bien</h2>
                <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
            </div>

            <!-- Section des points forts / commodités -->
            <div class="amenities-section">
                <h2>Points forts de l'hébergement</h2>
                <div class="amenities-grid">
                    <?php if ($property['nb_lits']): ?>
                        <span><i class="fas fa-bed"></i> <?php echo htmlspecialchars($property['nb_lits']); ?> Lit(s)</span>
                    <?php endif; ?>
                    <?php if ($property['nb_pieces']): ?>
                        <span><i class="fas fa-door-open"></i> <?php echo htmlspecialchars($property['nb_pieces']); ?> Pièce(s)</span>
                    <?php endif; ?>
                    <?php if ($property['superficie']): ?>
                        <span><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($property['superficie']); ?> m²</span>
                    <?php endif; ?>
                    <?php if ($property['wifi']): ?>
                        <span><i class="fas fa-wifi"></i> WiFi gratuit</span>
                    <?php endif; ?>
                    <?php if ($property['climatisation']): ?>
                        <span><i class="fas fa-snowflake"></i> Climatisation</span>
                    <?php endif; ?>
                    <?php if ($property['cuisine']): ?>
                        <span><i class="fas fa-utensils"></i> Cuisine équipée</span>
                    <?php endif; ?>
                    <?php if ($property['parking']): ?>
                        <span><i class="fas fa-parking"></i> Parking</span>
                    <?php endif; ?>
                    <!-- Ajoutez d'autres commodités ici si elles existent dans votre base de données -->
                </div>
            </div>

            <!-- Section de prix et bouton de contact -->
            <div class="booking-price-section">
                <div class="price-box">
                    <p class="from-price">Prix</p>
                    <p class="final-price"><?php echo number_format($property['prix'], 2, ',', ' '); ?> €</p>
                    <p class="price-note">
                        <?php
                        // Affiche si le prix est par nuit ou fixe, selon le mode_tarif
                        if ($property['mode_tarif'] === 'par_nuit') {
                            echo "par nuit";
                        } else {
                            echo "prix fixe";
                        }
                        ?>
                    </p>
                    <button class="btn-reserve">Contacter le vendeur</button>
                </div>
            </div>
        </div>
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

    <!-- Script JavaScript pour la galerie d'images -->
    <script>
        /**
         * Change l'image principale de la galerie en fonction de la miniature cliquée.
         * Met à jour la source de l'image principale et la classe 'active' des miniatures.
         * @param {HTMLImageElement} thumbnail L'élément image miniature HTML cliqué.
         */
        function changeMainImage(thumbnail) {
            const mainImage = document.getElementById('mainImage');
            mainImage.src = thumbnail.src; // Met à jour la source de l'image principale

            // Supprime la classe 'active' de toutes les miniatures pour désactiver l'état précédent
            const thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach(thumb => thumb.classList.remove('active'));

            // Ajoute la classe 'active' à la miniature qui a été cliquée pour la mettre en surbrillance
            thumbnail.classList.add('active');
        }
    </script>
</body>
</html>
