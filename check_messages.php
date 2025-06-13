<?php
session_start();
require 'connexion.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé']);
    exit;
}

$id_admin = $_SESSION['id_user'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM Message WHERE id_destinataire = ? AND statut = 'non lu'");
$stmt->execute([$id_admin]);
$nbMessagesNonLus = $stmt->fetchColumn();

echo json_encode(['count' => (int)$nbMessagesNonLus]);
