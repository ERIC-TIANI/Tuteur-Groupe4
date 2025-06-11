<?php
include 'connexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: users_list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE id_user = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur non trouvé");
}

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

    if ($nom && $prenom && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE Utilisateur SET nom=?, prenom=?, date_naissance=?, telephone=?, pays=?, ville=?, nationalite=?, email=?, role=?, mot_de_passe=? WHERE id_user=?");
            $stmt->execute([$nom, $prenom, $date_naissance, $telephone, $pays, $ville, $nationalite, $email, $role, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE Utilisateur SET nom=?, prenom=?, date_naissance=?, telephone=?, pays=?, ville=?, nationalite=?, email=?, role=? WHERE id_user=?");
            $stmt->execute([$nom, $prenom, $date_naissance, $telephone, $pays, $ville, $nationalite, $email, $role, $id]);
        }
        header('Location: users_list.php');
        exit;
    } else {
        $error = "Veuillez remplir correctement les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Modifier utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style_user.css" />
</head>
<body>
  <h1>Modifier utilisateur #<?= htmlspecialchars($id) ?></h1>
  <?php if (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="POST" action="">
    <label>Nom : <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required></label><br>
    <label>Prénom : <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required></label><br>
    <label>Date de naissance : <input type="date" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>"></label><br>
    <label>Téléphone : <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>"></label><br>
    <label>Pays : <input type="text" name="pays" value="<?= htmlspecialchars($user['pays']) ?>"></label><br>
    <label>Ville : <input type="text" name="ville" value="<?= htmlspecialchars($user['ville']) ?>"></label><br>
    <label>Nationalité : <input type="text" name="nationalite" value="<?= htmlspecialchars($user['nationalite']) ?>"></label><br>
    <label>Email : <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label><br>
    <label>Mot de passe (laisser vide pour ne pas changer) : <input type="password" name="mot_de_passe"></label><br>
    <label>Rôle : 
      <select name="role">
        <option value="client" <?= $user['role']=='client' ? 'selected' : '' ?>>Client</option>
        <option value="proprietaire" <?= $user['role']=='proprietaire' ? 'selected' : '' ?>>Propriétaire</option>
        <option value="admin" <?= $user['role']=='admin' ? 'selected' : '' ?>>Admin</option>
      </select>
    </label><br>
    <button type="submit">Modifier</button>
  </form>
  <a href="users_list.php">Retour à la liste</a>
</body>
</html>
