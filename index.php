<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "Immo_Web");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupérer uniquement les biens en vente
$sql = "SELECT * FROM BienImmobilier WHERE type_annonce = 'vente'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Nos biens immobiliers en vente</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5f5;
      color: #333;
    }

    header {
      background-color: #2c3e50;
      color: white;
      padding: 2rem;
      text-align: center;
      font-size: 2rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      padding: 2rem;
      max-width: 1200px;
      margin: auto;
    }

    .property-card {
      background-color: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .property-card:hover {
      transform: translateY(-5px);
    }

    .property-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .property-details {
      padding: 1rem;
    }

    .property-title {
      font-size: 1.2rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }

    .property-price {
      color: #27ae60;
      font-size: 1.1rem;
      margin-bottom: 1rem;
    }

    .button-group {
      display: flex;
      gap: 10px;
    }

    .details-button, .more-info-button {
      background-color: #2980b9;
      color: white;
      padding: 0.6rem 1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      text-align: center;
    }

    .details-button:hover, .more-info-button:hover {
      background-color: #1c5980;
    }
  </style>
</head>
<body>

  <header>Nos biens immobiliers en vente</header>

  <section class="container">
    <?php while ($bien = $result->fetch_assoc()): ?>
      <div class="property-card">
        <?php
        // Récupérer une image du bien (s'il y en a)
        $id_property = $bien['id_property'];
        $img_sql = "SELECT url_image FROM Image WHERE id_property = $id_property LIMIT 1";
        $img_result = $conn->query($img_sql);
        $img = ($img_result && $img_result->num_rows > 0) ? $img_result->fetch_assoc()['url_image'] : 'default.jpg';
        ?>
        <img src="<?= htmlspecialchars($img) ?>" alt="Image du bien" class="property-image">

        <div class="property-details">
          <div class="property-title"><?= htmlspecialchars($bien['titre']) ?></div>
          <div class="property-price"><?= number_format($bien['prix'], 0, ',', ' ') ?> FCFA</div>
          <p><?= htmlspecialchars(substr($bien['description'], 0, 150)) ?>...</p>
          <div class="button-group">
            <a href="formulaire_vente.php?id_property=<?= $bien['id_property'] ?>" class="details-button">Vendre ce bien</a>
            <a href="details_bien.php?id_property=<?= $bien['id_property'] ?>" class="more-info-button">Plus de détails</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </section>

</body>
</html>