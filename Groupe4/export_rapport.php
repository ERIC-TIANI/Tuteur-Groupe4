<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    die("Accès refusé");
}

require_once('connexion.php');
require('fpdf/fpdf.php'); // Chemin vers ta bibliothèque FPDF

// ✅ Récupérer toutes les données
function fetchAllData(PDO $pdo) {
    $tables = [
        "Utilisateur", "Categorie", "BienImmobilier", "Image",
        "Reservation", "HistoriqueReservation", "Avis",
        "Message", "TransactionVente", "Favori", "Signalement", "Indisponibilite"
    ];
    $data = [];

    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT * FROM $table");
        $data[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $data;
}

$timestamp = date('Ymd_His'); // Format : 20250613_145230

// ✅ Export XML
function exportToXML($data, $filename) {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Immo_Web></Immo_Web>');
    foreach ($data as $table => $rows) {
        $tableNode = $xml->addChild($table);
        foreach ($rows as $row) {
            $recordNode = $tableNode->addChild("record");
            foreach ($row as $col => $val) {
                $recordNode->addChild($col, htmlspecialchars((string)($val ?? '')));
            }
        }
    }

    $xmlPath = "exports/$filename.xml";
    $xml->asXML($xmlPath);
    return $xmlPath;
}


// ✅ Export PDF avec FPDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Rapport complet - Base de données Immo_Web', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 0, 128);
        $this->Cell(0,10,iconv('UTF-8', 'ISO-8859-1', $title),0,1,'L');
        $this->SetTextColor(0);
    }

    function Record($row, $index) {
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 6, "Enregistrement $index :", 0, 1);
        foreach ($row as $key => $value) {
            $this->MultiCell(0, 5, "$key : $value", 0, 1);
        }
        $this->Ln(2);
    }
}

function exportToPDF($data, $filename) {
    $pdf = new PDF();
    $pdf->AddPage();
    foreach ($data as $table => $rows) {
        $pdf->SectionTitle($table);
        if (count($rows) > 0) {
            foreach ($rows as $i => $row) {
                $pdf->Record($row, $i + 1);
            }
        } else {
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->Cell(0, 6, "Aucune donnée.", 0, 1);
        }
        $pdf->Ln(4);
    }

    $pdfPath = "exports/$filename.pdf";
    $pdf->Output('F', $pdfPath);
    return $pdfPath;
}


// 📁 Créer le dossier d’export s’il n’existe pas
if (!file_exists('exports')) {
    mkdir('exports', 0777, true);
}

// ▶ Traitement
$data = fetchAllData($pdo);
$timestamp = date('Ymd_His');
$pdfFile = exportToPDF($data, "immo_web_export_$timestamp");
$xmlFile = exportToXML($data, "immo_web_export_$timestamp");


// ✅ Liens pour téléchargement
echo "<h2>Exportation réussie</h2>";
echo "<p><a href='$pdfFile' download>Télécharger le PDF</a></p>";
echo "<p><a href='$xmlFile' download>Télécharger le XML</a></p>";
