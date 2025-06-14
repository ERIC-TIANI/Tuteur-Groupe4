<?php
require_once 'connexion.php';

if (!isset($_GET['id'])) {
    header("Location: vente_list.php");
    exit();
}

$id_transaction = (int) $_GET['id'];

// Supprimer la transaction
$sql = "DELETE FROM TransactionVente WHERE id_transaction = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_transaction]);

// Redirection vers la liste des ventes
header("Location: vente_list.php");
exit();
?>
