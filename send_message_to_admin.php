<?php
session_start();
require 'connexion.php';

if (!isset($_SESSION['id_user'])) {
    die("Vous devez être connecté pour envoyer un message.");
}

// Récupérer l'ID de l'expéditeur (utilisateur connecté)
$id_expediteur = $_SESSION['id_user'];

// Trouver l'id de l'admin via son email
$email_admin = 'tianieric3@gmail.com';
$stmt = $pdo->prepare("SELECT id_user FROM Utilisateur WHERE email = ? AND role = 'admin'");
$stmt->execute([$email_admin]);
$id_admin = $stmt->fetchColumn();

if (!$id_admin) {
    die("Administrateur introuvable.");
}

$erreur = '';
$succes = '';

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu'] ?? '');

    if (empty($contenu)) {
        $erreur = "Le message ne peut pas être vide.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO Message (contenu, date_envoi, statut, id_expediteur, id_destinataire) VALUES (?, NOW(), 'non lu', ?, ?)");
        $ok = $stmt->execute([$contenu, $id_expediteur, $id_admin]);

        if ($ok) {
            $succes = "Message envoyé avec succès à l'administrateur.";
        } else {
            $erreur = "Erreur lors de l'envoi du message.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Envoyer un message à l'administrateur</title>
    <style>
      textarea { width: 100%; height: 120px; }
      .error { color: red; }
      .success { color: green; }
    </style>
</head>
<body>
    <h2>Envoyer un message à l'administrateur</h2>

    <?php if ($erreur): ?>
        <p class="error"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <?php if ($succes): ?>
        <p class="success"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="contenu">Votre message :</label><br>
        <textarea id="contenu" name="contenu" required></textarea><br><br>

        <button type="submit">Envoyer</button>
    </form>
</body>
</html>
