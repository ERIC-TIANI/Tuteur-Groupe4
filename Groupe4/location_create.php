<?php
require_once 'connexion.php';

// Récupération des catégories
$sqlCat = "SELECT id_categorie, nom FROM Categorie ORDER BY nom";
$stmtCat = $pdo->prepare($sqlCat);
$stmtCat->execute();
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et valider les données du formulaire
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type_bien = $_POST['type_bien'] ?? '';
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $prix = $_POST['prix'] ?? '';
    $mode_tarif = $_POST['mode_tarif'] ?? '';
    $superficie = $_POST['superficie'] ?? '';
    $nb_pieces = $_POST['nb_pieces'] ?? '';
    $nb_lits = $_POST['nb_lits'] ?? '';
    $wifi = isset($_POST['wifi']) ? 1 : 0;
    $climatisation = isset($_POST['climatisation']) ? 1 : 0;
    $cuisine = isset($_POST['cuisine']) ? 1 : 0;
    $parking = isset($_POST['parking']) ? 1 : 0;
    $id_categorie = $_POST['id_categorie'] ?? null;
    $id_user = 1; // À adapter avec l'utilisateur connecté
    $date_ajout = date('Y-m-d');

    // Validation simple
    if (!$titre) $errors[] = "Le titre est obligatoire.";
    if (!$type_bien || !in_array($type_bien, ['appartement', 'villa', 'studio', 'chambre'])) $errors[] = "Type de bien invalide.";
    if (!$adresse) $errors[] = "L'adresse est obligatoire.";
    if (!$ville) $errors[] = "La ville est obligatoire.";
    if (!is_numeric($prix) || $prix < 0) $errors[] = "Prix invalide.";
    if (!$mode_tarif || !in_array($mode_tarif, ['fixe', 'par_nuit'])) $errors[] = "Mode de tarif invalide.";
    if (!is_numeric($superficie) || $superficie <= 0) $errors[] = "Superficie invalide.";
    if (!is_numeric($nb_pieces) || $nb_pieces < 0) $errors[] = "Nombre de pièces invalide.";
    if (!is_numeric($nb_lits) || $nb_lits < 0) $errors[] = "Nombre de lits invalide.";
    if (!$id_categorie) $errors[] = "Veuillez sélectionner une catégorie.";

    if (empty($errors)) {
        // Insertion du bien
        $sqlInsert = "INSERT INTO BienImmobilier 
            (titre, description, type_bien, type_annonce, adresse, ville, prix, mode_tarif, superficie, nb_pieces, nb_lits, wifi, climatisation, cuisine, parking, date_ajout, id_user, id_categorie)
            VALUES 
            (:titre, :description, :type_bien, 'location', :adresse, :ville, :prix, :mode_tarif, :superficie, :nb_pieces, :nb_lits, :wifi, :climatisation, :cuisine, :parking, :date_ajout, :id_user, :id_categorie)";

        $stmt = $pdo->prepare($sqlInsert);
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
            ':date_ajout' => $date_ajout,
            ':id_user' => $id_user,
            ':id_categorie' => $id_categorie,
        ]);

        $id_property = $pdo->lastInsertId();

        // Traitement des images
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $index => $fileName) {
                $fileTmpPath = $_FILES['images']['tmp_name'][$index];
                $fileError = $_FILES['images']['error'][$index];

                if ($fileError === UPLOAD_ERR_OK) {
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (in_array($fileExtension, $allowedExts)) {
                        $newFileName = uniqid('img_') . '.' . $fileExtension;
                        $uploadDir = 'images/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        $destPath = $uploadDir . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            // Enregistrer en base
                            $sqlImg = "INSERT INTO Image (url_image, id_property) VALUES (:url, :id_property)";
                            $stmtImg = $pdo->prepare($sqlImg);
                            $stmtImg->execute([
                                ':url' => $destPath,
                                ':id_property' => $id_property
                            ]);
                        } else {
                            $errors[] = "Erreur de déplacement de l’image : $fileName";
                        }
                    } else {
                        $errors[] = "Extension non autorisée : $fileName";
                    }
                } else {
                    $errors[] = "Erreur de téléchargement : $fileName";
                }
            }
        } else {
            $errors[] = "Veuillez sélectionner au moins une image.";
        }

        if (empty($errors)) {
            header("Location: location_list.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un bien à louer</title>
    <link rel="stylesheet" href="location&vente.css">
</head>
<body>
<div class="main-content">
    <header><h1>Ajouter un bien à louer</h1></header>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="location_create.php" method="POST" enctype="multipart/form-data">
        <label for="titre">Titre *</label>
        <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>

        <label for="description">Description</label>
        <textarea name="description" id="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

        <label for="type_bien">Type de bien *</label>
        <select name="type_bien" id="type_bien" required>
            <option value="">-- Sélectionnez --</option>
            <?php
            $types = ['appartement', 'villa', 'studio', 'chambre'];
            foreach ($types as $type) {
                $selected = ($_POST['type_bien'] ?? '') === $type ? 'selected' : '';
                echo "<option value=\"$type\" $selected>" . ucfirst($type) . "</option>";
            }
            ?>
        </select>

        <label for="adresse">Adresse *</label>
        <input type="text" name="adresse" id="adresse" value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>" required>

        <label for="ville">Ville *</label>
        <input type="text" name="ville" id="ville" value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>" required>

        <label for="prix">Prix *</label>
        <input type="number" step="0.01" name="prix" id="prix" value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>" required>

        <label for="mode_tarif">Mode tarif *</label>
        <select name="mode_tarif" id="mode_tarif" required>
            <option value="">-- Sélectionnez --</option>
            <option value="fixe" <?= ($_POST['mode_tarif'] ?? '') === 'fixe' ? 'selected' : '' ?>>Fixe</option>
            <option value="par_nuit" <?= ($_POST['mode_tarif'] ?? '') === 'par_nuit' ? 'selected' : '' ?>>Par nuit</option>
        </select>

        <label for="superficie">Superficie (m²) *</label>
        <input type="number" step="0.01" name="superficie" id="superficie" value="<?= htmlspecialchars($_POST['superficie'] ?? '') ?>" required>

        <label for="nb_pieces">Nombre de pièces *</label>
        <input type="number" name="nb_pieces" id="nb_pieces" value="<?= htmlspecialchars($_POST['nb_pieces'] ?? '') ?>" required>

        <label for="nb_lits">Nombre de lits *</label>
        <input type="number" name="nb_lits" id="nb_lits" value="<?= htmlspecialchars($_POST['nb_lits'] ?? '') ?>" required>

        <fieldset>
            <legend>Équipements</legend>
            <label><input type="checkbox" name="wifi" <?= isset($_POST['wifi']) ? 'checked' : '' ?>> Wifi</label>
            <label><input type="checkbox" name="climatisation" <?= isset($_POST['climatisation']) ? 'checked' : '' ?>> Climatisation</label>
            <label><input type="checkbox" name="cuisine" <?= isset($_POST['cuisine']) ? 'checked' : '' ?>> Cuisine</label>
            <label><input type="checkbox" name="parking" <?= isset($_POST['parking']) ? 'checked' : '' ?>> Parking</label>
        </fieldset>

        <label for="id_categorie">Catégorie *</label>
        <select name="id_categorie" id="id_categorie" required>
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id_categorie'] ?>" <?= ($_POST['id_categorie'] ?? '') == $cat['id_categorie'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="images">Images *</label>
        <input type="file" name="images[]" id="images" accept="image/*" multiple required>

        <button type="submit">Ajouter</button>
        <a href="location_list.php" class="btn-annuler">Annuler</a>
    </form>
</div>
</body>
</html>
