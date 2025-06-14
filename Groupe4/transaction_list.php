<?php
require_once 'connexion.php';

// Récupération des filtres
$statut = $_GET['statut'] ?? '';
$date = $_GET['date'] ?? '';
$nom = $_GET['nom'] ?? '';

$conditions = [];
$params = [];

if ($statut !== '') {
    $conditions[] = "v.statut = ?";
    $params[] = $statut;
}

if ($date !== '') {
    $conditions[] = "v.date_transaction = ?";
    $params[] = $date;
}

if ($nom !== '') {
    $conditions[] = "(acheteur.nom LIKE ? OR proprietaire.nom LIKE ?)";
    $params[] = "%$nom%";
    $params[] = "%$nom%";
}

$where = count($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

$sql = "SELECT 
            v.id_transaction, v.prix_vente, v.date_transaction, v.statut,
            b.titre AS titre_bien,
            acheteur.nom AS nom_acheteur,
            proprietaire.nom AS nom_proprietaire
        FROM TransactionVente v
        JOIN BienImmobilier b ON v.id_property = b.id_property
        JOIN Utilisateur acheteur ON v.id_acheteur = acheteur.id_user
        JOIN Utilisateur proprietaire ON v.id_proprietaire = proprietaire.id_user
        $where
        ORDER BY v.date_transaction DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Ventes</title>
    <link rel="stylesheet" href="transaction.css">
</head>
<body>
<div class="main-content">
    <header>
        <h1>Liste des Transactions de Vente</h1>
    </header>

    <form method="get" class="filters">
        <label>Statut:
            <select name="statut">
                <option value="">Tous</option>
                <option value="en attente" <?= $statut === 'en attente' ? 'selected' : '' ?>>En attente</option>
                <option value="finalisée" <?= $statut === 'finalisée' ? 'selected' : '' ?>>Finalisée</option>
                <option value="annulée" <?= $statut === 'annulée' ? 'selected' : '' ?>>Annulée</option>
            </select>
        </label>
        <label>Date: <input type="date" name="date" value="<?= htmlspecialchars($date) ?>"></label>
        <label>Nom acheteur/propriétaire: <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>"></label>
        <button type="submit">Filtrer</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Bien</th>
                <th>Acheteur</th>
                <th>Propriétaire</th>
                <th>Prix</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ventes as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['titre_bien']) ?></td>
                <td><?= htmlspecialchars($v['nom_acheteur']) ?></td>
                <td><?= htmlspecialchars($v['nom_proprietaire']) ?></td>
                <td><?= number_format($v['prix_vente'], 2, ',', ' ') ?> €</td>
                <td><?= $v['date_transaction'] ?></td>
                <td><?= $v['statut'] ?></td>
                <td>
                    <?php if ($v['statut'] === 'en attente'): ?>
                        <a href="transaction_statut.php?id=<?= $v['id_transaction'] ?>&action=finaliser" class="btn-valide">Finaliser</a>
                        <a href="transaction_statut.php?id=<?= $v['id_transaction'] ?>&action=annuler" class="btn-refus">Annuler</a>
                    <?php endif; ?>
                    <a href="transaction_delete.php?id=<?= $v['id_transaction'] ?>" class="btn-suppr"    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ? Cette action est irréversible.');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($ventes)): ?>
            <tr><td colspan="7">Aucune vente trouvée.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php">&larr; Retour au tableau de bord</a>
</div>
</body>
</html>
