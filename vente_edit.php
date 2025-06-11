<?php
require_once 'connexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: vente_list.php");
    exit;
}

$id = $_GET['id'];

// Récupération du bien
$stmt = $pdo->prepare("SELECT * FROM BienImmobilier WHERE id_property = ? AND type_annonce = 'vente'");
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

// Récupération des catégories
$stmtCat = $pdo->query("SELECT id_categorie, nom FROM Categorie ORDER BY nom");
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
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
    $id_categorie = $_POST['id_categorie'] ?? null;

    // Validation
    if (empty($titre)) $errors[] = "Le titre est requis.";
    if (empty($ville)) $errors[] = "La ville est requise.";
    if (empty($prix)) $errors[] = "Le prix est requis.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE BienImmobilier SET 
            titre=?, description=?, type_bien=?, adresse=?, ville=?, prix=?, mode_tarif=?, 
            superficie=?, nb_pieces=?, nb_lits=?, wifi=?, climatisation=?, cuisine=?, parking=?, id_categorie=? 
            WHERE id_property=? AND type_annonce='vente'");

        $stmt->execute([
            $titre, $description, $type_bien, $adresse, $ville, $prix, $mode_tarif,
            $superficie, $nb_pieces, $nb_lits,
            $wifi, $climatisation, $cuisine, $parking, $id_categorie, $id
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

        header("Location: vente_list.php");
        exit;
    }
}

// Suppression d’une image
if (isset($_GET['delete_img'])) {
    $imgId = $_GET['delete_img'];
    $stmtImg = $pdo->prepare("SELECT url_image FROM Image WHERE id_image = ? AND id_property = ?");
    $stmtImg->execute([$imgId, $id]);
    $img = $stmtImg->fetch();
    if ($img) {
        unlink($img['url_image']);
        $pdo->prepare("DELETE FROM Image WHERE id_image = ?")->execute([$imgId]);
        header("Location: vente_edit.php?id=$id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un bien à vendre</title>
    <link rel="stylesheet" href="location&vente.css">
</head>
<body>
<div class="main-content">
    <h1>Modifier un bien à vendre</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Titre : <input type="text" name="titre" value="<?= htmlspecialchars($bien['titre']) ?>"></label><br>
        <label>Description : <textarea name="description"><?= htmlspecialchars($bien['description']) ?></textarea></label><br>
        <label>Type de bien :
            <select name="type_bien">
                <option value="maison" <?= $bien['type_bien'] == 'maison' ? 'selected' : '' ?>>Maison</option>
                <option value="appartement" <?= $bien['type_bien'] == 'appartement' ? 'selected' : '' ?>>Appartement</option>
                <option value="studio" <?= $bien['type_bien'] == 'studio' ? 'selected' : '' ?>>Studio</option>
            </select>
        </label><br>
        <label>Adresse : <input type="text" name="adresse" value="<?= htmlspecialchars($bien['adresse']) ?>"></label><br>
        <label>Ville : <input type="text" name="ville" value="<?= htmlspecialchars($bien['ville']) ?>"></label><br>
        <label>Prix (€) : <input type="number" name="prix" value="<?= $bien['prix'] ?>"></label><br>
        <label>Mode tarif :
            <select name="mode_tarif">
                <option value="mois" <?= $bien['mode_tarif'] == 'mois' ? 'selected' : '' ?>>Par mois</option>
                <option value="vente" <?= $bien['mode_tarif'] == 'vente' ? 'selected' : '' ?>>Achat</option>
            </select>
        </label><br>
        <label>Superficie (m²) : <input type="number" name="superficie" value="<?= $bien['superficie'] ?>"></label><br>
        <label>Nombre de pièces : <input type="number" name="nb_pieces" value="<?= $bien['nb_pieces'] ?>"></label><br>
        <label>Nombre de lits : <input type="number" name="nb_lits" value="<?= $bien['nb_lits'] ?>"></label><br>

        <label><input type="checkbox" name="wifi" <?= $bien['wifi'] ? 'checked' : '' ?>> Wifi</label>
        <label><input type="checkbox" name="climatisation" <?= $bien['climatisation'] ? 'checked' : '' ?>> Climatisation</label>
        <label><input type="checkbox" name="cuisine" <?= $bien['cuisine'] ? 'checked' : '' ?>> Cuisine</label>
        <label><input type="checkbox" name="parking" <?= $bien['parking'] ? 'checked' : '' ?>> Parking</label><br>

        <label>Catégorie :
            <select name="id_categorie">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id_categorie'] ?>" <?= $bien['id_categorie'] == $cat['id_categorie'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>Ajouter des images : <input type="file" name="images[]" multiple></label><br><br>

        <button type="submit">Enregistrer</button>
        <a href="vente_list.php">Annuler</a>
    </form>

    <h3>Images existantes</h3>
    <div class="image-list">
        <?php foreach ($images as $img): ?>
            <div class="img-thumb">
                <img src="<?= $img['url_image'] ?>" height="100">
                <a href="?id=<?= $id ?>&delete_img=<?= $img['id_image'] ?>" onclick="return confirm('Supprimer cette image ?')">Supprimer</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
