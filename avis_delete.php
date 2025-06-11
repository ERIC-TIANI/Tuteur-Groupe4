<?php
require_once 'connexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: avis_list.php');
    exit();
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM Avis WHERE id_review = ?");
$stmt->execute([$id]);

header('Location: avis_list.php');
exit();
