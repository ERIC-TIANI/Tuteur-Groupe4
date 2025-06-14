<?php
require_once 'connexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: location_list.php");
    exit;
}

// Récupération des catégories
$stmtCat = $pdo->query("SELECT id_categorie, nom FROM Categorie ORDER BY nom");
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Récupération du bien à modifier
$stmt = $pdo->prepare("SELECT * FROM BienImmobilier WHERE id_property = ? AND type_annonce = 'location'");
$stmt->execute([$id]);
$bien = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bien) {
    echo "Bien non trouvé.";
    exit;
}

// Récupération des images
$stmtImg = $pdo->prepare("SELECT * FROM Image WHERE id_property = ?");
$stmtImg->execute([$id]);
$images = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $type_bien = $_POST['type_bien'];
    $adresse = trim($_POST['adresse']);
    $ville = trim($_POST['ville']);
    $prix = $_POST['prix'];
    $mode_tarif = $_POST['mode_tarif'];
    $superficie = $_POST['superficie'];
    $nb_pieces = $_POST['nb_pieces'];
    $nb_lits = $_POST['nb_lits'];
    $wifi = isset($_POST['wifi']) ? 1 : 0;
    $climatisation = isset($_POST['climatisation']) ? 1 : 0;
    $cuisine = isset($_POST['cuisine']) ? 1 : 0;
    $parking = isset($_POST['parking']) ? 1 : 0;
    $id_categorie = $_POST['id_categorie'];

    if (empty($titre)) $errors[] = "Le titre est obligatoire.";
    // ... autres validations ici

    if (empty($errors)) {
        $sqlUpdate = "UPDATE BienImmobilier SET
            titre = :titre, description = :description, type_bien = :type_bien,
            adresse = :adresse, ville = :ville, prix = :prix, mode_tarif = :mode_tarif,
            superficie = :superficie, nb_pieces = :nb_pieces, nb_lits = :nb_lits,
            wifi = :wifi, climatisation = :climatisation, cuisine = :cuisine,
            parking = :parking, id_categorie = :id_categorie
            WHERE id_property = :id";

        $stmt = $pdo->prepare($sqlUpdate);
        $stmt->execute([
            ':titre' => $titre,
            ':description' => $description,
            ':type_bien' => $type_bien,
            ':adresse' => $adresse,
            ':ville' => $ville,
            ':prix' => $prix,
            ':mode_tarif' => $mode_tarif,
            ':superficie' => $superficie,
            ':nb_pieces' => $nb_pieces,
            ':nb_lits' => $nb_lits,
            ':wifi' => $wifi,
            ':climatisation' => $climatisation,
            ':cuisine' => $cuisine,
            ':parking' => $parking,
            ':id_categorie' => $id_categorie,
            ':id' => $id
        ]);

        // Ajout d’images
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = 'images/';
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $imageName = basename($_FILES['images']['name'][$key]);
                $targetPath = $uploadDir . time() . '_' . $imageName;
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $stmtImg = $pdo->prepare("INSERT INTO Image (url_image, id_property) VALUES (?, ?)");
                    $stmtImg->execute([$targetPath, $id]);
                }
            }
        }

        // Suppression d’images
        if (!empty($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $imgId) {
                $stmtDel = $pdo->prepare("SELECT url_image FROM Image WHERE id_image = ?");
                $stmtDel->execute([$imgId]);
                $img = $stmtDel->fetch();
                if ($img && file_exists($img['url_image'])) {
                    unlink($img['url_image']);
                }
                $pdo->prepare("DELETE FROM Image WHERE id_image = ?")->execute([$imgId]);
            }
        }

        header("Location: location_list.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un bien à louer</title>
    <link rel="stylesheet" href="location&vente.css">
</head>
<body>
<div class="main-content">
    <h1>Modifier un bien à louer</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

    <label for="titre">Titre :</label>
    <input type="text" name="titre" value="<?= htmlspecialchars($bien['titre']) ?>">

    <label for="description">Description :</label>
    <textarea name="description"><?= htmlspecialchars($bien['description']) ?></textarea>

    <label for="type_bien">Type de bien :</label>
    <input type="text" name="type_bien" value="<?= htmlspecialchars($bien['type_bien']) ?>">

    <label for="adresse">Adresse :</label>
    <input type="text" name="adresse" value="<?= htmlspecialchars($bien['adresse']) ?>">

    <label for="ville">Ville :</label>
    <input type="text" name="ville" value="<?= htmlspecialchars($bien['ville']) ?>">

    <label for="prix">Prix :</label>
    <input type="number" name="prix" value="<?= htmlspecialchars($bien['prix']) ?>">

    <label for="mode_tarif">Mode tarif :</label>
    <select name="mode_tarif">
        <option value="jour" <?= $bien['mode_tarif'] == 'jour' ? 'selected' : '' ?>>Par jour</option>
        <option value="semaine" <?= $bien['mode_tarif'] == 'semaine' ? 'selected' : '' ?>>Par semaine</option>
        <option value="mois" <?= $bien['mode_tarif'] == 'mois' ? 'selected' : '' ?>>Par mois</option>
    </select>

    <label for="superficie">Superficie :</label>
    <input type="number" name="superficie" value="<?= htmlspecialchars($bien['superficie']) ?>">

    <label for="nb_pieces">Nombre de pièces :</label>
    <input type="number" name="nb_pieces" value="<?= htmlspecialchars($bien['nb_pieces']) ?>">

    <label for="nb_lits">Nombre de lits :</label>
    <input type="number" name="nb_lits" value="<?= htmlspecialchars($bien['nb_lits']) ?>">

    <label><input type="checkbox" name="wifi" <?= $bien['wifi'] ? 'checked' : '' ?>> Wifi</label>
    <label><input type="checkbox" name="climatisation" <?= $bien['climatisation'] ? 'checked' : '' ?>> Climatisation</label>
    <label><input type="checkbox" name="cuisine" <?= $bien['cuisine'] ? 'checked' : '' ?>> Cuisine</label>
    <label><input type="checkbox" name="parking" <?= $bien['parking'] ? 'checked' : '' ?>> Parking</label>

    <label for="id_categorie">Catégorie :</label>
    <select name="id_categorie">
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id_categorie'] ?>" <?= $cat['id_categorie'] == $bien['id_categorie'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>


        <h3>Images existantes</h3>
        <div class="image-list">
            <?php foreach ($images as $img): ?>
                <div class="image-item">
                    <img src="<?= htmlspecialchars($img['url_image']) ?>" width="100">
                    <label><input type="checkbox" name="delete_images[]" value="<?= $img['id_image'] ?>"> Supprimer</label>
                </div>
            <?php endforeach; ?>
        </div>

        <label for="images">Ajouter des images :</label>
        <input type="file" name="images[]" multiple accept="image/*">

        <button type="submit">Enregistrer les modifications</button>
        <a href="location_list.php">Annuler</a>
    </form>
</div>
</body>
</html>
