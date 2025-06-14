<?php
require_once 'config.php';

$sql = "SELECT 
            r.id_reservation, r.date_debut, r.date_fin, r.statut,
            u.nom AS nom_utilisateur,
            b.titre AS titre_bien
        FROM Reservation r
        JOIN Utilisateur u ON r.id_user = u.id_user
        JOIN BienImmobilier b ON r.id_property = b.id_property
        ORDER BY r.date_debut DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservations</title>
    <link rel="stylesheet" href="reservation2.css">
</head>
<body>
    <div class="main-content">
        <header>
            <h1>Liste des Réservations</h1>
        </header>

        <table>
            <thead>
                <tr>
                    <th>Bien</th>
                    <th>Client</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><?= htmlspecialchars($res['titre_bien']) ?></td>
                        <td><?= htmlspecialchars($res['nom_utilisateur']) ?></td>
                        <td><?= $res['date_debut'] ?></td>
                        <td><?= $res['date_fin'] ?></td>
                        <td><?= $res['statut'] ?></td>
                        <td>
                            <?php if ($res['statut'] === 'en attente'): ?>
                                <a href="reservation2_statut.php?id=<?= $res['id_reservation'] ?>&action=accepter" class="btn-valide">Accepter</a>
                                <a href="reservation2_statut.php?id=<?= $res['id_reservation'] ?>&action=refuser" class="btn-refus">Refuser</a>
                            <?php endif; ?>
                            <a href="reservation2_delete.php?id=<?= $res['id_reservation'] ?>" class="btn-suppr" onclick="return confirm('Supprimer cette réservation ?')">Supprimer</a>
                            <?php if (in_array($res['statut'], ['confirmée', 'annulée'])): ?>
                                <a href="archiver_reservations2.php?id=<?= $res['id_reservation'] ?>" class="btn-archive" onclick="return confirm('Archiver cette réservation ?')">Archiver maintenant</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($reservations)): ?>
                    <tr><td colspan="6">Aucune réservation trouvée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>


        <a class="hist"   href="historique_reservations2.php" >→ Historique des réservations</a>
        <a href="proprietaire.php">← Retour au tableau de bord</a>
    </div>
</body>
</html>
