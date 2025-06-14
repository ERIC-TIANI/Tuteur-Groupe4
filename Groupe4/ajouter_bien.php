<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'proprietaire') {
    header("Location: auth.php");
    exit;
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_SESSION['id_user'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $type_bien = $_POST['type_bien'];
    $type_annonce = $_POST['type_annonce'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $prix = $_POST['prix'];
    $superficie = $_POST['superficie'];
    $nb_pieces = $_POST['nb_pieces'];
    $wifi = isset($_POST['wifi']) ? 1 : 0;
    $clim = isset($_POST['climatisation']) ? 1 : 0;
    $cuisine = isset($_POST['cuisine']) ? 1 : 0;
    $parking = isset($_POST['parking']) ? 1 : 0;
    $id_categorie = $_POST['id_categorie'];

    $stmt = $pdo->prepare("INSERT INTO bienimmobilier (titre, description, type_bien, type_annonce, adresse, ville, prix, superficie, nb_pieces, wifi, climatisation, cuisine, parking, date_ajout, id_user, id_categorie)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->execute([$titre, $description, $type_bien, $type_annonce, $adresse, $ville, $prix, $superficie, $nb_pieces, $wifi, $clim, $cuisine, $parking, $id_user, $id_categorie]);

    $id_property = $pdo->lastInsertId();

    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = 'images/';
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['images']['name'][$key]);
            $targetFile = $uploadDir . time() . "_" . $fileName;
            if (move_uploaded_file($tmpName, $targetFile)) {
                $insertImg = $pdo->prepare("INSERT INTO image (id_property, url_image) VALUES (?, ?)");
                $insertImg->execute([$id_property, $targetFile]);
            }
        }
    }

    header("Location: mes_biens.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categorie")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un bien</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        form { background: white; padding: 20px; max-width: 700px; margin: auto; border-radius: 8px; }
        input, textarea, select { width: 100%; margin-bottom: 10px; padding: 10px; border-radius: 4px; border: 1px solid #ccc; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        .checkbox-group { display: flex; gap: 10px; margin-top: 10px; }
        button { background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #219150; }
    </style>
</head>
<body>
<h2 style="text-align:center;">Ajouter un nouveau bien</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Titre</label>
    <input type="text" name="titre" required>

    <label>Description</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Type de bien</label>
    <input type="text" name="type_bien" required>

    <label>Type d'annonce</label>
    <select name="type_annonce" required>
        <option value="location">Location</option>
        <option value="vente">Vente</option>
    </select>

    <label>Adresse</label>
    <input type="text" name="adresse" required>

    <label>Ville</label>
    <input type="text" name="ville" required>

    <label>Prix</label>
    <input type="number" name="prix" required>

    <label>Superficie (m²)</label>
    <input type="number" name="superficie" required>

    <label>Nombre de pièces</label>
    <input type="number" name="nb_pieces" required>

    <label>Catégorie</label>
    <select name="id_categorie" required>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Équipements</label>
    <div class="checkbox-group">
        <label><input type="checkbox" name="wifi"> WiFi</label>
        <label><input type="checkbox" name="climatisation"> Climatisation</label>
        <label><input type="checkbox" name="cuisine"> Cuisine</label>
        <label><input type="checkbox" name="parking"> Parking</label>
    </div>

    <label>Images (vous pouvez en sélectionner plusieurs)</label>
    <input type="file" name="images[]" multiple accept="image/*">

    <button type="submit">Enregistrer le bien</button>
</form>
</body>
</html>
