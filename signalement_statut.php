<?php
require_once 'connexion.php';

// Vérification des paramètres dans l'URL
if (!isset($_GET['id'], $_GET['action'])) {
    header('Location: signalement_list.php');
    exit();
}

$id = (int)$_GET['id'];
$action = $_GET['action'];

// Déterminer le nouveau statut en fonction de l'action
$nouveauStatut = match ($action) {
    'traiter' => 'traité',
    'rejeter' => 'rejeté',
    default => null
};

if ($nouveauStatut) {
    try {
        $stmt = $pdo->prepare("UPDATE Signalement SET statut = ? WHERE id_signalement = ?");
        $stmt->execute([$nouveauStatut, $id]);
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour : " . $e->getMessage();
        exit();
    }
}

header('Location: signalement_list.php');
exit();
?>
