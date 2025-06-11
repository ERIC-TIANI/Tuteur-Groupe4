<?php
include 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? null;
    $telephone = $_POST['telephone'] ?? '';
    $pays = $_POST['pays'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $nationalite = $_POST['nationalite'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'client';
    $password = $_POST['mot_de_passe'] ?? '';

    if ($nom && $prenom && filter_var($email, FILTER_VALIDATE_EMAIL) && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO Utilisateur (nom, prenom, date_naissance, telephone, pays, ville, nationalite, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $date_naissance, $telephone, $pays, $ville, $nationalite, $email, $hash, $role]);
        header('Location: users_list.php');
        exit;
    } else {
        $error = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Ajouter un utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style_user.css" />
</head>
<body>
  <h1>Ajouter un utilisateur</h1>
  <?php if (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="POST" action="">
    <label>Nom : <input type="text" name="nom" required></label><br>
    <label>Prénom : <input type="text" name="prenom" required></label><br>
    <label>Date de naissance : <input type="date" name="date_naissance"></label><br>
    <label>Téléphone : <input type="text" name="telephone"></label><br>
    <label>Pays : <input type="text" name="pays"></label><br>
    <label>Ville : <input type="text" name="ville"></label><br>
    <label>Nationalité : <input type="text" name="nationalite"></label><br>
    <label>Email : <input type="email" name="email" required></label><br>
    <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br>
    <label>Rôle : 
      <select name="role">
        <option value="client">Client</option>
        <option value="proprietaire">Propriétaire</option>
        <option value="admin">Admin</option>
      </select>
    </label><br>
    <button type="submit">Créer</button>
  </form>
  <a href="users_list.php">Retour à la liste</a>
</body>
</html>
