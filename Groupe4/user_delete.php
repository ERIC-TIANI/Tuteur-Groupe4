<?php
include 'connexion.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM Utilisateur WHERE id_user = ?");
    $stmt->execute([$id]);
}

header('Location: users_list.php');
exit;
