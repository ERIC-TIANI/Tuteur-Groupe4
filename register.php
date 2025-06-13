<?php
session_start();
include 'connexion.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $telephone = $_POST['telephone'];
    $pays = $_POST['pays'];
    $ville = $_POST['ville'];
    $nationalite = $_POST['nationalite'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Utilisateur WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetchColumn() > 0) {
        $message = "Cet email est déjà utilisé.";
    } else {
        $sql = "INSERT INTO Utilisateur (nom, prenom, date_naissance, telephone, pays, ville, nationalite, email, mot_de_passe, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $date_naissance, $telephone, $pays, $ville, $nationalite, $email, $mot_de_passe, $role]);
        $message = "Inscription réussie. Vous pouvez maintenant vous connecter.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
     <div class="wrapper">
    <form method="post" action="">
        <h2>Inscription</h2>

        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="date" name="date_naissance" required>
        <input type="text" name="telephone" placeholder="Téléphone" required>
        <input type="text" name="pays" placeholder="Pays" required>
        <input type="text" name="ville" placeholder="Ville" required>
        <input type="text" name="nationalite" placeholder="Nationalité" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>

        <select name="role" required>
            <option value="" disabled selected>Choisissez votre rôle</option>
            <option value="client">Client</option>
            <option value="proprietaire">Propriétaire</option>
        </select>

        <button type="submit">S'inscrire</button>

        <p><?= $message ?></p>
        <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
    </form>
    </div>
</body>
</html>
