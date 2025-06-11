<?php
require_once 'connexion.php';

// Récupération des filtres
$tri = $_GET['tri'] ?? 'date_review';
$nom = $_GET['nom'] ?? '';
$date = $_GET['date'] ?? '';

// Tri SQL
$orderBy = match ($tri) {
    'note' => 'a.note DESC',
    'utilisateur' => 'u.nom ASC',
    default => 'a.date_review DESC'
};

// Construction dynamique du WHERE
$where = "1=1";
$params = [];

if (!empty($nom)) {
    $where .= " AND u.nom LIKE ?";
    $params[] = '%' . $nom . '%';
}

if (!empty($date)) {
    $where .= " AND a.date_review = ?";
    $params[] = $date;
}

$sql = "SELECT 
            a.id_review, a.note, a.commentaire, a.date_review,
            u.nom AS nom_utilisateur,
            b.titre AS titre_bien
        FROM Avis a
        JOIN Utilisateur u ON a.id_user = u.id_user
        JOIN BienImmobilier b ON a.id_property = b.id_property
        WHERE $where
        ORDER BY $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$avis = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['export'])) {
    if ($_GET['export'] === 'csv') {
        // En-têtes CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=avis_export.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Bien', 'Utilisateur', 'Note', 'Commentaire', 'Date']);

        foreach ($avis as $a) {
            fputcsv($output, [
                $a['titre_bien'],
                $a['nom_utilisateur'],
                $a['note'],
                $a['commentaire'],
                $a['date_review']
            ]);
        }

        fclose($output);
        exit();
    }

    if ($_GET['export'] === 'pdf') {
        require_once('fpdf/fpdf.php'); // Assure-toi que FPDF est installé dans ton projet

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Export des Avis', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 10);

        foreach ($avis as $a) {
            $pdf->MultiCell(0, 8, 
                "Bien : " . $a['titre_bien'] . "\n" .
                "Utilisateur : " . $a['nom_utilisateur'] . "\n" .
                "Note : " . $a['note'] . "/5\n" .
                "Commentaire : " . $a['commentaire'] . "\n" .
                "Date : " . $a['date_review'] . "\n" .
                str_repeat('-', 80), 0, 'L');
        }

        $date = date('Y-m-d_H-i-s');
        $pdf->Output('D', "avis_list_{$date}.pdf");

        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Avis</title>
    <link rel="stylesheet" href="avis.css">
</head>
<body>
<div class="main-content">
    <header>
        <h1>Liste des Avis</h1>
    </header>

<div class="filters">
    <form method="get">
        <label for="tri">Trier par :</label>
        <select name="tri">
            <option value="date_review" <?= $tri === 'date_review' ? 'selected' : '' ?>>Date</option>
            <option value="note" <?= $tri === 'note' ? 'selected' : '' ?>>Note</option>
            <option value="utilisateur" <?= $tri === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
        </select>

        <label for="nom">Nom utilisateur :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" placeholder="Nom client">

        <label for="date">Date :</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">

        <button type="submit">Filtrer</button>
        <button type="submit" name="export" value="pdf">Exporter en PDF</button>
        <button type="submit" name="export" value="csv">Exporter en Excel</button>

    </form>
</div>


    <table>
        <thead>
            <tr>
                <th>Bien</th>
                <th>Utilisateur</th>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($avis as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['titre_bien']) ?></td>
                <td><?= htmlspecialchars($a['nom_utilisateur']) ?></td>
                <td><?= $a['note'] ?>/5</td>
                <td><?= nl2br(htmlspecialchars($a['commentaire'])) ?></td>
                <td><?= $a['date_review'] ?></td>
                <td>
                    <a href="avis_delete.php?id=<?= $a['id_review'] ?>" class="btn-suppr" onclick="return confirm('Supprimer cet avis ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($avis)): ?>
            <tr><td colspan="6">Aucun avis trouvé.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn-retour">← Retour au tableau de bord</a>
</div>
</body>
</html>
