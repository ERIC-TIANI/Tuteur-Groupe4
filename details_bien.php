<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "Immo_Web");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérifie si l'identifiant du bien est passé en paramètre
if (!isset($_GET['id_property'])) {
    die("Aucun bien sélectionné.");
}

$id = intval($_GET['id_property']);

// Récupère les détails du bien
$sql = "SELECT b.*, u.nom AS nom_proprio, u.prenom AS prenom_proprio, c.nom AS categorie
        FROM BienImmobilier b
        JOIN Utilisateur u ON b.id_user = u.id_user
        JOIN Categorie c ON b.id_categorie = c.id_categorie
        WHERE b.id_property = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Bien introuvable.");
}

$bien = $result->fetch_assoc();

// Récupère toutes les images associées au bien
$img_sql = "SELECT url_image FROM Image WHERE id_property = $id";
$img_result = $conn->query($img_sql);
$images = [];
while ($row = $img_result->fetch_assoc()) {
    $images[] = $row['url_image'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du bien - <?= htmlspecialchars($bien['titre']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 2rem;
            text-align: center;
            font-size: 2rem;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .images {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            margin-bottom: 1.5rem;
        }

        .images img {
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
        }

        h2 {
            margin-bottom: 0.5rem;
        }

        .info {
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .label {
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .back-link:hover {
            background-color: #217dbb;
        }
    </style>
</head>
<body>

<header>Détails du bien</header>

<div class="container">
    <div class="images">
        <?php if (count($images) > 0): ?>
            <?php foreach ($images as $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="Image du bien">
            <?php endforeach; ?>
        <?php else: ?>
            <img src="default.jpg" alt="Aucune image">
        <?php endif; ?>
    </div>

    <h2><?= htmlspecialchars($bien['titre']) ?></h2>
    <div class="info">
        <span class="label">Description :</span><br>
        <?= nl2br(htmlspecialchars($bien['description'])) ?>
    </div>
    <div class="info"><span class="label">Catégorie :</span> <?= htmlspecialchars($bien['categorie']) ?></div>
    <div class="info"><span class="label">Type :</span> <?= htmlspecialchars($bien['type_bien']) ?> | <?= htmlspecialchars($bien['type_annonce']) ?></div>
    <div class="info"><span class="label">Adresse :</span> <?= htmlspecialchars($bien['adresse']) ?>, <?= htmlspecialchars($bien['ville']) ?></div>
    <div class="info"><span class="label">Prix :</span> <?= number_format($bien['prix'], 0, ',', ' ') ?> FCFA</div>
    <div class="info"><span class="label">Superficie :</span> <?= $bien['superficie'] ?> m²</div>
    <div class="info"><span class="label">Pièces :</span> <?= $bien['nb_pieces'] ?> | Lits : <?= $bien['nb_lits'] ?></div>
    <div class="info"><span class="label">Options :</span>
        <?= $bien['wifi'] ? 'Wi-Fi, ' : '' ?>
        <?= $bien['climatisation'] ? 'Climatisation, ' : '' ?>
        <?= $bien['cuisine'] ? 'Cuisine, ' : '' ?>
        <?= $bien['parking'] ? 'Parking' : '' ?>
    </div>
    <div class="info"><span class="label">Date d'ajout :</span> <?= $bien['date_ajout'] ?></div>
    <div class="info"><span class="label">Propriétaire :</span> <?= htmlspecialchars($bien['prenom_proprio'] . ' ' . $bien['nom_proprio']) ?></div>

    <a href="index.php" class="back-link">← Retour à la liste</a>
</div>

</body>
</html>