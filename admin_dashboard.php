<?php
include 'connexion.php';

// Statistiques
$totalUsers = $pdo->query("SELECT COUNT(*) FROM Utilisateur")->fetchColumn();
$biensLocation = $pdo->query("SELECT COUNT(*) FROM BienImmobilier WHERE type_annonce = 'location'")->fetchColumn();
$biensVente = $pdo->query("SELECT COUNT(*) FROM BienImmobilier WHERE type_annonce = 'vente'")->fetchColumn();
$reservations = $pdo->query("SELECT COUNT(*) FROM Reservation")->fetchColumn();
$reservationsNonTraitees = $pdo->query("SELECT COUNT(*) FROM Reservation WHERE statut = 'en attente'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - Agence Immobilière</title>
  <link rel="stylesheet" href="admin.css">

  <!-- Font Awesome (icônes) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><a href="users_list.php">Utilisateurs</a></li>
      <li><a href="location_list.php">Biens à louer</a></li>
      <li><a href="vente_list.php">Biens à vendre</a></li>
      <li>
        <a href="reservation_list.php">
          Réservations
          <?php if ($reservationsNonTraitees > 0): ?>
            <span class="badge"><i class="fas fa-bell"></i> <?= $reservationsNonTraitees ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li><a href="avis_list.php">Avis</a></li>
      <li><a href="signalement_list.php">Signalements</a></li>
      <li><a href="transaction_list.php">Transactions</a></li>
      <li><a href="#">Paiement</a></li>
      <li><a href="#">Messagerie interne</a></li>
      <li><a href="#">Export de données / rapports</a></li>
      <li><a href="#">Paramètres</a></li>
    </ul>
  </div>

  <div class="main-content">
    <header>
      <h1>Tableau de bord</h1>
    </header>
    <section class="stats">
      <div class="card">Total utilisateurs: <span><?= $totalUsers ?></span></div>
      <div class="card">Biens à louer: <span><?= $biensLocation ?></span></div>
      <div class="card">Biens à vendre: <span><?= $biensVente ?></span></div>
      <div class="card">Réservations: <span><?= $reservations ?></span></div>
    </section>
  </div>
</body>
</html>
