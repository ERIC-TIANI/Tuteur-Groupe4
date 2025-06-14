<?php
session_start();
require 'connexion.php';

$id = $_GET['id'] ?? null;
$userId = $_SESSION['id_user'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// Récupération des données du bien
$stmt = $pdo->prepare("SELECT * FROM BienImmobilier WHERE id_property = ?");
$stmt->execute([$id]);
$bien = $stmt->fetch();

if (!$bien) {
    echo "Bien non trouvé.";
    exit;
}

// Récupération des images du bien
$stmtImg = $pdo->prepare("SELECT url_image FROM Image WHERE id_property = ?");
$stmtImg->execute([$id]);
$images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

// Récupération des avis
$stmtAvis = $pdo->prepare("
    SELECT a.commentaire, a.note, a.date_review, u.nom 
    FROM Avis a 
    JOIN Utilisateur u ON a.id_user = u.id_user 
    WHERE a.id_property = ?
    ORDER BY a.date_review DESC
");
$stmtAvis->execute([$id]);
$avisList = $stmtAvis->fetchAll();

// Description enrichie par IA (simulation ici)
function enrichirDescription($bien) {
    $details = [];
    if ($bien['wifi']) $details[] = "Wi-Fi gratuit";
    if ($bien['climatisation']) $details[] = "climatisation moderne";
    if ($bien['cuisine']) $details[] = "cuisine équipée";
    if ($bien['parking']) $details[] = "parking sécurisé";

    $texte = "Ce " . $bien['type_bien'] . " situé à " . $bien['ville'] . " propose " . implode(", ", $details) . ". Parfait pour votre séjour à " . $bien['ville'] . ".";
    
    // Appel à Gemini ici si API intégrée
    // return callGeminiAPI($texte);
    return $texte;
}

$descriptionIA = enrichirDescription($bien);

// Insertion d’un avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
    $note = $_POST['note'] ?? 0;
    $commentaire = $_POST['commentaire'] ?? '';
    if ($note >= 1 && $note <= 5 && !empty($commentaire)) {
        $stmtAdd = $pdo->prepare("INSERT INTO Avis (note, commentaire, date_review, id_user, id_property) VALUES (?, ?, NOW(), ?, ?)");
        $stmtAdd->execute([$note, $commentaire, $userId, $id]);
        header("Location: details_bien.php?id=$id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($bien['titre']) ?> - Détails</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="details_bien.css">
    <style>
        body { font-family: Arial; margin: 20px; }
        .gallery img { max-width: 300px; margin-right: 10px; border-radius: 8px; }
        .info { margin-top: 20px; }
        .description, .carte, .avis-section, .ajout-avis { margin-top: 40px; }
        .avis { border-top: 1px solid #ccc; padding: 10px 0; }
        .note { color: #ffc107; }
        .form-group { margin-bottom: 10px; }
        input[type=number], textarea { width: 100%; padding: 5px; }
        iframe { width: 100%; height: 300px; border: none; }
    </style>
</head>
<body>

<h1><?= htmlspecialchars($bien['titre']) ?></h1>
<div class="gallery">
    <?php foreach ($images as $img): ?>
        <img src="<?= htmlspecialchars($img) ?>" alt="Image du bien">
    <?php endforeach; ?>
</div>

<div class="info">
    <p><strong>Type :</strong> <?= htmlspecialchars($bien['type_bien']) ?></p>
    <p><strong>Adresse :</strong> <?= htmlspecialchars($bien['adresse']) ?>, <?= htmlspecialchars($bien['ville']) ?></p>
    <p><strong>Prix :</strong> <?= number_format($bien['prix'], 0, ',', ' ') ?> XAF (<?= $bien['mode_tarif'] ?>)</p>
    <p><strong>Surface :</strong> <?= $bien['superficie'] ?> m²</p>
    <p><strong>Pièces :</strong> <?= $bien['nb_pieces'] ?> | Lits : <?= $bien['nb_lits'] ?></p>
    <a href="reserver.php?id_property=<?= $bien['id_property'] ?>" class="btn-reserver" style="display:inline-block;margin-top:15px;padding:10px 20px;background-color:#4CAF50;color:white;text-decoration:none;border-radius:5px;">Réserver ce bien</a>
</div>

<div class="description">
    <h2>Description</h2>
    <p><?= nl2br(htmlspecialchars($bien['description'])) ?></p>
    <p style="color: #555;"><em><?= htmlspecialchars($descriptionIA) ?></em></p>
</div>

<div class="carte">
    <h2>Localisation</h2>
    <iframe
        src="https://www.google.com/maps?q=<?= urlencode($bien['adresse'] . ',' . $bien['ville'] . ',' . $bien['pays']) ?>&output=embed">
    </iframe>
</div>

<div class="avis-section">
    <h2>Commentaires</h2>
    <?php foreach ($avisList as $avis): ?>
        <div class="avis">
            <p><strong><?= htmlspecialchars($avis['nom']) ?></strong> - <?= $avis['date_review'] ?> - 
                <span class="note"><?= str_repeat('★', $avis['note']) ?><?= str_repeat('☆', 5 - $avis['note']) ?></span>
            </p>
            <p><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($userId): ?>
<div class="ajout-avis">
    <h3>Laisser un commentaire</h3>
    <form method="post">
        <div class="form-group">
            <label for="note">Note (1-5):</label>
            <input type="number" id="note" name="note" min="1" max="5" required>
        </div>
        <div class="form-group">
            <label for="commentaire">Commentaire :</label>
            <textarea id="commentaire" name="commentaire" rows="4" required></textarea>
        </div>
        <button type="submit">Envoyer</button>
    </form>
</div>
<?php else: ?>
<p><a href="login.php">Connectez-vous</a> pour laisser un avis.</p>
<?php endif; ?>

</body>
</html>
