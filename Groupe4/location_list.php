<?php
require_once 'connexion.php';

// Requête pour récupérer les biens à louer avec les noms de l'utilisateur et de la catégorie
$sql = "SELECT 
            b.id_property, 
            b.titre, 
            b.ville, 
            b.prix, 
            b.superficie, 
            b.nb_pieces, 
            b.date_ajout,
            u.nom AS nom_proprietaire,
            c.nom AS nom_categorie
        FROM BienImmobilier b
        LEFT JOIN Utilisateur u ON b.id_user = u.id_user
        LEFT JOIN Categorie c ON b.id_categorie = c.id_categorie
        WHERE b.type_annonce = 'location'
        ORDER BY b.date_ajout DESC";


$stmt = $pdo->prepare($sql);
$stmt->execute();
$biens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Biens à louer</title>
    <link rel="stylesheet" href="location&vente.css">
    <style>
        .miniature {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
    </div>

    <div class="main-content">
        <header>
            <h1>Biens immobiliers à louer</h1>
            <a href="location_create.php" class="btn-ajout">+ Ajouter un bien</a>
        </header>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>Ville</th>
                    <th>Prix</th>
                    <th>Superficie</th>
                    <th>Pièces</th>
                    <th>Propriétaire</th>
                    <th>Catégorie</th>
                    <th>Date d'ajout</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($biens as $bien): ?>
                    <tr>
                        <td>
                            <?php
                            $stmtImg = $pdo->prepare("SELECT url_image FROM Image WHERE id_property = ? LIMIT 1");
                            $stmtImg->execute([$bien['id_property']]);
                            $img = $stmtImg->fetch();
                            if ($img):
                            ?>
                                <img src="<?= htmlspecialchars($img['url_image']) ?>" alt="Miniature" class="miniature">
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($bien['titre']) ?></td>
                        <td><?= htmlspecialchars($bien['ville']) ?></td>
                        <td><?= number_format($bien['prix'], 2) ?> €</td>
                        <td><?= $bien['superficie'] ?> m²</td>
                        <td><?= $bien['nb_pieces'] ?></td>
                        <td><?= htmlspecialchars($bien['nom_proprietaire'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($bien['nom_categorie'] ?? '-') ?></td>
                        <td><?= $bien['date_ajout'] ?></td>
                        <td>
                            <a href="location_edit.php?id=<?= $bien['id_property'] ?>" class="btn-modif">Modifier</a>
                            <a href="location_delete.php?id=<?= $bien['id_property'] ?>" class="btn-suppr" onclick="return confirm('Supprimer ce bien ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($biens)): ?>
                    <tr><td colspan="10">Aucun bien à louer trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="admin_dashboard.php">Retour au tableau de bord</a>
</body>
</html>
