<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription Admin</title>
  <link rel="stylesheet" href="auth.css">
</head>
<body>
  <div class="auth-container">
    <form action="admin_register_traitement.php" method="POST">
      <h2>Créer un compte administrateur</h2>

      <input type="text" name="nom" placeholder="Nom" required>
      <input type="text" name="prenom" placeholder="Prénom" required>
      <input type="date" name="date_naissance" required>
      <input type="text" name="telephone" placeholder="Téléphone" required>
      <input type="text" name="pays" placeholder="Pays" required>
      <input type="text" name="ville" placeholder="Ville" required>
      <input type="text" name="nationalite" placeholder="Nationalité" required>
      <input type="email" name="email" placeholder="Adresse email" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>

      <button type="submit">Créer le compte admin</button>
    </form>
  </div>
</body>
</html>
