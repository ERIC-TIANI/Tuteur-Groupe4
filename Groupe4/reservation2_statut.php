<?php
require_once 'config.php';

if (!isset($_GET['id'], $_GET['action'])) {
    header('Location: reservation2_list.php');
    exit();
}

$id = (int)$_GET['id'];
$action = $_GET['action'];

$nouveauStatut = null;

if ($action === 'accepter') {
    $nouveauStatut = 'confirmée';
} elseif ($action === 'refuser') {
    $nouveauStatut = 'annulée';
}

if ($nouveauStatut) {
    $stmt = $pdo->prepare("UPDATE Reservation SET statut = ? WHERE id_reservation = ?");
    $stmt->execute([$nouveauStatut, $id]);
}

header('Location: reservation2_list.php');
exit();