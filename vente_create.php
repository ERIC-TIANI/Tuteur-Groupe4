<?php
require_once 'connexion.php';

// Récupération des catégories pour le menu déroulant
$stmtCat = $pdo->query("SELECT id_categorie, nom FROM Categorie ORDER BY nom");
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
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

    // Validation basique
    if (empty($titre)) $errors[] = "Le titre est obligatoire.";
    if (empty($ville)) $errors[] = "La ville est obligatoire.";
    if (empty($prix)) $errors[] = "Le prix est obligatoire.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO BienImmobilier 
            (titre, description, type_bien, adresse, ville, prix, mode_tarif, superficie, nb_pieces, nb_lits,
            wifi, climatisation, cuisine, parking, id_categorie, type_annonce)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'vente')");

        $stmt->execute([
            $titre, $description, $type_bien, $adresse, $ville, $prix, $mode_tarif,
            $superficie, $nb_pieces, $nb_lits,
            $wifi, $climatisation, $cuisine, $parking, $id_categorie
        ]);

        $id_property = $pdo->lastInsertId();

        // Enregistrement des images
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = 'images/';
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $imageName = basename($_FILES['images']['name'][$key]);
                $targetPath = $uploadDir . time() . '_' . $imageName;
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $stmtImg = $pdo->prepare("INSERT INTO Image (url_image, id_property) VALUES (?, ?)");
                    $stmtImg->execute([$targetPath, $id_property]);
                }
            }
        }

        header("Location: vente_list.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un bien à vendre</title>
    <link rel="stylesheet" href="location&vente.css">
</head>
<body>
<div class="main-content">
    <h1>Ajouter un bien à vendre</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Titre : <input type="text" name="titre" required></label><br>
        <label>Description : <textarea name="description"></textarea></label><br>
        <label>Type de bien : 
            <select name="type_bien">
                <option value="maison">Maison</option>
                <option value="appartement">Appartement</option>
                <option value="studio">Studio</option>
            </select>
        </label><br>
        <label>Adresse : <input type="text" name="adresse"></label><br>
        <label>Ville : <input type="text" name="ville" required></label><br>
        <label>Prix (€) : <input type="number" name="prix" required></label><br>
        <label>Mode tarif : 
            <select name="mode_tarif">
                <option value="mois">Par mois</option>
                <option value="vente">Achat</option>
            </select>
        </label><br>
        <label>Superficie (m²) : <input type="number" name="superficie"></label><br>
        <label>Nombre de pièces : <input type="number" name="nb_pieces"></label><br>
        <label>Nombre de lits : <input type="number" name="nb_lits"></label><br>

        <label><input type="checkbox" name="wifi"> Wifi</label>
        <label><input type="checkbox" name="climatisation"> Climatisation</label>
        <label><input type="checkbox" name="cuisine"> Cuisine</label>
        <label><input type="checkbox" name="parking"> Parking</label><br>

        <label>Catégorie :
            <select name="id_categorie">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>Images : <input type="file" name="images[]" multiple accept="image/*"></label><br><br>

        <button type="submit">Ajouter</button>
        <a href="vente_list.php">Annuler</a>
    </form>
</div>
</body>
</html>
