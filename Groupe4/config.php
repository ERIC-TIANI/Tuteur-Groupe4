<?php
$host = 'localhost';
$db = 'Immo_Web';
$user = 'root';
$pass = ''; // selon ton WAMP/MAMP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>