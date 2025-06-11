<?php
require_once 'connexion.php';

// Vérifier si un ID est fourni dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirection si l'ID est manquant ou invalide
    header('Location: location_list.php');
    exit();
}

$id = (int)$_GET['id'];

try {
    // Commencer une transaction
    $pdo->beginTransaction();

    // Supprimer d'abord les images liées au bien
    $stmtImg = $pdo->prepare("DELETE FROM Image WHERE id_property = ?");
    $stmtImg->execute([$id]);

    // Supprimer ensuite le bien lui-même
    $stmt = $pdo->prepare("DELETE FROM BienImmobilier WHERE id_property = ?");
    $stmt->execute([$id]);

    // Valider la suppression
    $pdo->commit();

    // Rediriger vers la liste après suppression
    header('Location: location_list.php');
    exit();

} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    $pdo->rollBack();
    echo "Erreur lors de la suppression : " . $e->getMessage();
}
?>
