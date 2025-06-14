<?php
// Paramètres de connexion
$host = 'localhost';      // ou 127.0.0.1
$dbname = 'immo_web';  // nom de ta base de données
$user = 'root';           // nom d'utilisateur MySQL
$pass = '';               // mot de passe (laisse vide si aucun sur XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Activation des erreurs PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
