<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "immo_web";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

// Récupérer l'ID du bien depuis l'URL
$id_property = isset($_GET['id_property']) ? (int)$_GET['id_property'] : 0;
$id_user = $_SESSION['id_user'];

$success_message = '';
$error_message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';

    if ($date_debut && $date_fin && $date_debut <= $date_fin) {
        $sql = "INSERT INTO reservation (date_debut, date_fin, statut, id_user, id_property, date_demande)
                VALUES (:date_debut, :date_fin, 'en attente', :id_user, :id_property, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':date_debut' => $date_debut,
            ':date_fin' => $date_fin,
            ':id_user' => $id_user,
            ':id_property' => $id_property
        ]);

        $success_message = "Votre demande de réservation a été envoyée avec succès.";
    } else {
        $error_message = "Veuillez entrer des dates valides.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver ce bien</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .form-container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .success, .error {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        button {
            background-color: #007BFF;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Réserver ce bien</h2>

        <?php if ($success_message): ?>
            <div class="success"><?= htmlspecialchars($success_message) ?></div>
        <?php elseif ($error_message): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="date_debut">Date de début</label>
            <input type="date" name="date_debut" required>

            <label for="date_fin">Date de fin</label>
            <input type="date" name="date_fin" required>

            <button type="submit">Envoyer la réservation</button>
        </form>
    </div>
</body>
</html>
