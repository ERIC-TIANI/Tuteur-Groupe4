<?php
require_once 'connexion.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$id = $_GET['id'] ?? null;

if ($id) {
    // Vérifie que le paiement appartient à l'utilisateur et est annulable
    $stmt = $pdo->prepare("SELECT * FROM Paiement WHERE id_paiement = ? AND id_user = ?");
    $stmt->execute([$id, $id_user]);
    $paiement = $stmt->fetch();

    if ($paiement && in_array($paiement['statut'], ['en attente', 'échoué'])) {
        $update = $pdo->prepare("UPDATE Paiement SET statut = 'annulé' WHERE id_paiement = ?");
        $update->execute([$id]);
    }
}

header("Location: paiement_client.php");
exit();
