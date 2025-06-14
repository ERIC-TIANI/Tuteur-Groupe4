<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Agence Immobilière Élite - Votre partenaire pour des transactions immobilières haut de gamme">
    <meta name="author" content="Agence Immobilière Élite">
    <link rel="shortcut icon" href="assets/ico/favicon.png">

    <title>Agence Immobilière Élite | Propriétés d'exception</title>

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Custom styles -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --accent-color: #3498db;
            --text-color: #333;
            --text-light: #7f8c8d;
        }
        
        body {
            font-family: 'Raleway', sans-serif;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
        }
        
        .navbar {
            background-color: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            transition: all 0.3s;
        }
        
        .navbar.scrolled {
            padding: 10px 0;
            background-color: rgba(255, 255, 255, 0.98) !important;
        }
        
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 24px;
            color: var(--primary-color) !important;
        }
        
        .nav-link {
            color: var(--dark-color) !important;
            font-weight: 600;
            margin: 0 10px;
            position: relative;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--secondary-color);
            bottom: 0;
            left: 0;
            transition: width 0.3s;
        }
        
        .nav-link:hover:after {
            width: 100%;
        }
        
        #headerwrap {
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(44, 62, 80, 0.8)), 
                        url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 180px 0 150px;
            text-align: center;
        }
        
        #headerwrap h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        #headerwrap p {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #c0392b;
            border-color: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            position: relative;
            margin-bottom: 60px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background: var(--secondary-color);
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .service-card {
            background: white;
            border-radius: 10px;
            padding: 40px 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            text-align: center;
            border-bottom: 3px solid transparent;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid var(--secondary-color);
        }
        
        .service-icon {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .property-card {
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .property-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .property-img {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        
        .property-info {
            padding: 20px;
            background: white;
        }
        
        .property-price {
            color: var(--secondary-color);
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .property-features {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
        }
        
        .property-features li {
            display: flex;
            align-items: center;
            color: var(--text-light);
        }
        
        .property-features i {
            margin-right: 5px;
            color: var(--accent-color);
        }
        
        .agent-card {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .agent-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
       
        .section-divider {
            padding: 100px 0;
            color: white;
            text-align: center;
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
            position: relative;
        }
        
        .section-divider:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(44, 62, 80, 0.8);
        }
        
        form .form-control {
            border-radius: 0;
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        form .form-control:focus {
            box-shadow: none;
            border-color: var(--secondary-color);
        }
        
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 60px 0 20px;
        }
        
        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            
        }
        
        .social-icons a:hover {
            background: var(--secondary-color);
        }
    </style>
</head>

<body data-spy="scroll" data-target="#navbar-main">
  
<nav id="navbar-main" class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#home">Immobilier<span style="color: var(--secondary-color);">Élite</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">À propos</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#properties">Propriétés</a></li>
                <li class="nav-item"><a class="nav-link" href="#team">Équipe</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                <li class="nav-item ms-2"><a class="btn btn-primary" href="login.php">connexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ==== HEADER ==== -->
<div id="headerwrap" id="home" name="home">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1>Votre logement notre priorite</h1>
                <p class="lead">Découvrez notre sélection de biens immobiliers haut de gamme</p>
                <a href="#properties" class="btn btn-primary btn-lg mt-4">Explorer nos biens</a>
            </div>
        </div>
    </div>
</div>

<!-- ==== FEATURES ==== -->
<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="service-card">
                <div class="service-icon">
                </div>
                <h3>Recherche sur mesure</h3>
                <p>Nous identifions les propriétés qui correspondent parfaitement à vos critères et aspirations.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card">
                <div class="service-icon">
                </div>
                <h3>Négociation experte</h3>
                <p>Notre expertise garantit les meilleures conditions d'achat ou de vente pour votre bien.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card">
                <div class="service-icon">
                </div>
                <h3>Accompagnement complet</h3>
                <p>De la recherche à la remise des clés, nous gérons tous les aspects de votre transaction.</p>
            </div>
        </div>
    </div>
</div>

<!-- ==== ABOUT ==== -->
<div class="container py-5" id="about" name="about">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <img src="images/apropos1.jpg" 
                 alt="Agence immobilière" class="img-fluid rounded shadow">
        </div>
        <div class="col-lg-6">
            <h2 class="section-title">Notre agence</h2>
            <p class="lead">Fondée en 2005, notre agence s'est spécialisée dans l'immobilier haut de gamme et les propriétés d'exception.</p>
            <p>Nous combinons expertise du marché local et approche personnalisée pour offrir à nos clients un service premium. Notre réseau étendu et notre connaissance approfondie des tendances du marché nous permettent d'identifier les opportunités les plus intéressantes.</p>
            <ul class="list-unstyled">
                <li class="mb-2"><i class="me-2"></i> Plus de 500 transactions réalisées</li>
                <li class="mb-2"><i class="me-2"></i> Réseau international de clients</li>
                <li class="mb-2"><i class="me-2"></i> Équipe d'experts multilingues</li>
                <li class="mb-2"><i class="me-2"></i> Partenariats avec les meilleures banques</li>
            </ul>
        </div>
    </div>
</div>

<!-- ==== SERVICES ==== -->
<div class="container py-5" id="services" name="services">
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="section-title">Nos services</h2>
            <p class="lead mb-5">Une gamme complète de services haut de gamme pour répondre à tous vos besoins immobiliers</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="service-card">
                <div class="service-icon">
                </div>
                <h4>Achat/Location immobilier</h4>
                <p>Accompagnement personnalisé dans votre recherche de bien, négociation et formalités administratives.</p>
                <a href="#" class="text-uppercase small font-weight-bold">En savoir plus <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card">
                <div class="service-icon">
                </div>
                <h4>Vente exclusive</h4>
                <p>Estimation précise, marketing sur mesure et stratégie de commercialisation optimale pour votre bien.</p>
                <a href="#" class="text-uppercase small font-weight-bold">En savoir plus <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card">
                <div class="service-icon">
                </div>
                <h4>Investissement</h4>
                <p>Conseils en investissement locatif et identification des meilleures opportunités du marché.</p>
                <a href="#" class="text-uppercase small font-weight-bold">En savoir plus <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- ==== PROPERTIES ==== -->
<div class="container py-5" id="properties" name="properties">
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="section-title">Nos propriétés sélectionnées</h2>
            <p class="lead mb-5">Découvrez notre sélection de biens d'exception</p>
        </div>
    </div>
    <div class="row">
        <!-- Property 1 -->
        <div class="col-lg-4 col-md-6">
            <div class="property-card">
                <img src="images/select3.jpeg" 
                     alt="Studio moderne" class="property-img">
                <div class="property-info">
                    <span class="badge bg-success mb-2">À vendre</span>
                    <h4>Studio de l'heure</h4>
                    <p class="text-muted"><i class="fas fa-map-marker-alt text-primary me-1"></i> Deido, Douala</p>
                    <div class="property-features">
                        <li><i class="fas fa-bed"></i> 5 chambres</li>
                        <li><i class="fas fa-bath"></i> 3 salles de bain</li>
                        <li><i class="fas fa-ruler-combined"></i> 220 m²</li>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="property-price">1 250 000 €</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Détails</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Property 2 -->
        <div class="col-lg-4 col-md-6">
            <div class="property-card">
                <img src="images/select1.jpeg" 
                     alt="Appartement luxe" class="property-img">
                <div class="property-info">
                    <span class="badge bg-primary mb-2">Location</span>
                    <h4>Appartement de prestige</h4>
                    <p class="text-muted"><i class="fas fa-map-marker-alt text-primary me-1"></i>Chapelle Nsimeyong, Yaoundé</p>
                    <div class="property-features">
                        <li><i class="fas fa-bed"></i> 3 chambres</li>
                        <li><i class="fas fa-bath"></i> 2 salles de bain</li>
                        <li><i class="fas fa-ruler-combined"></i> 120 m²</li>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="property-price">45 500 F/mois</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Détails</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Property 3 -->
        <div class="col-lg-4 col-md-6">
            <div class="property-card">
                <img src="images/select2.jpg" class="property-img">
                <div class="property-info">
                    <span class="badge bg-success mb-2">À vendre</span>
                    <h4>Villa tres paisible a Bastos</h4>
                    <p class="text-muted"><i class="fas fa-map-marker-alt text-primary me-1"></i> Bastos Yaoundé</p>
                    <div class="property-features">
                        <li><i class="fas fa-bed"></i> 12 chambres</li>
                        <li><i class="fas fa-bath"></i> 8 salles de bain</li>
                        <li><i class="fas fa-ruler-combined"></i> 1 800 m²</li>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="property-price">3 750 000 €</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Détails</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="client.php" class="btn btn-primary">Voir toutes nos propriétés</a>
    </div>
</div>

<!-- ==== TEAM ==== -->
<div class="container py-5" id="team" name="team">
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="section-title">Notre équipe d'experts</h2>
            <p class="lead mb-5">Des professionnels dévoués à votre service</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="agent-card">
                <img src="images/directeur.jpg"class="agent-img">
                <h4>Cabrel Teumou</h4>
                <p class="text-muted">Directeur de l'agence</p>
                <div class="social-icons">
                    <a href="#"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="agent-card">
                <img src="images/propri3.jpg"  class="agent-img">
                <h4>Merveille Tchoutchi</h4>
                <p class="text-muted">Charge de la clientelle</p>
                <div class="social-icons">
                    <a href="#"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="agent-card">
                <img src="images/Foe.jpg" class="agent-img">
                <h4>Foe Ndi</h4>
                <p class="text-muted">Agent immobilier</p>
                <div class="social-icons">
                    <a href="#"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="agent-card">
                <img src="images/propri 2.jpg" class="agent-img">
                <h4>Eric Tiani</h4>
                <p class="text-muted">Conseiller immobilier</p>
                <div class="social-icons">
                    <a href="#"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ==== CONTACT ==== -->
<div class="container py-5" id="contact" name="contact">
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="section-title">Contactez-nous</h2>
            <p class="lead mb-5">Prêts à concrétiser votre projet immobilier ? Parlons-en</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <form>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" placeholder="Votre nom" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="email" class="form-control" placeholder="Votre email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Sujet">
                </div>
                <div class="mb-3">
                    <textarea class="form-control" rows="5" placeholder="Votre message" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer le message</button>
            </form>
        </div>
        <div class="col-lg-6">
            <div class="bg-light p-4 h-100">
                <h4 class="mb-4">Nos coordonnées</h4>
                <ul class="list-unstyled">
                    <li class="mb-3"><i class="fas fa-map-marker-alt text-primary me-2"></i> Nkolmesseng, Yaounde (Centre-Cameroun)</li>
                    <li class="mb-3"><i class="fas fa-phone-alt text-primary me-2"></i> +237 678 89 09 67</li>
                    <li class="mb-3"><i class="fas fa-envelope text-primary me-2"></i> agence@immobilierelite.com</li>
                    <li class="mb-3"><i class="fas fa-clock text-primary me-2"></i> Lundi-Vendredi: 9h-19h | Samedi: 10h-16h</li>
                </ul>
                <div class="mt-4">
                    <h5>Suivez-nous</h5>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<footer>
            <div class="col-md-6 text-center text-md-start">
                <p class="small text-white-50 mb-0">© 2025 ImmobilierÉlite. Groupe 4 B1-C.</p>
            </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script>
    // Navbar scroll effect
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.navbar').addClass('scrolled