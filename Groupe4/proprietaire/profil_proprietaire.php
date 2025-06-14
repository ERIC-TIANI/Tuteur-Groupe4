<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'proprietaire') {
    header("Location: auth.php");
    exit;
}

require 'config.php';
$id = $_SESSION['id_user'];

// Récupérer les infos actuelles
$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_user = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$message = "";
$hasPhoto = !empty($user['photo_profil']);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gestion de la photo de profil
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/profils/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Vérification du fichier
        $check = getimagesize($_FILES['photo']['tmp_name']);
        if ($check !== false && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                // Supprimer l'ancienne photo si elle existe
                if ($hasPhoto && file_exists($user['photo_profil'])) {
                    unlink($user['photo_profil']);
                }
                
                $photoUpdate = $pdo->prepare("UPDATE utilisateur SET photo_profil = ? WHERE id_user = ?");
                $photoUpdate->execute([$targetFile, $id]);
                $user['photo_profil'] = $targetFile;
                $hasPhoto = true;
                $message .= "📷 Photo de profil mise à jour.<br>";
            } else {
                $message .= "❌ Erreur lors du téléchargement de la photo.<br>";
            }
        } else {
            $message .= "❌ Format de fichier non supporté (seuls JPG, JPEG, PNG et GIF sont autorisés).<br>";
        }
    }

    // Mise à jour des autres infos
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);

    $update = $pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id_user = ?");
    $update->execute([$nom, $prenom, $email, $telephone, $id]);
    $message .= "✅ Informations mises à jour avec succès.";

    // Si mot de passe changé
    if (!empty($_POST['new_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $passUpdate = $pdo->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id_user = ?");
            $passUpdate->execute([$hashed, $id]);
            $message .= "<br>🔐 Mot de passe mis à jour.";
        } else {
            $message .= "<br>❌ Les mots de passe ne correspondent pas.";
        }
    }
}

// Recharger les données utilisateur après mise à jour
$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_user = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$hasPhoto = !empty($user['photo_profil']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil</title>
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
            background: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .profile-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            overflow: hidden;
            background: white;
        }
        
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .profile-picture-container {
            position: relative;
            display: inline-block;
            margin-top: -75px;
        }
        
        .profile-picture-edit {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: var(--secondary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-picture-edit:hover {
            background: #2980b9;
            transform: scale(1.1);
        }
        
        .profile-info {
            padding: 2rem;
        }
        
        .profile-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .profile-email {
            color: var(--light-text);
            margin-bottom: 1.5rem;
        }
        
        .info-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            color: var(--light-text);
        }
        
        .btn-edit {
            background-color: var(--secondary-color);
            border: none;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-edit:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .form-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #eee;
        }
        
        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        
        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .profile-picture {
                width: 120px;
                height: 120px;
            }
            
            .profile-picture-container {
                margin-top: -60px;
            }
            
            .profile-name {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="profile-header">
    <div class="container text-center">
        <h1><i class="bi bi-person-circle"></i> Mon profil</h1>
    </div>
</div>

<div class="container">
    <?php if ($message): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="profile-card text-center">
                <div class="profile-picture-container">
                    <img src="<?= $hasPhoto ? htmlspecialchars($user['photo_profil']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['prenom'] . '+' . $user['nom']) . '&size=150&background=random' ?>" 
                         class="profile-picture" 
                         alt="Photo de profil">
                    <div class="profile-picture-edit" data-bs-toggle="modal" data-bs-target="#photoModal">
                        <i class="bi bi-camera"></i>
                    </div>
                </div>
                
                <div class="profile-info">
                    <h2 class="profile-name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>
                    <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <div class="text-start">
                        <div class="info-item">
                            <div class="info-label">Téléphone</div>
                            <div class="info-value"><?= htmlspecialchars($user['telephone']) ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Rôle</div>
                            <div class="info-value text-capitalize"><?= htmlspecialchars($user['role']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <form method="POST" enctype="multipart/form-data">
                <!-- Modal pour la photo de profil -->
                <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Changer la photo de profil</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Sélectionner une image</label>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                    <div class="form-text">Formats acceptés : JPG, JPEG, PNG, GIF (max 2MB)</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3 class="section-title"><i class="bi bi-person-lines-fill"></i> Informations personnelles</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" required value="<?= htmlspecialchars($user['prenom']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required value="<?= htmlspecialchars($user['nom']) ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" required value="<?= htmlspecialchars($user['telephone']) ?>">
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-edit text-white">
                            <i class="bi bi-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3 class="section-title"><i class="bi bi-shield-lock"></i> Sécurité</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="new_password" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input type="password" name="confirm_password" class="form-control">
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-edit text-white">
                            <i class="bi bi-key"></i> Mettre à jour le mot de passe
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>