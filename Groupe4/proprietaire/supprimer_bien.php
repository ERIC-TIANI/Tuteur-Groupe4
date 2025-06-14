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

// Vérifier que le bien appartient à ce user
$stmt = $pdo->prepare("SELECT titre FROM bienimmobilier WHERE id_property = ? AND id_user = ?");
$stmt->execute([$id_bien, $id_user]);
$bien = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bien) {
    header("Location: mes_biens.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Suppression
    $delete = $pdo->prepare("DELETE FROM bienimmobilier WHERE id_property = ? AND id_user = ?");
    $delete->execute([$id_bien, $id_user]);

    // Optionnel : supprimer images liées aussi si nécessaire
    $deleteImages = $pdo->prepare("DELETE FROM image WHERE id_property = ?");
    $deleteImages->execute([$id_bien]);

    header("Location: mes_biens.php?msg=delete_success");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Supprimer bien immobilier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container my-5">
    <h2 class="mb-4 text-danger">Confirmer la suppression du bien</h2>

    <p>Êtes-vous sûr de vouloir supprimer le bien <strong><?= htmlspecialchars($bien['titre']) ?></strong> ? Cette action est irréversible.</p>

    <form method="post" action="">
        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
        <a href="mes_biens.php" class="btn btn-secondary ms-2">Annuler</a>
    </form>
</div>

</body>
</html>
