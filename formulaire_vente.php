<?php
$conn = new mysqli("localhost", "root", "", "Immo_Web");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$id_property = isset($_GET['id_property']) ? (int)$_GET['id_property'] : 0;

$bien = $conn->query("SELECT * FROM BienImmobilier WHERE id_property = $id_property")->fetch_assoc();
$proprietaire = $conn->query("SELECT * FROM Utilisateur WHERE id_user = {$bien['id_user']}")->fetch_assoc();
$clients = $conn->query("SELECT * FROM Utilisateur WHERE role = 'client'");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire de vente</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 2rem; }
        form { background: white; padding: 2rem; border-radius: 8px; max-width: 600px; margin: auto; }
        h2 { text-align: center; }
        label { display: block; margin-top: 1rem; }
        input, select, textarea { width: 100%; padding: 0.5rem; }
        button { margin-top: 2rem; background: #27ae60; color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #219150; }
    </style>
</head>
<body>
    <form action="traiter_vente.php" method="POST">
        <h2>Vente du bien : <?= htmlspecialchars($bien['titre']) ?></h2>

        <input type="hidden" name="id_property" value="<?= $bien['id_property'] ?>">
        <input type="hidden" name="id_proprietaire" value="<?= $bien['id_user'] ?>">

        <label for="id_acheteur">Acheteur :</label>
        <select name="id_acheteur" required>
            <option value="">-- Sélectionner un client --</option>
            <?php while($client = $clients->fetch_assoc()): ?>
                <option value="<?= $client['id_user'] ?>">
                    <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?> - <?= htmlspecialchars($client['email']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="prix">Prix du bien :</label>
        <input type="text" value="<?= number_format($bien['prix'], 0, ',', ' ') ?> FCFA" disabled>

        <button type="submit">Finaliser la vente et générer la facture</button>
    </form>
</body>
</html>