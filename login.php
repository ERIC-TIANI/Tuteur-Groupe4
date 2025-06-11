<?php
session_start();
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        // Traitement connexion
        $email = $_POST['email_connexion'];
        $password = $_POST['mot_de_passe_connexion'];

        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['role'] = $user['role'];
            switch ($user['role']) {
                case 'client':
                    header("Location: client.php"); exit;
                case 'proprietaire':
                    header("Location: proprietaire.php"); exit;
                case 'admin':
                    header("Location: admin.php"); exit;
            }
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    }

    if (isset($_POST['register'])) {
        // Traitement inscription
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $password = $_POST['mot_de_passe'];
        $confirm = $_POST['confirmer'];
        $role = $_POST['role'];

        if ($password !== $confirm) {
            $message = "Les mots de passe ne correspondent pas.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("SELECT id_user FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "Un compte avec cet email existe déjà.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $prenom, $email, $hash, $role]);
                $message = "Inscription réussie ! Vous pouvez vous connecter.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Authentification</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background: #f9f9f9; }
        .container { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { text-align: center; }
        form { display: none; }
        input, select, button { width: 100%; padding: 10px; margin: 8px 0; }
        .message { color: red; text-align: center; }
        #form-connexion { display: block; }
        .toggle-link { text-align: center; margin-top: 10px; cursor: pointer; color: blue; text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <div class="message"><?= $message ?></div>

    <!-- Connexion -->
    <form id="form-connexion" method="POST">
        <h2>Connexion</h2>
        <input type="email" name="email_connexion" placeholder="Email" required>
        <input type="password" name="mot_de_passe_connexion" placeholder="Mot de passe" required>
        <button type="submit" name="login">Se connecter</button>
        <div class="toggle-link" onclick="toggleForms()">Pas encore de compte ? S'inscrire ici</div>
    </form>

    <!-- Inscription -->
    <form id="form-inscription" method="POST">
        <h2>Inscription</h2>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="telephone" placeholder="Téléphone">
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <input type="password" name="confirmer" placeholder="Confirmer mot de passe" required>
        <select name="role" required>
            <option value="client">Client</option>
            <option value="proprietaire">Propriétaire</option>
        </select>
        <button type="submit" name="register">S'inscrire</button>
        <div class="toggle-link" onclick="toggleForms()">Déjà inscrit ? Se connecter ici</div>
    </form>
</div>

<script>
    function toggleForms() {
        let loginForm = document.getElementById("form-connexion");
        let registerForm = document.getElementById("form-inscription");

        if (loginForm.style.display === "none") {
            loginForm.style.display = "block";
            registerForm.style.display = "none";
        } else {
            loginForm.style.display = "none";
            registerForm.style.display = "block";
        }
    }
</script>

</body>
</html>
