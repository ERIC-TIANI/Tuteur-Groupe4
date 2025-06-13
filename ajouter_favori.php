<?php
session_start();
require 'connexion.php';

if (!isset($_SESSION['id_user'])) {
    http_response_code(403);
    exit("Non autorisé");
}

$id_user = $_SESSION['id_user'];
$id_property = $_POST['id_property'] ?? null;
$action = $_POST['action'] ?? '';

if (!$id_property || !in_array($action, ['add', 'remove'])) {
    http_response_code(400);
    exit("Paramètres manquants");
}

if ($action === 'add') {
    // On évite les doublons
    $check = $pdo->prepare("SELECT * FROM Favori WHERE id_user = ? AND id_property = ?");
    $check->execute([$id_user, $id_property]);
    if (!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO Favori (id_user, id_property, date_ajout) VALUES (?, ?, CURDATE())");
        $stmt->execute([$id_user, $id_property]);
    }
    echo "Ajouté";
} else {
    $stmt = $pdo->prepare("DELETE FROM Favori WHERE id_user = ? AND id_property = ?");
    $stmt->execute([$id_user, $id_property]);
    echo "Supprimé";
}
