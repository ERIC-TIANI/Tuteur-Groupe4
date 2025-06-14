<?php
require_once 'config.php';

// Gestion des filtres & tri
$filters = [];
$params = [];
$orderBy = 'date_debut';
$orderDir = 'DESC';

if (!empty($_GET['filtre_bien'])) {
    $filters[] = 'b.titre LIKE ?';
    $params[] = '%' . $_GET['filtre_bien'] . '%';
}

if (!empty($_GET['filtre_client'])) {
    $filters[] = 'u.nom LIKE ?';
    $params[] = '%' . $_GET['filtre_client'] . '%';
}

if (!empty($_GET['filtre_statut']) && in_array($_GET['filtre_statut'], ['confirmée', 'annulée'])) {
    $filters[] = 'h.statut = ?';
    $params[] = $_GET['filtre_statut'];
}

if (!empty($_GET['tri_colonne']) && in_array($_GET['tri_colonne'], ['titre', 'nom_utilisateur', 'date_debut', 'date_fin', 'statut'])) {
    switch ($_GET['tri_colonne']) {
        case 'titre':
            $orderBy = 'b.titre';
            break;
        case 'nom_utilisateur':
            $orderBy = 'u.nom';
            break;
        case 'date_debut':
            $orderBy = 'h.date_debut';
            break;
        case 'date_fin':
            $orderBy = 'h.date_fin';
            break;
        case 'statut':
            $orderBy = 'h.statut';
            break;
        default:
            $orderBy = 'h.date_debut';
            break;
    }
}

if (!empty($_GET['tri_direction']) && in_array(strtoupper($_GET['tri_direction']), ['ASC', 'DESC'])) {
    $orderDir = strtoupper($_GET['tri_direction']);
}

// Construction de la requête
$sql = "SELECT 
            h.id_historique, 
            h.date_debut, 
            h.date_fin, 
            h.statut, 
            u.nom AS nom_utilisateur,
            b.titre AS titre_bien
        FROM HistoriqueReservation h
        JOIN Utilisateur u ON h.id_user = u.id_user
        JOIN BienImmobilier b ON h.id_property = b.id_property";

if ($filters) {
    $sql .= " WHERE " . implode(' AND ', $filters);
}

$sql .= " ORDER BY $orderBy $orderDir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Réservations</title>
    <link rel="stylesheet" href="reservation.css">
    <style>
        form.filter-form {
            margin-bottom: 20px;
        }
        form.filter-form input, form.filter-form select {
            margin-right: 10px;
            padding: 5px;
        }
        th a {
            text-decoration: none;
            color: inherit;
        }
        th a.asc::after {
            content: " ▲";
        }
        th a.desc::after {
            content: " ▼";
        }
    </style>
</head>
<body>
    <div class="main-content">
        <header>
            <h1>Historique des Réservations</h1>
        </header>

        <form method="get" class="filter-form">
            <input type="text" name="filtre_bien" placeholder="Filtrer par bien" value="<?= htmlspecialchars($_GET['filtre_bien'] ?? '') ?>">
            <input type="text" name="filtre_client" placeholder="Filtrer par client" value="<?= htmlspecialchars($_GET['filtre_client'] ?? '') ?>">
            <select name="filtre_statut">
                <option value="">Tous statuts</option>
                <option value="confirmée" <?= (($_GET['filtre_statut'] ?? '') === 'confirmée') ? 'selected' : '' ?>>Confirmée</option>
                <option value="annulée" <?= (($_GET['filtre_statut'] ?? '') === 'annulée') ? 'selected' : '' ?>>Annulée</option>
            </select>
            <button type="submit">Filtrer</button>
            <a href="historique_reservations.php" style="margin-left:10px;">Réinitialiser</a>
        </form>

        <table>
            <thead>
                <tr>
                    <?php
                    // Pour gérer le tri sur chaque colonne via les liens
                    function triLien($col, $label, $currentCol, $currentDir) {
                        $dir = 'ASC';
                        $class = '';
                        if ($col === $currentCol) {
                            if ($currentDir === 'ASC') {
                                $dir = 'DESC';
                                $class = 'asc';
                            } else {
                                $dir = 'ASC';
                                $class = 'desc';
                            }
                        }
                        $urlParams = $_GET;
                        $urlParams['tri_colonne'] = $col;
                        $urlParams['tri_direction'] = $dir;
                        $query = http_build_query($urlParams);
                        return "<a href=\"?{$query}\" class=\"$class\">$label</a>";
                    }

                    $currentCol = $_GET['tri_colonne'] ?? 'date_debut';
                    $currentDir = $_GET['tri_direction'] ?? 'DESC';
                    ?>
                    <th><?= triLien('titre', 'Bien', $currentCol, $currentDir) ?></th>
                    <th><?= triLien('nom_utilisateur', 'Client', $currentCol, $currentDir) ?></th>
                    <th><?= triLien('date_debut', 'Date Début', $currentCol, $currentDir) ?></th>
                    <th><?= triLien('date_fin', 'Date Fin', $currentCol, $currentDir) ?></th>
                    <th><?= triLien('statut', 'Statut', $currentCol, $currentDir) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($historique) > 0): ?>
                    <?php foreach ($historique as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['titre_bien']) ?></td>
                            <td><?= htmlspecialchars($h['nom_utilisateur']) ?></td>
                            <td><?= $h['date_debut'] ?></td>
                            <td><?= $h['date_fin'] ?></td>
                            <td><?= $h['statut'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">Aucune réservation historique trouvée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="reservation_list.php" style="margin-top: 20px; display: inline-block;">← Retour à la liste des réservations</a>
    </div>
</body>
</html>