<?php
session_start();

// Connexion à la base de données
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

// Récupérer l'ID du bien
$id_property = isset($_GET['id_property']) ? (int)$_GET['id_property'] : 0;
$userId = $_SESSION['id_user'] ?? null;

if ($id_property <= 0) {
    die("Bien immobilier invalide.");
}

// Récupérer les infos du bien
$sql = "SELECT b.*, c.nom as nom_categorie 
        FROM bienimmobilier b
        LEFT JOIN categorie c ON b.id_categorie = c.id_categorie
        WHERE b.id_property = :id_property";
$stmt = $conn->prepare($sql);
$stmt->execute([':id_property' => $id_property]);
$bien = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bien) {
    die("Bien immobilier non trouvé.");
}

// Récupérer les images
$sqlImages = "SELECT url_image FROM image WHERE id_property = :id_property";
$stmtImages = $conn->prepare($sqlImages);
$stmtImages->execute([':id_property' => $id_property]);
$images = $stmtImages->fetchAll(PDO::FETCH_COLUMN);

// Ajouter un avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
    $note = (int)($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    if ($note >= 1 && $note <= 5 && !empty($commentaire)) {
        $stmt = $conn->prepare("INSERT INTO avis (note, commentaire, date_review, id_user, id_property) VALUES (?, ?, NOW(), ?, ?)");
        $stmt->execute([$note, $commentaire, $userId, $id_property]);
        header("Location: details_bien.php?id_property=$id_property");
        exit;
    }
}

// Récupérer les avis
$sqlAvis = "SELECT a.*, u.nom 
            FROM avis a
            JOIN utilisateur u ON a.id_user = u.id_user
            WHERE a.id_property = :id_property
            ORDER BY a.date_review DESC";
$stmtAvis = $conn->prepare($sqlAvis);
$stmtAvis->execute([':id_property' => $id_property]);
$avisList = $stmtAvis->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($bien['titre']) ?> - Détails</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .images-gallery {
            display: flex;
            gap: 10px;
            overflow-x: auto;
        }
        .images-gallery img {
            height: 200px;
            border-radius: 6px;
            object-fit: cover;
        }
        .caracteristiques {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .caracteristique-item {
            background: #e0f7fa;
            padding: 5px 10px;
            border-radius: 4px;
            color: #00796b;
            font-size: 0.9em;
        }
        .avis {
            border-top: 1px solid #ccc;
            padding-top: 15px;
            margin-top: 20px;
        }
        .note {
            color: #f39c12;
        }
        iframe {
            width: 100%;
            height: 300px;
            border: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1><?= htmlspecialchars($bien['titre']) ?></h1>

    <div class="images-gallery">
        <?php foreach ($images as $img): ?>
            <img src="<?= htmlspecialchars($img) ?>" alt="Image du bien">
        <?php endforeach; ?>
    </div>

    <p><strong>Catégorie :</strong> <?= htmlspecialchars($bien['nom_categorie'] ?? 'Non renseignée') ?></p>
    <p><strong>Type :</strong> <?= htmlspecialchars($bien['type_bien']) ?></p>
    <p><strong>Description :</strong><br><?= nl2br(htmlspecialchars($bien['description'])) ?></p>
    <p><strong>Surface :</strong> <?= $bien['superficie'] ?> m²</p>
    <p><strong>Nombre de pièces :</strong> <?= $bien['nb_pieces'] ?></p>

    <div class="caracteristiques">
        <?php if ($bien['wifi']) echo '<span class="caracteristique-item">Wi-Fi</span>'; ?>
        <?php if ($bien['climatisation']) echo '<span class="caracteristique-item">Climatisation</span>'; ?>
        <?php if ($bien['cuisine']) echo '<span class="caracteristique-item">Cuisine</span>'; ?>
        <?php if ($bien['parking']) echo '<span class="caracteristique-item">Parking</span>'; ?>
    </div>

    <p style="font-size: 1.2em; margin-top: 15px;"><strong>Prix :</strong> <?= number_format($bien['prix'], 0, ',', ' ') ?> F
        <?= $bien['type_annonce'] === 'location' ? '/mois' : '' ?></p>

    <a href="reserver.php?id_property=<?= $bien['id_property'] ?>" class="btn-reserver" style="display:inline-block;margin-top:15px;padding:10px 20px;background-color:#4CAF50;color:white;text-decoration:none;border-radius:5px;">Réserver ce bien</a>

    <!-- Carte -->
    <?php if (!empty($bien['adresse']) && !empty($bien['ville']) && !empty($bien['pays'])): ?>
        <div style="margin-top: 40px;">
            <h2>Localisation</h2>
            <iframe src="https://www.google.com/maps?q=<?= urlencode($bien['adresse'] . ',' . $bien['ville'] . ',' . $bien['pays']) ?>&output=embed" loading="lazy"></iframe>
        </div>
    <?php endif; ?>

    <!-- Avis -->
    <div class="avis">
        <h2>Avis des utilisateurs</h2>
        <?php if ($avisList): ?>
            <?php foreach ($avisList as $avis): ?>
                <div style="margin-bottom:15px;">
                    <strong><?= htmlspecialchars($avis['nom']) ?></strong> - <?= $avis['date_review'] ?> <br>
                    <span class="note"><?= str_repeat("★", $avis['note']) . str_repeat("☆", 5 - $avis['note']) ?></span>
                    <p><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun avis pour le moment.</p>
        <?php endif; ?>
    </div>

    <!-- Formulaire d'avis -->
    <?php if ($userId): ?>
        <div style="margin-top: 30px;">
            <h3>Laisser un avis</h3>
            <form method="post">
                <label for="note">Note (1 à 5):</label><br>
                <input type="number" name="note" min="1" max="5" required><br><br>

                <label for="commentaire">Commentaire :</label><br>
                <textarea name="commentaire" rows="4" required style="width:100%;"></textarea><br><br>

                <button type="submit" style="padding:8px 20px;background:#3498db;color:white;border:none;border-radius:4px;">Envoyer</button>
            </form>
        </div>
    <?php else: ?>
        <p><a href="login.php">Connectez-vous</a> pour laisser un avis.</p>
    <?php endif; ?>

</div>
</body>
</html>
