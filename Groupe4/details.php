<?php include 'config.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM BienImmobilier WHERE id_property = ?");
$stmt->execute([$id]);
$bien = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="details.css">

    <title>Détails du bien</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1><?= $bien['titre'] ?></h1>
    <p><strong>Description :</strong> <?= $bien['description'] ?></p>
    <p><strong>Ville :</strong> <?= $bien['ville'] ?></p>
    <p><strong>Prix :</strong> <?= $bien['prix'] ?> fcfa</p>
    <a href="acheter.php?id=<?= $bien['id_property'] ?>">Acheter ce bien</a>
    <a href="vente.php">← Retour</a>
</body>
</html>