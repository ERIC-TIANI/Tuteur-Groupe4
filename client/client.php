<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "immo_web";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


$categories = $conn->query("SELECT * FROM categorie")->fetchAll(PDO::FETCH_ASSOC);


$typesBien = $conn->query("SELECT DISTINCT type_bien FROM bienimmobilier WHERE type_bien IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);


$typesAnnonce = ['location', 'vente'];


$selectedCategory = $_GET['categorie'] ?? '';
$selectedTypeBien = $_GET['type_bien'] ?? '';
$selectedTypeAnnonce = $_GET['type_annonce'] ?? '';
$minPrice = $_GET['min_prix'] ?? '';
$maxPrice = $_GET['max_prix'] ?? '';

// Construction de la requête avec filtres
$whereClauses = [];
$params = [];

if ($selectedCategory) {
    $whereClauses[] = "b.id_categorie = :categorie";
    $params[':categorie'] = $selectedCategory;
}

if ($selectedTypeBien) {
    $whereClauses[] = "b.type_bien = :type_bien";
    $params[':type_bien'] = $selectedTypeBien;
}

if ($selectedTypeAnnonce) {
    $whereClauses[] = "b.type_annonce = :type_annonce";
    $params[':type_annonce'] = $selectedTypeAnnonce;
}

if ($minPrice !== '') {
    $whereClauses[] = "b.prix >= :min_prix";
    $params[':min_prix'] = $minPrice;
}

if ($maxPrice !== '') {
    $whereClauses[] = "b.prix <= :max_prix";
    $params[':max_prix'] = $maxPrice;
}

$whereSQL = $whereClauses ? " WHERE " . implode(" AND ", $whereClauses) : "";


$sql = "SELECT b.*, c.nom as nom_categorie, 
               (SELECT url_image FROM image WHERE id_property = b.id_property LIMIT 1) as image_url
        FROM bienimmobilier b
        LEFT JOIN categorie c ON b.id_categorie = c.id_categorie
        $whereSQL";

$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$biens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Interface Client - Immobilier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .search-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .search-bar select, 
        .search-bar input,
        .search-bar button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 150px;
        }
        .search-bar button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            flex: 1;
            max-width: 200px;
        }
        .search-bar button:hover {
            background-color: #45a049;
        }
        .price-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .price-range input {
            width: 100px;
        }
        .biens-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        /* Style lien couvrant toute la carte */
        .bien-link {
            text-decoration: none;
            color: inherit;
            display: block;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .bien-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .bien-link img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .bien-type {
            color: #666;
            font-style: italic;
            margin-bottom: 5px;
        }
        .bien-categorie {
            background-color: #e0f7fa;
            color: #00796b;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            display: inline-block;
            margin-bottom: 5px;
        }
        .bien-prix {
            font-weight: bold;
            color: #e74c3c;
            font-size: 1.2em;
            margin-top: auto;
            padding-top: 10px;
        }
        .bien-annonce-type {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8em;
        }
        .bien-card-header {
            position: relative;
        }
        .bien-options {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }
        .bien-option {
            background-color: #f1f1f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8em;
        }
        .reset-filters {
            display: inline-block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            margin-left: 10px;
        }
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #c0392b;
            border-color: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .reset-filters:hover {
            text-decoration: underline;
        }
        /* position relative pour badge type annonce */
        .bien-link .bien-card-header {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nos biens immobiliers</h1>
        
        <!-- Barre de recherche avancée -->
        <form method="GET" action="client.php" class="search-bar">
            <select name="categorie">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= htmlspecialchars($categorie['id_categorie']) ?>" 
                        <?= ($selectedCategory == $categorie['id_categorie']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categorie['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="type_bien">
                <option value="">Tous les types</option>
                <?php foreach ($typesBien as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>" 
                        <?= ($selectedTypeBien === $type) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($type) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="type_annonce">
                <option value="">Location ou vente</option>
                <?php foreach ($typesAnnonce as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>" 
                        <?= ($selectedTypeAnnonce === $type) ? 'selected' : '' ?>>
                        <?= ucfirst(htmlspecialchars($type)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <div class="price-range">
                <input type="number" name="min_prix" placeholder="Prix min" value="<?= htmlspecialchars($minPrice) ?>">
                <span>-</span>
                <input type="number" name="max_prix" placeholder="Prix max" value="<?= htmlspecialchars($maxPrice) ?>">
            </div>
            
            <button type="submit">Filtrer</button>
            
            <?php if ($selectedCategory || $selectedTypeBien || $selectedTypeAnnonce || $minPrice !== '' || $maxPrice !== ''): ?>
                <a href="client.php" class="reset-filters">Réinitialiser</a>
            <?php endif; ?>
        </form>
        
        <!-- Affichage des biens -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
        <li class="nav-item ms-2"><a class="btn btn-primary" href="IA.php">Chat</a></li>
            </ul>
            <a href="logout.php" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
            </div>

        <div class="biens-container">
            <?php if (count($biens) > 0): ?>
                <?php foreach ($biens as $bien): ?>
                    <a href="detail_bien.php?id_property=<?= urlencode($bien['id_property']) ?>" class="bien-link">
                        <div class="bien-card-header">
                            <?php if (!empty($bien['image_url'])): ?>
                                <img src="<?= htmlspecialchars($bien['image_url']) ?>" alt="<?= htmlspecialchars($bien['titre']) ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x200?text=Pas+d'image" alt="Image non disponible">
                            <?php endif; ?>
                            <span class="bien-annonce-type"><?= ucfirst(htmlspecialchars($bien['type_annonce'])) ?></span>
                        </div>
                        
                        <h3><?= htmlspecialchars($bien['titre']) ?></h3>
                        
                        <?php if (!empty($bien['nom_categorie'])): ?>
                            <span class="bien-categorie"><?= htmlspecialchars($bien['nom_categorie']) ?></span>
                        <?php endif; ?>
                        
                        <?php if (!empty($bien['type_bien'])): ?>
                            <p class="bien-type">Type: <?= htmlspecialchars($bien['type_bien']) ?></p>
                        <?php endif; ?>
                        
                        <p><?= htmlspecialchars(mb_substr($bien['description'], 0, 100)) ?>...</p>
                        
                        <p>Surface: <?= htmlspecialchars($bien['superficie']) ?> m²</p>
                        <p>Pièces: <?= htmlspecialchars($bien['nb_pieces']) ?></p>
                        
                        <div class="bien-options">
                            <?php if ($bien['wifi']): ?><span class="bien-option">WiFi</span><?php endif; ?>
                            <?php if ($bien['climatisation']): ?><span class="bien-option">Climatisation</span><?php endif; ?>
                            <?php if ($bien['cuisine']): ?><span class="bien-option">Cuisine</span><?php endif; ?>
                            <?php if ($bien['parking']): ?><span class="bien-option">Parking</span><?php endif; ?>
                        </div>
                        
                        <p class="bien-prix">
                            <?= number_format($bien['prix'], 2, ',', ' ') ?> F
                            <?php if ($bien['type_annonce'] === 'location'): ?>
                                /mois
                            <?php endif; ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center;">Aucun bien trouvé avec ces critères de recherche.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
