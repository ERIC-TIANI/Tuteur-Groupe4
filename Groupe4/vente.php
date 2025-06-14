<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Biens immobiliers à vendre</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
        }
.header {
    background: #003b95;
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
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
        .nav a {
    margin: 0 10px;
    color: white;
    text-decoration: none;
}
        .btn {
    background: white;
    color: #003b95;
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
}
    </style>
</head>
<body>
    <header class="header">
    <div class="logo">Immobilier.com</div>
    <nav class="nav">
        <a href="location_search.php">location</a>
        <a href="IA.php">IA</a>
    </nav>
    <div class="actions">

        <!-- Icône message vers admin -->
        <a href="send_message_to_admin.php" title="Envoyer un message à l'administrateur" style="margin-right: 15px; font-size: 1.5rem; color: #333;">
            <i class="fa-solid fa-envelope"></i>
        </a>

        <a href="logout.php" class="btn">Se déconnecter</a>
    </div>
    </header>
    <h1><em>Regardez nos biens à vendre</em></h1>
    <div class="container">
        <?php
        $stmt = $pdo->query("SELECT * FROM BienImmobilier WHERE type_annonce = 'vente'");
        while ($row = $stmt->fetch()) {
            $id_property = $row['id_property'];

            // Récupérer la première image liée à ce bien
            $imgStmt = $pdo->prepare("SELECT url_image FROM Image WHERE id_property = ? LIMIT 1");
            $imgStmt->execute([$id_property]);
            $img = $imgStmt->fetch();

            $imagePath = $img ? "" . $img['url_image'] : "images/default.jpg"; // image par défaut si aucune image

            echo "<div class='card'>
                <img src='$imagePath' alt='Image du bien'>
                <h2>{$row['titre']}</h2>
                <p>{$row['ville']} - {$row['prix']} fcfa</p>
                <a href='details.php?id={$row['id_property']}'>Voir plus de détails</a><br>
                <a href='acheter.php?id={$row['id_property']}'>Acheter ce bien immobilier</a>
                <a href='IA.php' class='btn-blue'>Voir l'IA pour plus d'édification</a>
            </div>";
        }
        ?>
    </div>
</body>
</html> 