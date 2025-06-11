<?php
$conn = new mysqli("localhost", "root", "", "Immo_Web");
if ($conn->connect_error) die("Connexion échouée : " . $conn->connect_error);

$id_property = $_GET['id_property'];
$sql = "SELECT * FROM BienImmobilier WHERE id_property = $id_property";
$bien = $conn->query($sql)->fetch_assoc();

// Récupérer les utilisateurs (clients)
$clients = $conn->query("SELECT id_user, nom, prenom FROM Utilisateur WHERE role = 'client'");
$proprietaire = $conn->query("SELECT nom, prenom FROM Utilisateur WHERE id_user = {$bien['id_user']}")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Formulaire de vente</title>
</head>
<body>
  <h2>Formulaire de vente du bien : <?= htmlspecialchars($bien['titre']) ?></h2>
  <form action="generer_facture.php" method="POST">
    <input type="hidden" name="id_property" value="<?= $bien['id_property'] ?>">
    <input type="hidden" name="id_proprietaire" value="<?= $bien['id_user'] ?>">
    <input type="hidden" name="prix" value="<?= $bien['prix'] ?>">

    <label>Acheteur :</label>
    <select name="id_acheteur" required>
      <?php while ($client = $clients->fetch_assoc()): ?>
        <option value="<?= $client['id_user'] ?>">
          <?= htmlspecialchars($client['nom']) . ' ' . htmlspecialchars($client['prenom']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <br><br>
    <label>Date de vente :</label>
    <input type="date" name="date_transaction" required>

    <br><br>
    <input type="submit" value="Finaliser la vente et générer la facture">
  </form>
</body>
</html>