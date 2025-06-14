<?php
include 'connexion.php'; // fichier contenant ta connexion PDO

$req = $pdo->query("SELECT * FROM utilisateur");
$utilisateurs = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Liste des utilisateurs</h2>
<table border="1">
  <thead>
    <tr>
      <th>Nom</th>
      <th>Prénom</th>
      <th>Email</th>
      <th>Rôle</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($utilisateurs as $u): ?>
      <tr>
        <td><?= htmlspecialchars($u['nom']) ?></td>
        <td><?= htmlspecialchars($u['prénom']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['rôle']) ?></td>
        <td>
          <a href="modifier_user.php?id=<?= $u['id_user'] ?>">Modifier</a> |
          <a href="supprimer_user.php?id=<?= $u['id_user'] ?>" onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
