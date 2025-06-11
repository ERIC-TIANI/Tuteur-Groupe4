<?php
require_once 'connexion.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: vente_list.php");
    exit();
}

$id_transaction = (int) $_GET['id'];
$action = $_GET['action'];

if (!in_array($action, ['finaliser', 'annuler'])) {
    header("Location: transaction_list.php");
    exit();
}

$nouveau_statut = $action === 'finaliser' ? 'finalisée' : 'annulée';

$sql = "UPDATE TransactionVente SET statut = ? WHERE id_transaction = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nouveau_statut, $id_transaction]);

header("Location: vente_list.php");
exit();
?>
