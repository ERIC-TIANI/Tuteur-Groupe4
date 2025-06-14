<?php
require_once 'connexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: reservation_list.php');
    exit();
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("DELETE FROM Reservation WHERE id_reservation = ?");
$stmt->execute([$id]);

header('Location: reservation_list.php');
exit();
