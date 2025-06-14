<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'proprietaire') {
    header("Location: auth.php");
    exit;
}

require 'config.php';
$id_user = $_SESSION['id_user'];

$stmt = $pdo->prepare("SELECT * FROM bienimmobilier WHERE id_user = ?");
$stmt->execute([$id_user]);
$biens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes biens immobiliers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --light-text: #7f8c8d;
        }
        
        body {
            background: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        
        .carousel-inner {
            border-radius: 12px 12px 0 0;
            overflow: hidden;
        }
        
        .carousel-inner img {
            height: 280px;
            object-fit: cover;
            width: 100%;
        }
        
        .carousel-control-prev, .carousel-control-next {
            background: rgba(0,0,0,0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            margin: 0 15px;
        }
        
        .card-body {
            padding: 1.75rem;
        }
        
        .card-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--primary-color);
        }
        
        .property-type {
            display: inline-block;
            background: var(--secondary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .property-details {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .property-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--light-text);
            font-size: 0.9rem;
        }
        
        .card-text.description {
            color: var(--light-text);
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        
        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 1.5rem;
        }
        
        .btn-action {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background-color: var(--secondary-color);
            border: none;
        }
        
        .btn-edit:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background-color: var(--accent-color);
            border: none;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--light-text);
            margin-bottom: 1rem;
        }
        
        .add-property-btn {
            background: var(--secondary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .add-property-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .carousel-inner img {
                height: 220px;
            }
            
            .property-details {
                gap: 1rem;
            }
            
            .card-body {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3"><i class="bi bi-house-heart"></i> Mes biens immobiliers</h1>
                <p class="lead mb-0">Gérez vos propriétés en toute simplicité</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="ajouter_bien.php" class="btn btn-light btn-lg add-property-btn">
                    <i class="bi bi-plus-circle"></i> Ajouter un bien
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container my-4">
    <?php if (count($biens) > 0): ?>
        <div class="row">
            <?php foreach ($biens as $bien): ?>
                <?php
                    $imagesStmt = $pdo->prepare("SELECT url_image FROM image WHERE id_property = ?");
                    $imagesStmt->execute([$bien['id_property']]);
                    $images = $imagesStmt->fetchAll(PDO::FETCH_COLUMN);
                ?>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <?php if (!empty($images)): ?>
                            <div id="carousel<?= $bien['id_property'] ?>" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                                <div class="carousel-inner">
                                    <?php foreach ($images as $index => $img): ?>
                                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                            <img src="<?= htmlspecialchars($img) ?>" class="d-block w-100" alt="Image du bien <?= htmlspecialchars($bien['titre']) ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($images) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?= $bien['id_property'] ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Précédent</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carousel<?= $bien['id_property'] ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Suivant</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <img src="https://via.placeholder.com/800x400?text=Pas+d'image" class="card-img-top" alt="Pas d'image disponible">
                        <?php endif; ?>

                        <div class="card-body">
                            <span class="property-type">
                                <?= htmlspecialchars($bien['type_annonce'] === 'location' ? 'À louer' : 'À vendre') ?>
                            </span>
                            <h5 class="card-title"><?= htmlspecialchars($bien['titre']) ?></h5>
                            
                            <div class="property-details">
                                <span class="property-detail">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($bien['adresse']) ?>
                                </span>
                                <span class="property-detail">
                                    <i class="bi bi-arrows-angle-expand"></i> <?= htmlspecialchars($bien['superficie']) ?> m²
                                </span>
                                <span class="property-detail">
                                    <i class="bi bi-door-open"></i> <?= htmlspecialchars($bien['nb_pieces']) ?> pièces
                                </span>
                            </div>
                            
                            <p class="card-text description"><?= htmlspecialchars(substr($bien['description'], 0, 150)) ?>...</p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="price mb-0">
                                    <?= number_format($bien['prix'], 0, ',', ' ') ?> F
                                    <?= ($bien['type_annonce'] === 'location') ? "<small>/mois</small>" : "" ?>
                                </p>
                                
                                <div class="d-flex gap-2">
                                    <a href="modifier_bien.php?id=<?= $bien['id_property'] ?>" class="btn btn-action btn-edit">
                                        <i class="bi bi-pencil"></i> Modifier
                                    </a>
                                    <a href="supprimer_bien.php?id=<?= $bien['id_property'] ?>" class="btn btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bien ?')">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-house-x"></i>
            <h3 class="mb-3">Aucun bien enregistré</h3>
            <p class="mb-4">Vous n'avez pas encore ajouté de propriété à votre portfolio.</p>
            <a href="ajouter_bien.php" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Ajouter votre premier bien
            </a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>