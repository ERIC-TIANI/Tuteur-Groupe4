<?php
session_start();
require 'connexion.php';
$userId = $_SESSION['id_user'] ?? null;
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'client') {
    die("Accès refusé. Seul les client peuvent accéder à cette page.");
}
$ville = $_GET['ville'] ?? '';
$nb = $_GET['nb'] ?? '';
$type_nb = $_GET['type_nb'] ?? 'chambre';
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';

function getUserFavoris($pdo, $userId) {
    if (!$userId) return [];
    $stmt = $pdo->prepare("SELECT id_property FROM Favori WHERE id_user = ?");
    $stmt->execute([$userId]);
    return array_column($stmt->fetchAll(), 'id_property');
}

$favoris = getUserFavoris($pdo, $userId);

$sql = "
SELECT b.*, 
       (SELECT url_image FROM Image i WHERE i.id_property = b.id_property ORDER BY id_image ASC LIMIT 1) AS image_url 
FROM BienImmobilier b 
WHERE b.type_annonce='location'
";

$params = [];
if (!empty($ville)) {
    $sql .= " AND b.ville LIKE ?";
    $params[] = "%$ville%";
}
if (!empty($nb)) {
    if ($type_nb === 'lit') {
        $sql .= " AND b.nb_lits >= ?";
    } else {
        $sql .= " AND b.nb_pieces >= ?";
    }
    $params[] = $nb;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$biens = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Location de biens immobiliers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="location.css">
</head>
<body>
<header class="header">
    <div class="logo">Immobilier.com</div>
    <nav class="nav">
        <a href="vente.php">Vente</a>
        <a href="IA.php">IA</a>
    </nav>
    <div class="actions">
        <span class="lang">🇫🇷</span>

        <!-- Icône message vers admin -->
        <a href="send_message_to_admin.php" title="Envoyer un message à l'administrateur" style="margin-right: 15px; font-size: 1.5rem; color: #333;">
            <i class="fa-solid fa-envelope"></i>
        </a>

        <a href="logout.php" class="btn">Se déconnecter</a>
    </div>
</header>


<section class="search-bar">
    <form method="get" action="">
        <input type="text" name="ville" placeholder="Ville (ex: Kribi)" value="<?= htmlspecialchars($ville) ?>">
        <input type="date" name="date_debut" value="<?= $date_debut ?>">
        <input type="date" name="date_fin" value="<?= $date_fin ?>">

        <select name="type_nb">
            <option value="chambre" <?= $type_nb == 'chambre' ? 'selected' : '' ?>>Chambre</option>
            <option value="lit" <?= $type_nb == 'lit' ? 'selected' : '' ?>>Lit</option>
        </select>
        <input type="number" name="nb" min="1" placeholder="Nombre" value="<?= htmlspecialchars($nb) ?>">

        <button type="submit">Rechercher</button>
    </form>
</section>

<section class="results">
    <?php foreach ($biens as $bien): ?>
        <div class="card">
            <a href="details_bien.php?id=<?= $bien['id_property'] ?>" class="thecard">
            <div class="image-container">
                <img src="<?= htmlspecialchars($bien['image_url'] ?? 'uploads/default.jpg') ?>" alt="Image du bien">
                <button 
                    class="heart-btn <?= in_array($bien['id_property'], $favoris) ? 'active' : '' ?>" 
                    data-id="<?= $bien['id_property'] ?>">
                    <i class="fa-heart <?= in_array($bien['id_property'], $favoris) ? 'fa-solid' : 'fa-regular' ?>"></i>
                </button>
            </div>
            <h3><?= htmlspecialchars($bien['titre']) ?></h3>
            <p><?= htmlspecialchars($bien['type_bien']) ?> - <?= htmlspecialchars($bien['ville']) ?>, <?= htmlspecialchars($bien['pays']) ?></p>
            <p><strong><?= number_format($bien['prix'], 0, ',', ' ') ?> XAF</strong> - <?= $bien['mode_tarif'] ?></p>
            <p><?= $bien['superficie'] ?> m²</p>
            <a>
        </div>
    <?php endforeach; ?>
</section>

<script>
document.querySelectorAll('.heart-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const propertyId = this.dataset.id;
        const isActive = this.classList.contains('active');
        const formData = new FormData();
        formData.append('id_property', propertyId);
        formData.append('action', isActive ? 'remove' : 'add');

        fetch('ajouter_favori.php', {
            method: 'POST',
            body: formData
        }).then(res => res.text()).then(data => {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-solid');
            icon.classList.toggle('fa-regular');
        });
    });
});
</script>

</body>
</html>