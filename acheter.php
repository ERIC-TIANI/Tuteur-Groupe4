<?php
include 'config.php';
require('fpdf/fpdf.php');

$id_property = $_GET['id'];
$id_acheteur = 1; // À remplacer par la session de l'utilisateur connecté
$date_transaction = date('Y-m-d');

// Récupérer le bien immobilier
$stmt = $pdo->prepare("SELECT * FROM BienImmobilier WHERE id_property = ?");
$stmt->execute([$id_property]);
$bien = $stmt->fetch();

if (!$bien) {
    echo "<p style='color:red;'>Bien immobilier non trouvé.</p>";
    exit;
}

$prix = $bien['prix'];
$id_proprietaire = $bien['id_user'];

// Insérer la transaction
$stmt = $pdo->prepare("INSERT INTO TransactionVente (id_property, id_acheteur, id_proprietaire, prix_vente, date_transaction, statut)
    VALUES (?, ?, ?, ?, ?, 'finalisée')");
$stmt->execute([$id_property, $id_acheteur, $id_proprietaire, $prix, $date_transaction]);

$id_transaction = $pdo->lastInsertId();

// Générer la facture PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Facture d\'achat immobilier', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Bien : " . $bien['titre'], 0, 1);
$pdf->Cell(0, 10, "Ville : " . $bien['ville'], 0, 1);
$pdf->Cell(0, 10, "Prix : " . number_format($prix, 0, ',', ' ') . " FCFA", 0, 1);
$pdf->Cell(0, 10, "Date : " . $date_transaction, 0, 1);

$filename = "factures/facture_" . $id_transaction . ".pdf";
$pdf->Output('F', $filename);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation d'achat</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 80px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            text-align: center;
        }

        h1 {
            color: #4caf50;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #333;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            text-decoration: none;
            background-color: #4caf50;
            color: white;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        a:hover {
            background-color: #388e3c;
        }

        .links {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🎉 Achat réussi !</h1>
    <p>Félicitations ! L'achat du bien <strong><?php echo htmlspecialchars($bien['titre']); ?></strong> a été enregistré avec succès.</p>
    <div class="links">
        <a href="<?php echo $filename; ?>" target="_blank">📄 Télécharger la facture PDF</a><br><br>
        <a href="index.php">🏠 Retour à la liste des biens</a>
    </div>
</div>

</body>
</html>
