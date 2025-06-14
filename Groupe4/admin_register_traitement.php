<?php
include 'connexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $telephone = $_POST['telephone'];
    $pays = $_POST['pays'];
    $ville = $_POST['ville'];
    $nationalite = $_POST['nationalite'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Vérifier si l'email existe déjà
    $verif = $pdo->prepare("SELECT id_user FROM Utilisateur WHERE email = ?");
    $verif->execute([$email]);

    if ($verif->rowCount() > 0) {
        echo "Cet email est déjà utilisé.";
        exit;
    }

    // Insertion du nouvel admin
    $stmt = $pdo->prepare("INSERT INTO Utilisateur (
        nom, prenom, date_naissance, telephone, pays, ville, nationalite,
        email, mot_de_passe, role, photo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'admin', NULL)");

    $stmt->execute([
        $nom, $prenom, $date_naissance, $telephone, $pays, $ville, $nationalite,
        $email, $hash
    ]);

    echo "Administrateur créé avec succès.";
    // Rediriger vers la page de connexion ou admin_dashboard
    // header("Location: login.php");
    // exit;
}
?>
