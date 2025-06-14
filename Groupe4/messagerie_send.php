<?php
session_start();
include 'connexion.php';

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expediteur = $_SESSION['user_id'];
    $destinataire = $_POST['destinataire'] ?? null;
    $contenu = trim($_POST['contenu']);

    if ($destinataire && !empty($contenu)) {
        $stmt = $pdo->prepare("INSERT INTO Message (contenu, date_envoi, statut, id_expediteur, id_destinataire) VALUES (?, NOW(), 'non lu', ?, ?)");
        $stmt->execute([$contenu, $expediteur, $destinataire]);

        header('Location: messagerie.php?success=1');
        exit;
    } else {
        header('Location: messagerie.php?error=1');
        exit;
    }
} else {
    header('Location: messagerie.php');
    exit;
}
