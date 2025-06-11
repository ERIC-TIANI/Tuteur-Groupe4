<?php
require_once 'connexion.php';

if (!isset($_GET['id'], $_GET['action'])) {
    header('Location: reservation_list.php');
    exit();
}

$id = (int)$_GET['id'];
$action = $_GET['action'];

$nouveauStatut = match ($action) {
    'accepter' => 'confirmée',
    'refuser' => 'annulée',
    default => null
};

if ($nouveauStatut) {
    $stmt = $pdo->prepare("UPDATE Reservation SET statut = ? WHERE id_reservation = ?");
    $stmt->execute([$nouveauStatut, $id]);
}

header('Location: reservation_list.php');
exit();
