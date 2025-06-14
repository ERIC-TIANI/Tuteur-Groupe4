<?php
include 'connexion.php';

// Récupérer tous les utilisateurs avec les nouveaux champs
$stmt = $pdo->query("SELECT id_user, nom, prenom, date_naissance, telephone, pays, ville, nationalite, email, role FROM Utilisateur ORDER BY id_user DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Gestion Utilisateurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style_user.css" />
</head>
<body>
  <h1>Liste des utilisateurs</h1>
  <a href="user_create.php">Ajouter un utilisateur</a>ss
  <table border="1" cellpadding="8" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th><th>Nom</th><th>Prénom</th><th>Date de naissance</th><th>Téléphone</th><th>Pays</th><th>Ville</th><th>Nationalité</th><th>Email</th><th>Rôle</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
      <tr>
        <td><?= htmlspecialchars($user['id_user']) ?></td>
        <td><?= htmlspecialchars($user['nom']) ?></td>
        <td><?= htmlspecialchars($user['prenom']) ?></td>
        <td><?= htmlspecialchars($user['date_naissance']) ?></td>
        <td><?= htmlspecialchars($user['telephone']) ?></td>
        <td><?= htmlspecialchars($user['pays']) ?></td>
        <td><?= htmlspecialchars($user['ville']) ?></td>
        <td><?= htmlspecialchars($user['nationalite']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= htmlspecialchars($user['role']) ?></td>
        <td>
          <a href="user_edit.php?id=<?= $user['id_user'] ?>">Modifier</a> |
          <a href="user_delete.php?id=<?= $user['id_user'] ?>" onclick="return confirm('Confirmer suppression ?')">Supprimer</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a href="admin_dashboard.php">Retour au tableau de bord</a>
</body>
</html>
