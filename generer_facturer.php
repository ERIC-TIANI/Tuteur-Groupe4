<?php
require('fpdf/fpdf.php');
$conn = new mysqli("localhost", "root", "", "Immo_Web");

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les données du formulaire
$id_property = $_POST['id_property'];
$id_client = $_POST['id_client']; // supposé être sélectionné ou saisi
$date_vente = date('Y-m-d');

// Récupérer les infos du bien
$req = $conn->query("SELECT * FROM BienImmobilier WHERE id_property = $id_property");
$bien = $req->fetch_assoc();

// Récupérer les infos du propriétaire
$req_prop = $conn->query("SELECT * FROM Utilisateur WHERE id_user = " . $bien['id_user']);
$proprietaire = $req_prop->fetch_assoc();

// Récupérer les infos de l'acheteur
$req_client = $conn->query("SELECT * FROM Utilisateur WHERE id_user = $id_client");
$client = $req_client->fetch_assoc();

// Enregistrer la transaction
$conn->query("INSERT INTO TransactionVente (id_property, id_acheteur, id_proprietaire, prix_vente, date_transaction, statut)
              VALUES ($id_property, $id_client, {$bien['id_user']}, {$bien['prix']}, '$date_vente', 'finalisée')");

// Création du PDF
$pdf = new FPDF();
$pdf->AddPage();

// En-tête
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Facture de Vente Immobilier', 0, 1, 'C');
$pdf->Ln(10);

// Infos client
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, "Acheteur : " . $client['prenom'] . " " . $client['nom'], 0, 1);
$pdf->Cell(0, 8, "Email : " . $client['email'], 0, 1);
$pdf->Cell(0, 8, "Téléphone : " . $client['telephone'], 0, 1);
$pdf->Ln(5);

// Infos vendeur
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, "Vendeur :", 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $proprietaire['prenom'] . " " . $proprietaire['nom'], 0, 1);
$pdf->Cell(0, 8, "Email : " . $proprietaire['email'], 0, 1);
$pdf->Ln(5);

// Infos bien
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, "Détails du bien :", 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, "Titre : " . $bien['titre'], 0, 1);
$pdf->MultiCell(0, 8, "Description : " . $bien['description']);
$pdf->Cell(0, 8, "Adresse : " . $bien['adresse'] . ", " . $bien['ville'], 0, 1);
$pdf->Cell(0, 8, "Prix : " . number_format($bien['prix'], 0, ',', ' ') . " FCFA", 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, "Date de la transaction : " . $date_vente, 0, 1, 'R');

$pdf->Output('I', 'facture.pdf'); // 'I' pour afficher dans le navigateur
?>