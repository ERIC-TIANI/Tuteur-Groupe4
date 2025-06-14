<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'proprietaire') {
    header("Location: auth.php");
    exit;
}

require 'config.php';
$id_user = $_SESSION['id_user'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: mes_biens.php");
    exit;
}

$id_bien = (int) $_GET['id'];

// Récupérer le bien et vérifier qu'il appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM bienimmobilier WHERE id_property = ? AND id_user = ?");
$stmt->execute([$id_bien, $id_user]);
$bien = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bien) {
    // Bien non trouvé ou pas à cet utilisateur
    header("Location: mes_biens.php");
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = $_POST['prix'] ?? '';
    $type_annonce = $_POST['type_annonce'] ?? '';

    // Validation simple
    if ($titre === '') $errors[] = "Le titre est obligatoire.";
    if ($description === '') $errors[] = "La description est obligatoire.";
    if (!is_numeric($prix) || $prix <= 0) $errors[] = "Le prix doit être un nombre positif.";
    if (!in_array($type_annonce, ['vente', 'location'])) $errors[] = "Type d'annonce invalide.";

    if (empty($errors)) {
        // Mise à jour en base
        $update = $pdo->prepare("UPDATE bienimmobilier SET titre = ?, description = ?, prix = ?, type_annonce = ? WHERE id_property = ? AND id_user = ?");
        $update->execute([$titre, $description, $prix, $type_annonce, $id_bien, $id_user]);

        header("Location: mes_biens.php?msg=modif_success");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Modifier bien immobilier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container my-5">
    <h2 class="mb-4">Modifier votre bien immobilier</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" id="titre" name="titre" class="form-control" value="<?= htmlspecialchars($_POST['titre'] ?? $bien['titre']) ?>" required />
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="5" required><?= htmlspecialchars($_POST['description'] ?? $bien['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix (€)</label>
            <input type="number" step="0.01" min="0" id="prix" name="prix" class="form-control" value="<?= htmlspecialchars($_POST['prix'] ?? $bien['prix']) ?>" required />
        </div>

        <div class="mb-3">
            <label class="form-label">Type d'annonce</label>
            <select name="type_annonce" class="form-select" required>
                <option value="vente" <?= (($_POST['type_annonce'] ?? $bien['type_annonce']) === 'vente') ? 'selected' : '' ?>>Vente</option>
                <option value="location" <?= (($_POST['type_annonce'] ?? $bien['type_annonce']) === 'location') ? 'selected' : '' ?>>Location</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="mes_biens.php" class="btn btn-secondary ms-2">Annuler</a>
    </form>
</div>

</body>
</html>
