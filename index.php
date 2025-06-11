<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Biens immobiliers à vendre</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
        }
        .card {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 15px;
            width: 300px;
            text-align: center;
        }
        .card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Regardez nos biens à vendre</h1>
    <div class="container">
        <?php
        $stmt = $pdo->query("SELECT * FROM BienImmobilier WHERE type_annonce = 'vente'");
        while ($row = $stmt->fetch()) {
            $id_property = $row['id_property'];

            // Récupérer la première image liée à ce bien
            $imgStmt = $pdo->prepare("SELECT url_image FROM Image WHERE id_property = ? LIMIT 1");
            $imgStmt->execute([$id_property]);
            $img = $imgStmt->fetch();

            $imagePath = $img ? "images/" . $img['url_image'] : "images/default.jpg"; // image par défaut si aucune image

            echo "<div class='card'>
                <img src='$imagePath' alt='Image du bien'>
                <h2>{$row['titre']}</h2>
                <p>{$row['ville']} - {$row['prix']} fcfa</p>
                <a href='details.php?id={$row['id_property']}'>Voir plus de détails</a><br>
                <a href='acheter.php?id={$row['id_property']}'>Acheter ce bien immobilier</a>
            </div>";
        }
        ?>
    </div>
</body>
</html>