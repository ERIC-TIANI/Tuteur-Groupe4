<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'proprietaire') {
    header("Location: auth.php");
    exit;
}

require 'config.php';
$id_proprietaire = $_SESSION['id_user'];

// Traitement des actions (accepter/refuser)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reservation'], $_POST['action'])) {
    $id_res = $_POST['id_reservation'];
    $action = $_POST['action'];

    if (in_array($action, ['acceptee', 'refusee'])) {
        $update = $pdo->prepare("UPDATE reservation SET statut = ? WHERE id_reservation = ?");
        $update->execute([$action, $id_res]);

        // Envoi d'email
        $info = $pdo->prepare("SELECT u.email, b.titre FROM reservation r 
                               JOIN utilisateur u ON r.id_user = u.id_user 
                               JOIN bienimmobilier b ON r.id_property = b.id_property
                               WHERE r.id_reservation = ?");
        $info->execute([$id_res]);
        $result = $info->fetch();

        if ($result && filter_var($result['email'], FILTER_VALIDATE_EMAIL)) {
            $to = $result['email'];
            $subject = "Statut de votre demande pour : " . $result['titre'];
            $message = "Bonjour,\n\nVotre demande a été " . ($action === 'acceptee' ? "acceptée" : "refusée") . ".\n\nMerci pour votre intérêt.";
            $headers = "From: no-reply@immo_web.com";

            @mail($to, $subject, $message, $headers);
        }
    }
}

// Récupération des demandes
$sql = "SELECT r.*, b.titre AS bien_titre, u.nom AS client_nom, u.prenom AS client_prenom, u.email AS client_email
        FROM reservation r
        JOIN bienimmobilier b ON r.id_property = b.id_property
        JOIN utilisateur u ON r.id_user = u.id_user
        WHERE b.id_user = ?
        ORDER BY r.date_demande DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_proprietaire]);
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes reçues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <h2 class="mb-4 text-center">Demandes reçues pour vos biens</h2>

    <?php if (count($demandes) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Bien</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Date visite</th>
                        <th>Message</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($demandes as $demande): ?>
                        <tr>
                            <td><?= htmlspecialchars($demande['bien_titre']) ?></td>
                            <td><?= htmlspecialchars($demande['client_prenom'] . ' ' . $demande['client_nom']) ?></td>
                            <td><?= htmlspecialchars($demande['client_email']) ?></td>
                            <td><?= htmlspecialchars($demande['date_visite']) ?></td>
                            <td><?= nl2br(htmlspecialchars($demande['message'])) ?></td>
                            <td>
                                <?php
                                    $statut = $demande['statut'] ?? 'en_attente';
                                    if ($statut === 'acceptee') {
                                        echo '<span class="badge bg-success">Acceptée</span>';
                                    } elseif ($statut === 'refusee') {
                                        echo '<span class="badge bg-danger">Refusée</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">En attente</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php if ($statut === 'en_attente'): ?>
                                    <form method="POST" class="d-flex gap-1">
                                        <input type="hidden" name="id_reservation" value="<?= $demande['id_reservation'] ?>">
                                        <button name="action" value="acceptee" class="btn btn-sm btn-success">Accepter</button>
                                        <button name="action" value="refusee" class="btn btn-sm btn-danger">Refuser</button>
                                    </form>
                                <?php else: ?>
                                    <em>Traité</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Aucune demande reçue pour l’instant.</div>
    <?php endif; ?>
</div>
</body>
</html>
