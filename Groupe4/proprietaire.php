<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'proprietaire') {
    header("Location: auth.php");
    exit;
}

require 'config.php';
$id_user = $_SESSION['id_user'];
$nom = $_SESSION['nom'] ?? 'Propriétaire';

// Récupérer les statistiques
$biensStmt = $pdo->prepare("SELECT COUNT(*) FROM bienimmobilier WHERE id_user = ?");
$biensStmt->execute([$id_user]);
$nbBiens = $biensStmt->fetchColumn();

$demandesStmt = $pdo->prepare("SELECT COUNT(*) FROM reservation WHERE id_user = ? AND statut = 'en_attente'");
$demandesStmt->execute([$id_user]);
 $nbDemandes = $demandesStmt->fetchColumn();

$locationsStmt = $pdo->prepare("SELECT COUNT(*) FROM contrats WHERE id_user = ? AND date_fin > NOW()");
$locationsStmt->execute([$id_user]);
$nbLocationsActives = $locationsStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Propriétaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .user-greeting {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        .nav-pills {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .nav-link {
            color: var(--dark-text);
            border-radius: 8px;
            padding: 0.75rem 1.25rem;
            margin: 0.25rem 0;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .nav-link:hover, .nav-link.active {
            background: var(--secondary-color);
            color: white;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--light-text);
            font-size: 0.9rem;
        }
        
        .quick-actions {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            background: var(--light-bg);
            border-radius: 8px;
            color: var(--dark-text);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: var(--secondary-color);
            color: white;
        }
        
        .welcome-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem 0;
            }
            
            .user-avatar {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="container">
            <div class="user-greeting">
                <div class="user-avatar">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <h1 class="mb-1">Bonjour, <?= htmlspecialchars($nom) ?> !</h1>
                    <p class="mb-0">Tableau de bord propriétaire</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="nav-pills sticky-top" style="top: 20px;">
                    <a href="ajouter_bien.php" class="nav-link">
                        <i class="bi bi-plus-circle"></i> Ajouter un bien
                    </a>
                    <a href="mes_biens.php" class="nav-link">
                        <i class="bi bi-house"></i> Mes biens
                    </a>
                    <a href="reservation_list.php" class="nav-link">
                        <i class="bi bi-envelope"></i> Reservation
                        <?php if ($nbDemandes > 0): ?>
                            <span class="badge bg-danger ms-auto"><?= $nbDemandes ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="locations.php" class="nav-link">
                        <i class="bi bi-file-earmark-text"></i> Locations
                    </a>
                    <a href="profil_proprietaire.php" class="nav-link">
                        <i class="bi bi-person"></i> Mon profil
                    </a>
                    <hr>
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(52, 152, 219, 0.1); color: var(--secondary-color);">
                                <i class="bi bi-house"></i>
                            </div>
                            <div class="stat-value"><?= $nbBiens ?></div>
                            <div class="stat-label">Biens immobiliers</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(231, 76, 60, 0.1); color: var(--accent-color);">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="stat-value"><?= $nbDemandes ?></div>
                            <div class="stat-label">Demandes en attente</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="stat-value"><?= $nbLocationsActives ?></div>
                            <div class="stat-label">Locations actives</div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="welcome-section">
                            <h3 class="mb-3"><i class="bi bi-speedometer2"></i> Bienvenue sur votre espace propriétaire</h3>
                            <p class="mb-4">Gérez facilement vos biens immobiliers, consultez les demandes de location et suivez vos locations en cours.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="ajouter_bien.php" class="action-btn">
                                        <i class="bi bi-plus-lg"></i> Ajouter un nouveau bien
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="mes_biens.php" class="action-btn">
                                        <i class="bi bi-house"></i> Voir mes biens
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="demandes.php" class="action-btn">
                                        <i class="bi bi-envelope"></i> Consulter les demandes
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="profil_proprietaire.php" class="action-btn">
                                        <i class="bi bi-gear"></i> Paramètres du compte
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="quick-actions">
                            <h5 class="mb-3"><i class="bi bi-lightning"></i> Actions rapides</h5>
                            <div class="d-grid gap-2">
                                <a href="ajouter_bien.php" class="btn btn-primary">
                                    <i class="bi bi-plus-lg"></i> Ajouter un bien
                                </a>
                                <a href="demandes.php" class="btn btn-outline-primary">
                                    <i class="bi bi-envelope"></i> Voir les demandes
                                </a>
                                <a href="profil_proprietaire.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-person"></i> Mon profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>