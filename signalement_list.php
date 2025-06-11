<?php
require_once 'connexion.php';

// Récupération des filtres
$statut = $_GET['statut'] ?? '';
$date = $_GET['date'] ?? '';
$nom = $_GET['nom'] ?? '';

$conditions = [];
$params = [];

if ($statut !== '') {
    $conditions[] = "s.statut = ?";
    $params[] = $statut;
}

if ($date !== '') {
    $conditions[] = "s.date_signalement = ?";
    $params[] = $date;
}

if ($nom !== '') {
    $conditions[] = "u.nom LIKE ?";
    $params[] = "%$nom%";
}

$where = count($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

$sql = "SELECT 
            s.id_signalement, s.type_probleme, s.description, s.date_signalement, s.statut,
            u.nom AS nom_utilisateur,
            b.titre AS titre_bien
        FROM Signalement s
        JOIN Utilisateur u ON s.id_user = u.id_user
        JOIN BienImmobilier b ON s.id_property = b.id_property
        $where
        ORDER BY s.date_signalement DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$signalements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Signalements</title>
    <link rel="stylesheet" href="signalement.css">
</head>
<body>
<div class="main-content">
    <header>
        <h1>Liste des Signalements</h1>
    </header>

    <form method="get" class="filters">
        <label>Statut:
            <select name="statut">
                <option value="">Tous</option>
                <option value="en attente" <?= $statut === 'en attente' ? 'selected' : '' ?>>En attente</option>
                <option value="traité" <?= $statut === 'traité' ? 'selected' : '' ?>>Traité</option>
                <option value="rejeté" <?= $statut === 'rejeté' ? 'selected' : '' ?>>Rejeté</option>
            </select>
        </label>
        <label>Date: <input type="date" name="date" value="<?= htmlspecialchars($date) ?>"></label>
        <label>Nom utilisateur: <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>"></label>
        <button type="submit">Filtrer</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Bien</th>
                <th>Utilisateur</th>
                <th>Type de problème</th>
                <th>Description</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($signalements as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['titre_bien']) ?></td>
                    <td><?= htmlspecialchars($s['nom_utilisateur']) ?></td>
                    <td><?= htmlspecialchars($s['type_probleme']) ?></td>
                    <td><?= nl2br(htmlspecialchars($s['description'])) ?></td>
                    <td><?= $s['date_signalement'] ?></td>
                    <td><?= $s['statut'] ?></td>
                    <td>
                        <?php if ($s['statut'] === 'en attente'): ?>
                            <a href="signalement_statut.php?id=<?= $s['id_signalement'] ?>&action=traiter" class="btn-valide">Traiter</a>
                            <a href="signalement_statut.php?id=<?= $s['id_signalement'] ?>&action=rejeter" class="btn-refus">Rejeter</a>
                        <?php endif; ?>
                        <a href="signalement_delete.php?id=<?= $s['id_signalement'] ?>" class="btn-suppr" onclick="return confirm('Supprimer ce signalement ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($signalements)): ?>
                <tr><td colspan="7">Aucun signalement trouvé.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="admin_dashboard.php">&larr; Retour au tableau de bord</a>
</div>
</body>
</html>
