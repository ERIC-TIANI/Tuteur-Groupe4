<?php
require_once('tcpdf/tcpdf.php');

$conn = new mysqli("localhost", "root", "", "Immo_Web");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$id_property = $_POST['id_property'];
$id_acheteur = $_POST['id_acheteur'];
$id_proprietaire = $_POST['id_proprietaire'];
$date = date('Y-m-d H:i:s');

// ➤ 1. Récupérer les infos du bien
$property = $conn->query("SELECT * FROM BienImmobilier WHERE id_property = $id_property")->fetch_assoc();
$prix = $property['prix'];
$titre = $property['titre'];

// ➤ 2. Récupérer les infos de l'acheteur
$acheteur = $conn->query("SELECT * FROM Utilisateur WHERE id_user = $id_acheteur")->fetch_assoc();

// ➤ 3. Enregistrement de la transaction de vente
$conn->query("INSERT INTO TransactionVente (id_property, id_acheteur, id_proprietaire, prix_vente, date_transaction, statut) 
VALUES ($id_property, $id_acheteur, $id_proprietaire, $prix, NOW(), 'finalisée')");

$id_transaction = $conn->insert_id;

// ➤ 4. Enregistrement du paiement
$conn->query("INSERT INTO Paiement (montant, mode_paiement, statut, date_paiement, id_user, id_transaction) 
VALUES ($prix, 'virement bancaire', 'payé', NOW(), $id_acheteur, $id_transaction)");

$id_paiement = $conn->insert_id;

// ➤ 5. Génération du PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Immo_Web');
$pdf->SetTitle('Facture de Vente');
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$html = '
<h1 style="text-align:center;">FACTURE DE VENTE IMMOBILIÈRE</h1>
<p><strong>Date :</strong> ' . date('d/m/Y H:i') . '</p>
<p><strong>ID Transaction :</strong> ' . $id_transaction . '</p>
<hr>
<h3>Détails du Bien</h3>
<ul>
  <li><strong>Titre :</strong> ' . $titre . '</li>
  <li><strong>Prix :</strong> ' . number_format($prix, 0, ',', ' ') . ' FCFA</li>
  <li><strong>Ville :</strong> ' . $property['ville'] . '</li>
</ul>

<h3>Détails de l\'Acheteur</h3>
<ul>
  <li><strong>Nom :</strong> ' . $acheteur['prenom'] . ' ' . $acheteur['nom'] . '</li>
  <li><strong>Email :</strong> ' . $acheteur['email'] . '</li>
  <li><strong>Téléphone :</strong> ' . $acheteur['telephone'] . '</li>
</ul>

<h3>Paiement</h3>
<ul>
  <li><strong>Montant :</strong> ' . number_format($prix, 0, ',', ' ') . ' FCFA</li>
  <li><strong>Mode de paiement :</strong> Virement bancaire</li>
  <li><strong>Statut :</strong> Payé</li>
</ul>

<hr>
<p style="text-align:center;">Merci pour votre achat sur Immo_Web. La vente est enregistrée avec succès.</p>
';

$pdf->writeHTML($html);
$pdf->Output("facture_transaction_$id_transaction.pdf", 'D'); // Téléchargement automatique
exit;
require_once _DIR_ . '/tcpdf/tcpdf.php';