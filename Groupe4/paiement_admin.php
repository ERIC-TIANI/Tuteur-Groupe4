<?php
require_once 'connexion.php';
session_start();

/*if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}*/

// Récupération des filtres
$mois = $_GET['mois'] ?? '';
$annee = $_GET['annee'] ?? '';

$conditions = ["p.statut = 'payé'"];
$params = [];

if ($mois !== '') {
    $conditions[] = "MONTH(p.date_paiement) = ?";
    $params[] = $mois;
}

if ($annee !== '') {
    $conditions[] = "YEAR(p.date_paiement) = ?";
    $params[] = $annee;
}

$where = 'WHERE ' . implode(' AND ', $conditions);

$sql = "SELECT p.*, 
               u.nom, 
               r.id_reservation, 
               t.id_transaction, 
               b.titre AS bien_concerne
        FROM Paiement p
        JOIN Utilisateur u ON p.id_user = u.id_user
        LEFT JOIN Reservation r ON p.id_reservation = r.id_reservation
        LEFT JOIN TransactionVente t ON p.id_transaction = t.id_transaction
        LEFT JOIN BienImmobilier b ON 
            (r.id_property = b.id_property OR t.id_property = b.id_property)
        $where
        ORDER BY p.date_paiement DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiements confirmés</title>
    <link rel="stylesheet" href="paiement.css">
</head>
<body>
<div class="main-content">
    <h1>Paiements confirmés</h1>
    <form method="get" class="filtre-paiement">
    <label>Mois :
        <select name="mois">
            <option value="">-- Tous --</option>
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= ($mois == $m) ? 'selected' : '' ?>>
                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                </option>
            <?php endfor; ?>
        </select>
    </label>

    <label>Année :
        <input type="number" name="annee" value="<?= htmlspecialchars($annee) ?>" min="2000" max="<?= date('Y') ?>">
    </label>

    <button type="submit">Filtrer</button>
</form>

    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Montant</th>
                <th>Mode</th>
                <th>Date</th>
                <th>Bien concerné</th>
                <th>Origine</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($paiements)): ?>
            <tr><td colspan="6">Aucun paiement trouvé.</td></tr>
        <?php else: ?>
            <?php foreach ($paiements as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nom']) ?></td>
                    <td><?= number_format($p['montant'], 2) ?> FCFA</td>
                    <td><?= htmlspecialchars($p['mode_paiement']) ?></td>
                    <td><?= $p['date_paiement'] ?></td>
                    <td><?= htmlspecialchars($p['bien_concerne'] ?? 'N/A') ?></td>
                    <td><?= $p['id_reservation'] ? 'Réservation' : 'Vente' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn-retour">← Retour au tableau de bord</a>
</div>
</body>
</html>
