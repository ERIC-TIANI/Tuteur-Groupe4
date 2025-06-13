<?php
session_start();
require 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];
    $id_property = $_POST['id_property'];
    $note = $_POST['note'];
    $commentaire = $_POST['commentaire'];
    $date = date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO Avis (note, commentaire, date_review, id_user, id_property) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$note, $commentaire, $date, $id_user, $id_property]);
    header("Location: details_bien.php?id=$id_property");
    exit;
}
?>
