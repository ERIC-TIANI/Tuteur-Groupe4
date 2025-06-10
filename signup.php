<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO clients (nom, email, password) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$nom, $email, $password]);
        echo "Inscription réussie. <a href='login.html'>Se connecter</a>";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>