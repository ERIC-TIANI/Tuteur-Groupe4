<?php
require_once 'connexion.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Récupérer les paiements de l'utilisateur
$sql = "SELECT p.*, 
               r.id_reservation, t.id_transaction, 
               b.titre AS bien_concerne
        FROM Paiement p
        LEFT JOIN Reservation r ON p.id_reservation = r.id_reservation
        LEFT JOIN TransactionVente t ON p.id_transaction = t.id_transaction
        LEFT JOIN BienImmobilier b ON 
            (r.id_property = b.id_property OR t.id_property = b.id_property)
        WHERE p.id_user = ?
        ORDER BY p.date_paiement DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Paiements</title>
    <link rel="stylesheet" href="paiement.css">
</head>
<body>
<div class="main-content">
    <h1>Mes Paiements</h1>

    <table>
        <thead>
            <tr>
                <th>Montant</th>
                <th>Mode</th>
                <th>Date</th>
                <th>Bien concerné</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($paiements)): ?>
            <tr><td colspan="6">Aucun paiement trouvé.</td></tr>
        <?php else: ?>
            <?php foreach ($paiements as $p): ?>
                <tr>
                    <td><?= number_format($p['montant'], 2) ?> FCFA</td>
                    <td><?= htmlspecialchars($p['mode_paiement']) ?></td>
                    <td><?= $p['date_paiement'] ?></td>
                    <td><?= htmlspecialchars($p['bien_concerne'] ?? 'N/A') ?></td>
                    <td><?= ucfirst($p['statut']) ?></td>
                    <td>
                        <?php if ($p['statut'] === 'en attente' || $p['statut'] === 'échoué'): ?>
                            <a href="paiement_annuler.php?id=<?= $p['id_paiement'] ?>" class="btn-annuler"
                               onclick="return confirm('Annuler ce paiement ?');">Annuler</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="client_dashboard.php" class="btn-retour">← Retour</a>
</div>
</body>
</html>
