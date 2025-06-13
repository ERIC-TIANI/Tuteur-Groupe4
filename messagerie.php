<?php
include 'connexion.php';
session_start();

if (!isset($_SESSION['id_user'])) {
  header('Location: login.php');
  exit();
}

$id_user = $_SESSION['id_user'];

// Récupérer les messages reçus
$messagesRecus = $pdo->prepare("SELECT m.*, u.nom AS expediteur_nom FROM Message m JOIN Utilisateur u ON m.id_expediteur = u.id_user WHERE m.id_destinataire = ? ORDER BY date_envoi DESC");
$messagesRecus->execute([$id_user]);

// Récupérer les messages envoyés
$messagesEnvoyes = $pdo->prepare("SELECT m.*, u.nom AS destinataire_nom FROM Message m JOIN Utilisateur u ON m.id_destinataire = u.id_user WHERE m.id_expediteur = ? ORDER BY date_envoi DESC");
$messagesEnvoyes->execute([$id_user]);

// Récupérer tous les utilisateurs pour le formulaire de message
$utilisateurs = $pdo->prepare("SELECT id_user, nom FROM Utilisateur WHERE id_user != ?");
$utilisateurs->execute([$id_user]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Messagerie interne</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="main-content">
  <header>
    <h1>Messagerie interne</h1>
    <a href="admin_dashboard.php">Retour au tableau de bord</a>
  </header>

  <h2>Boîte de réception</h2>
  <table>
    <tr><th>Expéditeur</th><th>Message</th><th>Date</th><th>Statut</th></tr>
    <?php foreach ($messagesRecus as $msg): ?>
      <tr>
        <td><?= htmlspecialchars($msg['expediteur_nom']) ?></td>
        <td><?= nl2br(htmlspecialchars(substr($msg['contenu'], 0, 50))) ?>...</td>
        <td><?= $msg['date_envoi'] ?></td>
        <td><?= $msg['statut'] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h2>Messages envoyés</h2>
  <table>
    <tr><th>Destinataire</th><th>Message</th><th>Date</th></tr>
    <?php foreach ($messagesEnvoyes as $msg): ?>
      <tr>
        <td><?= htmlspecialchars($msg['destinataire_nom']) ?></td>
        <td><?= nl2br(htmlspecialchars(substr($msg['contenu'], 0, 50))) ?>...</td>
        <td><?= $msg['date_envoi'] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h2>Nouveau message</h2>
  <form action="messagerie_send.php" method="post">
    <label for="destinataire">Destinataire :</label>
    <select name="destinataire" id="destinataire" required>
      <?php foreach ($utilisateurs as $u): ?>
        <option value="<?= $u['id_user'] ?>"><?= htmlspecialchars($u['nom']) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="contenu">Message :</label><br>
    <textarea name="contenu" id="contenu" rows="5" cols="60" required></textarea><br>
    <button type="submit">Envoyer</button>
  </form>
</div>
</body>
</html>
