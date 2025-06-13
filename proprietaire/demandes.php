<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "immo_web";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier que l'utilisateur est connecté (exemple simple)
if (!isset($_SESSION['id_user'])) {
    die("Accès refusé. Veuillez vous connecter.");
}

$id_user_connecte = $_SESSION['id_user'];

// Traitement du changement de statut
$message_flash = '';
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id_demande = (int)$_GET['id'];

    if (in_array($action, ['valider', 'refuser'])) {
        $nouveau_statut = ($action === 'valider') ? 'validé' : 'refusé';

        try {
            $update = $conn->prepare("UPDATE demandes SET statut = :statut WHERE id = :id");
            $update->execute([
                ':statut' => $nouveau_statut,
                ':id' => $id_demande,
            ]);
            $message_flash = "La demande #$id_demande a bien été mise à jour (statut : $nouveau_statut).";
        } catch (Exception $e) {
            $message_flash = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Requête pour récupérer les demandes
// Optionnel : ne récupérer que celles des biens appartenant à l'utilisateur connecté
// Pour cela, on suppose que la table 'bienimmobilier' a une colonne 'id_user' qui est le propriétaire

$sql = "SELECT d.*, b.titre 
        FROM demandes d 
        JOIN bienimmobilier b ON d.id_property = b.id_property
        WHERE b.id_user = :id_user
        ORDER BY d.date_creation DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([':id_user' => $id_user_connecte]);


/*$sql = "SELECT d.*, b.titre 
        FROM demandes d 
        LEFT JOIN bienimmobilier b ON d.id_property = b.id_property
        ORDER BY d.date_creation DESC";
$stmt = $conn->query($sql);
*/
$demandes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion des demandes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f9f9f9;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
        }
        th, td {
            padding: 10px 15px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        th {
            background: #4CAF50;
            color: white;
            text-align: left;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-weight: bold;
            font-size: 0.9em;
            margin-right: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-valider {
            background-color: #28a745;
        }
        .btn-refuser {
            background-color: #dc3545;
        }
        .statut-validé {
            color: green;
            font-weight: bold;
        }
        .statut-refusé {
            color: red;
            font-weight: bold;
        }
        .statut-en_attente, .statut-en attente {
            color: orange;
            font-weight: bold;
        }
        .flash-message {
            background-color: #dff0d8;
            border: 1px solid #3c763d;
            padding: 10px 15px;
            color: #3c763d;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            margin: 0;
            font-family: inherit;
            font-size: 0.95em;
        }
    </style>
</head>
<body>

<h1>Gestion des demandes de réservation</h1>

<?php if ($message_flash): ?>
    <div class="flash-message"><?= htmlspecialchars($message_flash) ?></div>
<?php endif; ?>

<?php if (empty($demandes)): ?>
    <p>Aucune demande à afficher.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID Demande</th>
                <th>Bien concerné</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Message</th>
                <th>Date de la demande</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($demandes as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['id']) ?></td>
                    <td><?= htmlspecialchars($d['titre'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($d['nom']) ?></td>
                    <td><a href="mailto:<?= htmlspecialchars($d['email']) ?>"><?= htmlspecialchars($d['email']) ?></a></td>
                    <td><pre><?= htmlspecialchars($d['message']) ?></pre></td>
                    <td><?= htmlspecialchars($d['date_creation']) ?></td>
                    <td class="statut-<?= str_replace([' ', '-'], '_', strtolower($d['statut'])) ?>">
                        <?= htmlspecialchars($d['statut']) ?>
                    </td>
                    <td>
                        <?php if ($d['statut'] === 'en attente'): ?>
                            <a href="?action=valider&id=<?= $d['id'] ?>" class="btn btn-valider" onclick="return confirm('Valider la demande #<?= $d['id'] ?> ?');">Valider</a>
                            <a href="?action=refuser&id=<?= $d['id'] ?>" class="btn btn-refuser" onclick="return confirm('Refuser la demande #<?= $d['id'] ?> ?');">Refuser</a>
                        <?php else: ?>
                            <em>Action terminée</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>