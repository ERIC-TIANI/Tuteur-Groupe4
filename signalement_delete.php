<?php
require_once 'connexion.php';

// Vérifier que l'identifiant est bien passé dans l'URL
if (!isset($_GET['id'])) {
    header('Location: signalement_list.php');
    exit();
}

$id = (int)$_GET['id'];

try {
    // Supprimer le signalement
    $stmt = $pdo->prepare("DELETE FROM Signalement WHERE id_signalement = ?");
    $stmt->execute([$id]);
} catch (PDOException $e) {
    echo "Erreur lors de la suppression : " . $e->getMessage();
    exit();
}

// Redirection après suppression
header('Location: signalement_list.php');
exit();
?>
