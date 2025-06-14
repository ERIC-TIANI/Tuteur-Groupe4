<?php
session_start();
require_once 'connexion.php'; // $pdo

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    die("Accès refusé. Seul l'administrateur peut accéder à cette page.");
}

$id_admin = $_SESSION['id_user'];
$pdo->prepare("UPDATE Message SET statut = 'lu' WHERE id_destinataire = ? AND statut = 'non lu'")
    ->execute([$id_admin]);


// === TRAITEMENT BOUTON WHATSAPP ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contacter'])) {
    $telephone = preg_replace('/[^0-9]/', '', $_POST['telephone']);
    $messageId = intval($_POST['message_id']);
    $expediteurNom = htmlspecialchars($_POST['expediteur_nom'] ?? '');
    $expediteurPrenom = htmlspecialchars($_POST['expediteur_prenom'] ?? '');

    if (!str_starts_with($telephone, '237')) {
        $telephone = '237' . ltrim($telephone, '0');
    }

    $_SESSION['contacted_' . $messageId] = true;

    $messageTexte = "Bonjour $expediteurPrenom $expediteurNom, nous avons bien reçu votre message. Nous vous contacterons sous peu. Merci.";
    $messageTexteEncoded = urlencode($messageTexte);

    echo "<script>window.location.href = 'https://wa.me/$telephone?text=$messageTexteEncoded';</script>";
    exit;
}

// === FILTRES DE RECHERCHE ===
$nomRecherche = $_GET['nom'] ?? '';
$dateRecherche = $_GET['date'] ?? '';

$whereClauses = [];
$params = [];

if (!empty($nomRecherche)) {
    $whereClauses[] = "(u1.nom LIKE ? OR u1.prenom LIKE ?)";
    $params[] = '%' . $nomRecherche . '%';
    $params[] = '%' . $nomRecherche . '%';
}

if (!empty($dateRecherche)) {
    $whereClauses[] = "DATE(m.date_envoi) = ?";
    $params[] = $dateRecherche;
}

$whereSQL = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// === RÉCUPÉRATION DE TOUS LES MESSAGES ===
$sql = "SELECT m.*, 
               u1.nom AS expediteur_nom, u1.prenom AS expediteur_prenom, u1.telephone AS expediteur_tel, u1.role AS expediteur_role,
               u2.nom AS destinataire_nom, u2.prenom AS destinataire_prenom, u2.role AS destinataire_role, u2.id_user AS destinataire_id
        FROM Message m
        JOIN Utilisateur u1 ON m.id_expediteur = u1.id_user
        JOIN Utilisateur u2 ON m.id_destinataire = u2.id_user
        $whereSQL
        ORDER BY m.date_envoi DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boîte de réception (Admin)</title>
    <style>
        .message {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
        }
        .whatsapp-btn {
            background-color: #25D366;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .whatsapp-btn:hover {
            background-color: #1EBE5D;
        }
        .search-form {
            margin: 20px;
            padding: 10px;
            border: 1px solid #aaa;
            border-radius: 8px;
        }
        .search-form input[type="text"],
        .search-form input[type="date"] {
            padding: 6px;
            margin-right: 10px;
        }
        .search-form button {
            padding: 6px 12px;
        }
    </style>
</head>
<body>
    <h2>Messages reçus (Vue administrateur)</h2>

    <form method="get" class="search-form">
        <label>Nom / Prénom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($nomRecherche) ?>">

        <label>Date d'envoi :</label>
        <input type="date" name="date" value="<?= htmlspecialchars($dateRecherche) ?>">

        <button type="submit">Rechercher</button>
    </form>

    <?php if (empty($messages)): ?>
        <p>Aucun message trouvé.</p>
    <?php else: ?>
        <?php foreach ($messages as $row): ?>
            <div class="message">
                <strong>De :</strong> <?= htmlspecialchars($row['expediteur_prenom'] . ' ' . $row['expediteur_nom']) ?> (<?= $row['expediteur_role'] ?>)<br>
                <strong>À :</strong> <?= htmlspecialchars($row['destinataire_prenom'] . ' ' . $row['destinataire_nom']) ?> (<?= $row['destinataire_role'] ?>)<br>
                <strong>Date :</strong> <?= $row['date_envoi'] ?><br>
                <strong>Contenu :</strong>
                <p><?= nl2br(htmlspecialchars($row['contenu'])) ?></p>

                <?php
                // Afficher le bouton WhatsApp uniquement si :
                // - L'admin est le destinataire
                // - L'expéditeur est un client
                if (
                    $row['destinataire_id'] == $id_admin &&
                    $row['expediteur_role'] === 'client' &&
                    !isset($_SESSION['contacted_' . $row['id_message']])
                ): ?>
                    <form method="post">
                        <input type="hidden" name="message_id" value="<?= $row['id_message'] ?>">
                        <input type="hidden" name="telephone" value="<?= htmlspecialchars($row['expediteur_tel']) ?>">
                        <input type="hidden" name="expediteur_nom" value="<?= htmlspecialchars($row['expediteur_nom']) ?>">
                        <input type="hidden" name="expediteur_prenom" value="<?= htmlspecialchars($row['expediteur_prenom']) ?>">
                        <button class="whatsapp-btn" type="submit" name="contacter">Contacter via WhatsApp</button>
                    </form>
                <?php elseif ($row['destinataire_id'] == $id_admin && $row['expediteur_role'] === 'client'): ?>
                    <em>Déjà contacté via WhatsApp</em>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
