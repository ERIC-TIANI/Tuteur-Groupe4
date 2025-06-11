<?php
include 'config.php';
require('fpdf/fpdf.php');

$id_property = $_GET['id'];
$id_acheteur = 1; // ID de l’utilisateur connecté (à remplacer par une session réelle)
$date_transaction = date('Y-m-d');

// Récupérer le bien
$stmt = $pdo->prepare("SELECT * FROM BienImmobilier WHERE id_property = ?");
$stmt->execute([$id_property]);
$bien = $stmt->fetch();
$prix = $bien['prix'];
$id_proprietaire = $bien['id_user'];

// Insérer la transaction
$stmt = $pdo->prepare("INSERT INTO TransactionVente (id_property, id_acheteur, id_proprietaire, prix_vente, date_transaction, statut)
    VALUES (?, ?, ?, ?, ?, 'finalisée')");
$stmt->execute([$id_property, $id_acheteur, $id_proprietaire, $prix, $date_transaction]);

$id_transaction = $pdo->lastInsertId();

// Génération de la facture
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Facture d\'achat immobilier', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Bien : " . $bien['titre'], 0, 1);
$pdf->Cell(0, 10, "Ville : " . $bien['ville'], 0, 1);
$pdf->Cell(0, 10, "Prix : " . $prix . " fcfa", 0, 1);
$pdf->Cell(0, 10, "Date : " . $date_transaction, 0, 1);

$filename = "factures/facture_" . $id_transaction . ".pdf";
$pdf->Output('F', $filename);

echo "<p>Félicitations ! Achat effectué avec succès.</p>";
echo "<a href='$filename' target='_blank'>Télécharger la facture PDF</a><br>";
echo "<a href='index.php'>Retour à la liste</a>";
?>