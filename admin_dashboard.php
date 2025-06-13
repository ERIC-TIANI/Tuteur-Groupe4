<?php
session_start();
include 'connexion.php';

$id_admin = $_SESSION['id_user'];
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    die("Accès refusé. Seul l'administrateur peut accéder à cette page.");
}

// 1. Comptage des utilisateurs par rôle
function getUserCountsByRole() {
    global $pdo;
    $roles = ['client', 'proprietaire', 'admin'];
    $counts = [];
    foreach ($roles as $role) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Utilisateur WHERE role = ?");
        $stmt->execute([$role]);
        $counts[$role] = (int) $stmt->fetchColumn();
    }
    return $counts;
}

// 4. Favoris par bien
function getFavorisParBien() {
  
    global $pdo;
    $stmt = $pdo->query("
        SELECT b.titre, COUNT(f.id_favori) as total
        FROM Favori f
        JOIN BienImmobilier b ON f.id_property = b.id_property
        GROUP BY b.id_property
        ORDER BY total DESC
    ");
    
    $labels = [];
    $values = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $row['titre'];
        $values[] = (int) $row['total'];
    }

    return ['labels' => $labels, 'values' => $values];
}
$favorisData = getFavorisParBien();






// 2. Comptage des biens par type
function getBiensCountsByType() {
    global $pdo;
    $stmt = $pdo->query("SELECT type_bien, COUNT(*) as total FROM BienImmobilier GROUP BY type_bien");
    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[$row['type_bien']] = (int) $row['total'];
    }
    return $results;
}

// 3. Réservations par mois (année en cours)
function getReservationsByMonth() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT MONTH(date_debut) as mois, COUNT(*) as total 
        FROM Reservation 
        WHERE YEAR(date_debut) = YEAR(CURDATE())
        GROUP BY mois
    ");
    $months = array_fill(1, 12, 0);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $months[(int)$row['mois']] = (int)$row['total'];
    }
    return $months;
}

// Données à envoyer au JS
$userCounts = getUserCountsByRole();
$biensCounts = getBiensCountsByType();
$reservationsCounts = getReservationsByMonth();

// Statistiques
$totalUsers = $pdo->query("SELECT COUNT(*) FROM Utilisateur")->fetchColumn();
$biensLocation = $pdo->query("SELECT COUNT(*) FROM BienImmobilier WHERE type_annonce = 'location'")->fetchColumn();
$biensVente = $pdo->query("SELECT COUNT(*) FROM BienImmobilier WHERE type_annonce = 'vente'")->fetchColumn();
$reservations = $pdo->query("SELECT COUNT(*) FROM Reservation")->fetchColumn();
$reservationsNonTraitees = $pdo->query("SELECT COUNT(*) FROM Reservation WHERE statut = 'en attente'")->fetchColumn();
$messagesNonLusAdmin = $pdo->prepare("SELECT COUNT(*) FROM Message WHERE id_destinataire = ? AND statut = 'non lu'");
$messagesNonLusAdmin->execute([$id_admin]); // $id_admin doit venir de $_SESSION
$nbMessagesNonLus = $messagesNonLusAdmin->fetchColumn();

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
<li>
  <a href="messages.php" id="menu-messages">
    Messagerie interne
    <span id="messages-badge" style="display:none;" class="badge"><i class="fas fa-envelope"></i> 0</span>
  </a>
</li>


      <li><a href="export_rapport.php">Exporter les données (PDF / XML)</a></li>
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
    <section class="charts" style="display: flex; flex-wrap: wrap; gap: 30px; padding: 30px;">
  <canvas id="usersByRole" width="400" height="300"></canvas>
  <canvas id="favorisParBien" width="800" height="400"></canvas>
  <canvas id="biensByType" width="400" height="300"></canvas>
  <canvas id="reservationsPerMonth" width="800" height="300"></canvas>
</section>

  </div>
<script>
function checkMessages() {
  fetch('check_messages.php')
    .then(response => response.json())
    .then(data => {
      const badge = document.getElementById('messages-badge');
      if (data.count > 0) {
        badge.style.display = 'inline-block';
        badge.textContent = ` ${data.count}`;
      } else {
        badge.style.display = 'none';
      }
    })
    .catch(console.error);
}

// Vérifie immédiatement au chargement
checkMessages();

// Puis toutes les 15 secondes
setInterval(checkMessages, 15000);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // === 1. Utilisateurs par rôle ===
  const usersCtx = document.getElementById('usersByRole').getContext('2d');
  new Chart(usersCtx, {
    type: 'pie',
    data: {
      labels: ['Client', 'Propriétaire', 'Admin'],
      datasets: [{
        data: [<?= $userCounts['client'] ?>, <?= $userCounts['proprietaire'] ?>, <?= $userCounts['admin'] ?>],
        backgroundColor: ['#4caf50', '#2196f3', '#ff9800']
      }]
    },
    options: {
      plugins: {
        title: {
          display: true,
          text: 'Répartition des utilisateurs par rôle'
        }
      }
    }
  });

  // === 2. Biens par type ===
  const biensLabels = <?= json_encode(array_keys($biensCounts)) ?>;
  const biensData = <?= json_encode(array_values($biensCounts)) ?>;

  const biensCtx = document.getElementById('biensByType').getContext('2d');
  new Chart(biensCtx, {
    type: 'bar',
    data: {
      labels: biensLabels,
      datasets: [{
        label: 'Nombre de biens',
        data: biensData,
        backgroundColor: '#3f51b5'
      }]
    },
    options: {
      indexAxis: 'y',
      plugins: {
        title: {
          display: true,
          text: 'Biens par type'
        }
      }
    }
  });

  // === 3. Réservations par mois ===
  const moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
  const reservationsData = <?= json_encode(array_values($reservationsCounts)) ?>;

  const resCtx = document.getElementById('reservationsPerMonth').getContext('2d');
  new Chart(resCtx, {
    type: 'line',
    data: {
      labels: moisLabels,
      datasets: [{
        label: 'Réservations',
        data: reservationsData,
        fill: false,
        borderColor: '#e91e63',
        tension: 0.1
      }]
    },
    options: {
      plugins: {
        title: {
          display: true,
          text: 'Réservations mensuelles (année en cours)'
        }
      }
    }
  });
</script>

<script>
  // === 4. Favoris par bien ===
  const favorisLabels = <?= json_encode($favorisData['labels']) ?>;
  const favorisCounts = <?= json_encode($favorisData['values']) ?>;

  const favorisCtx = document.getElementById('favorisParBien').getContext('2d');
  new Chart(favorisCtx, {
    type: 'bar',
    data: {
      labels: favorisLabels,
      datasets: [{
        label: 'Nombre de favoris',
        data: favorisCounts,
        backgroundColor: favorisLabels.map((_, i) =>
          i === 0 ? '#f44336' : '#90caf9' // le bien le plus populaire en rouge
        )
      }]
    },
    options: {
      indexAxis: 'y',
      plugins: {
        title: {
          display: true,
          text: 'Favoris par bien (le bien le plus favorisé est en rouge)'
        },
        legend: {
          display: false
        }
      }
    }
  });
</script>



</body>
</html>
