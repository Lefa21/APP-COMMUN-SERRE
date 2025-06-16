<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serres Connectées Éco-responsables - Cultivons l'avenir</title>
    
    <!-- CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2d5a27;
            --secondary-color: #5d8a54;
            --accent-color: #8bc34a;
            --text-color: #333;
            --bg-color: #f8f9fa;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, rgba(45, 90, 39, 0.9), rgba(93, 138, 84, 0.8)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><defs><linearGradient id="greenhouse" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:%23e8f5e8"/><stop offset="100%" style="stop-color:%23c8e6c9"/></linearGradient></defs><rect width="1200" height="600" fill="url(%23greenhouse)"/><g transform="translate(100,150)"><path d="M0,300 Q150,50 300,100 Q450,150 600,100 Q750,50 900,100 Q1050,150 1200,100 L1200,300 Z" fill="%23a5d6a7" opacity="0.7"/><g transform="translate(200,100)"><rect x="0" y="150" width="200" height="100" fill="%23ffffff" opacity="0.9" rx="10"/><path d="M0,150 Q100,100 200,150" fill="%23e8f5e8" stroke="%23388e3c" stroke-width="2"/><circle cx="50" cy="180" r="8" fill="%2366bb6a"/><circle cx="100" cy="190" r="6" fill="%2381c784"/><circle cx="150" cy="175" r="7" fill="%234caf50"/></g></g></svg>') center/cover;
            color: white;
            min-height: 85vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 40%, rgba(139, 195, 74, 0.1) 60%);
            animation: floating 6s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            opacity: 0.95;
        }
        
        .cta-button {
            background: linear-gradient(45deg, var(--accent-color), #7cb342);
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 195, 74, 0.4);
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 195, 74, 0.6);
        }
        
        /* Features Section */
        .features-section {
            padding: 50px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e8 100%);
        }
        
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border: none;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 0.8rem;
            display: block;
        }
        
        .stats-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px 0;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            display: block;
        }
        
        .eco-badge {
            background: linear-gradient(45deg, var(--accent-color), #7cb342);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .navbar {
            background: rgba(45, 90, 39, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            background: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand, .navbar-nav .nav-link {
            color: white !important;
        }
        
        .footer {
            background: var(--primary-color);
            color: white;
            padding: 40px 0 20px;
        }
        
        /* About Section Styles */
        .about-section {
            padding: 50px 0;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }
        
        .tech-badge {
            background: var(--secondary-color);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.9rem;
            margin: 5px;
            display: inline-block;
        }
        
        .project-stat {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, var(--accent-color), #7cb342);
            color: white;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        
        .project-stat h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        /* Advantages Section */
        .advantages-section {
            padding: 50px 0;
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
        }
        
        .advantage-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .advantage-item:hover {
            transform: translateX(5px);
        }
        
        .advantage-icon {
            font-size: 2.5rem;
            margin-right: 15px;
            color: var(--primary-color);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }
            .hero-subtitle {
                font-size: 1rem;
            }
            .advantage-item {
                flex-direction: column;
                text-align: center;
            }
            .advantage-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
        
        /* Optimisation éco-responsable */
        @media (prefers-reduced-motion: reduce) {
            .hero-section::before,
            .pulse-animation {
                animation: none;
            }
            .feature-card,
            .cta-button,
            .advantage-item {
                transition: none;
            }
        }
        
        /* Mode sombre automatique */
        @media (prefers-color-scheme: dark) {
            .feature-card,
            .advantage-item {
                background: #2d2d2d;
                color: #e0e0e0;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#home">
                🌱 Serres Connectées
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#fonctionnalites">Fonctionnalités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#avantages">Avantages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#a-propos">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light ms-2 px-3" href="?controller=auth&action=login">
                            <i class="bi bi-box-arrow-in-right"></i> Connexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <div class="mb-3">
                        <span class="eco-badge">🌍 100% Éco-responsable</span>
                    </div>
                    <h1 class="hero-title">
                        Cultivons l'avenir avec des serres intelligentes
                    </h1>
                    <p class="hero-subtitle">
                        Gérez vos cultures de manière durable grâce à nos capteurs IoT avancés, 
                        notre intelligence artificielle et notre approche respectueuse de l'environnement.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="?controller=auth&action=register" class="btn cta-button text-white">
                            <i class="bi bi-rocket-takeoff"></i> Commencer maintenant
                        </a>
                        <a href="#fonctionnalites" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-play-circle"></i> Découvrir
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="position-relative">
                        <div style="font-size: 8rem; opacity: 0.9; text-shadow: 0 0 20px rgba(255,255,255,0.3);" class="pulse-animation">
                            🏡
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                            <div class="d-flex gap-3">
                                <div class="text-center" style="animation: floating 3s ease-in-out infinite;">
                                    <div style="font-size: 1.5rem;">🌡️</div>
                                    <small>24.5°C</small>
                                </div>
                                <div class="text-center" style="animation: floating 3s ease-in-out infinite 0.5s;">
                                    <div style="font-size: 1.5rem;">💧</div>
                                    <small>65%</small>
                                </div>
                                <div class="text-center" style="animation: floating 3s ease-in-out infinite 1s;">
                                    <div style="font-size: 1.5rem;">🌱</div>
                                    <small>Optimal</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques dynamiques -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <span class="stat-number">95%</span>
                    <p>d'économie d'eau</p>
                </div>
                <div class="col-md-3 mb-4">
                    <span class="stat-number">40%</span>
                    <p>de rendement en plus</p>
                </div>
                <div class="col-md-3 mb-4">
                    <span class="stat-number">24/7</span>
                    <p>surveillance automatique</p>
                </div>
                <div class="col-md-3 mb-4">
                    <span class="stat-number">150+</span>
                    <p>capteurs actifs</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Fonctionnalités -->
    <section id="fonctionnalites" class="features-section">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-3">Fonctionnalités Avancées</h2>
                    <p class="lead text-muted">Découvrez comment notre technologie révolutionne l'agriculture</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body">
                            <span class="feature-icon">🌡️</span>
                            <h5 class="card-title">Monitoring Intelligent</h5>
                            <p class="card-text">
                                Surveillance en temps réel de la température, humidité, pH et luminosité 
                                avec des alertes automatiques.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body">
                            <span class="feature-icon">⚡</span>
                            <h5 class="card-title">Automatisation Complète</h5>
                            <p class="card-text">
                                Contrôlez l'arrosage, la ventilation, l'éclairage et le chauffage 
                                automatiquement selon vos paramètres.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body">
                            <span class="feature-icon">📊</span>
                            <h5 class="card-title">Analytics Avancés</h5>
                            <p class="card-text">
                                Tableaux de bord interactifs, rapports détaillés et prédictions 
                                basées sur l'IA pour optimiser vos cultures.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body">
                            <span class="feature-icon">🌍</span>
                            <h5 class="card-title">Éco-responsable</h5>
                            <p class="card-text">
                                Réduction drastique de la consommation d'eau et d'énergie, 
                                culture sans pesticides, impact carbone minimal.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body">
                            <span class="feature-icon">📱</span>
                            <h5 class="card-title">Accès Mobile</h5>
                            <p class="card-text">
                                Gérez vos serres depuis n'importe où avec notre interface 
                                responsive et nos notifications push.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body">
                            <span class="feature-icon">👥</span>
                            <h5 class="card-title">Travail Collaboratif</h5>
                            <p class="card-text">
                                Gérez plusieurs équipes, partagez les données et coordonnez 
                                vos efforts avec des outils collaboratifs intégrés.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Avantages -->
    <section id="avantages" class="advantages-section">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-3">Pourquoi choisir nos serres connectées ?</h2>
                    <p class="lead text-muted">Les avantages qui font la différence</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="advantage-item">
                        <div class="advantage-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">Installation rapide</h5>
                            <p class="mb-0">Configuration complète en moins de 30 minutes avec notre guide interactif et notre équipe support dédiée.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="advantage-item">
                        <div class="advantage-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">Sécurité maximale</h5>
                            <p class="mb-0">Données chiffrées, authentification multi-facteurs, accès sécurisé et sauvegarde automatique dans le cloud.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="advantage-item">
                        <div class="advantage-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">Support expert 24/7</h5>
                            <p class="mb-0">Équipe d'ingénieurs agronomes et informaticiens disponible pour vous accompagner dans tous vos projets.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="advantage-item">
                        <div class="advantage-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">ROI prouvé</h5>
                            <p class="mb-0">Retour sur investissement moyen de 300% la première année grâce aux économies et à l'augmentation des rendements.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="advantage-item">
                        <div class="advantage-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">Certifications éco</h5>
                            <p class="mb-0">Conforme aux normes ISO 14001, certifié agriculture biologique et compatible avec les labels environnementaux.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="advantage-item">
                        <div class="advantage-icon">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">Technologie de pointe</h5>
                            <p class="mb-0">Intelligence artificielle, IoT de dernière génération, machine learning pour des prédictions ultra-précises.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- À propos -->
    <section id="a-propos" class="about-section">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-3">À propos du projet</h2>
                    <p class="lead text-muted">Un projet pédagogique innovant de l'ISEP</p>
                </div>
            </div>
            
            <div class="row align-items-center mb-4">
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-3">Projet Serres Connectées</h3>
                    <p class="mb-3">
                        Notre système de gestion de serres connectées est né d'un projet commun de l'École d'ingénieurs du numérique ISEP. 
                        Développé par 30 étudiants répartis en 5 équipes, ce projet combine expertise technique et approche éco-responsable.
                    </p>
                    
                    <div class="mb-3">
                        <h5 class="fw-bold mb-2">Technologies utilisées</h5>
                        <div>
                            <span class="tech-badge">PHP 8</span>
                            <span class="tech-badge">MySQL</span>
                            <span class="tech-badge">Bootstrap 5</span>
                            <span class="tech-badge">Arduino/IoT</span>
                            <span class="tech-badge">JavaScript</span>
                            <span class="tech-badge">API REST</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="fw-bold mb-2">Fonctionnalités éco-responsables</h5>
                        <ul class="list-unstyled">
                            <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Optimisation énergétique automatique</li>
                            <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Réduction de 95% de la consommation d'eau</li>
                            <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Agriculture 100% sans pesticides</li>
                            <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Interface web éco-conçue</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="project-stat">
                                <h3>30</h3>
                                <p class="mb-0">Étudiants</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="project-stat">
                                <h3>5</h3>
                                <p class="mb-0">Équipes</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="project-stat">
                                <h3>2</h3>
                                <p class="mb-0">Semaines</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="project-stat">
                                <h3>100%</h3>
                                <p class="mb-0">Éco-responsable</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 text-center mb-3">
                    <div class="mb-2">
                        <i class="bi bi-lightbulb" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                    </div>
                    <h5 class="fw-bold">Innovation</h5>
                    <p class="mb-0">Solutions technologiques de pointe pour une agriculture durable et intelligente.</p>
                </div>
                <div class="col-md-4 text-center mb-3">
                    <div class="mb-2">
                        <i class="bi bi-people" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                    </div>
                    <h5 class="fw-bold">Collaboration</h5>
                    <p class="mb-0">Travail d'équipe multidisciplinaire alliant informatique et électronique.</p>
                </div>
                <div class="col-md-4 text-center mb-3">
                    <div class="mb-2">
                        <i class="bi bi-leaf" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                    </div>
                    <h5 class="fw-bold">Durabilité</h5>
                    <p class="mb-0">Approche éco-responsable pour un impact environnemental minimal.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Témoignages -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-3">Ce que disent nos équipes</h2>
                    <p class="lead text-muted">Retours d'expérience de nos utilisateurs</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </div>
                            <p class="card-text">"Nos tomates ont un rendement 35% supérieur depuis l'utilisation du système. L'interface est intuitive et les données nous aident à prendre les bonnes décisions."</p>
                            <footer class="blockquote-footer">
                                <cite title="Source Title">Équipe Alpha</cite>
                            </footer>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </div>
                            <p class="card-text">"L'automatisation nous fait économiser 3h de travail par jour. Le système d'alertes nous permet de réagir rapidement aux changements climatiques."</p>
                            <footer class="blockquote-footer">
                                <cite title="Source Title">Équipe Beta</cite>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="py-4">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="h3 fw-bold mb-3">Contactez-nous</h2>
                    <p class="text-muted">Une question ? Nous sommes là pour vous aider</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <form>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contactName" class="form-label">Nom complet</label>
                                        <input type="text" class="form-control" id="contactName" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contactEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="contactEmail" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="contactSubject" class="form-label">Sujet</label>
                                    <select class="form-select" id="contactSubject">
                                        <option selected>Choisir un sujet...</option>
                                        <option value="demo">Demande de démonstration</option>
                                        <option value="support">Support technique</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="contactMessage" class="form-label">Message</label>
                                    <textarea class="form-control" id="contactMessage" rows="4" required></textarea>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-lg" style="background: var(--primary-color); color: white;">
                                        <i class="bi bi-send"></i> Envoyer le message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-4 text-center mb-3">
                    <div class="mb-2">
                        <i class="bi bi-geo-alt" style="font-size: 2rem; color: var(--primary-color);"></i>
                    </div>
                    <h5 class="fw-bold">Adresse</h5>
                    <p class="text-muted small">ISEP - École d'ingénieurs du numérique<br>28 Rue Notre Dame des Champs<br>75006 Paris</p>
                </div>
                <div class="col-md-4 text-center mb-3">
                    <div class="mb-2">
                        <i class="bi bi-envelope" style="font-size: 2rem; color: var(--primary-color);"></i>
                    </div>
                    <h5 class="fw-bold">Email</h5>
                    <p class="text-muted small">contact@serres-connectees.fr<br>support@serres-connectees.fr</p>
                </div>
                <div class="col-md-4 text-center mb-3">
                    <div class="mb-2">
                        <i class="bi bi-telephone" style="font-size: 2rem; color: var(--primary-color);"></i>
                    </div>
                    <h5 class="fw-bold">Téléphone</h5>
                    <p class="text-muted small">+33 1 49 54 52 00<br>Support 24/7</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="py-4" style="background: linear-gradient(135deg, var(--accent-color), #7cb342);">
        <div class="container text-center text-white">
            <h2 class="h3 fw-bold mb-3">Prêt à révolutionner votre agriculture ?</h2>
            <p class="mb-3">
                Rejoignez les 5 équipes qui ont déjà fait le choix de l'innovation durable.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="?controller=auth&action=register" class="btn btn-light btn-lg">
                    <i class="bi bi-person-plus"></i> S'inscrire gratuitement
                </a>
                <a href="?controller=auth&action=login" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                </a>
            </div>
        </div>
    </section>

    <!-- JavaScript Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript personnalisé -->
    <script>
        // Smooth scrolling pour les liens d'ancrage
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Form submission handler
        document.querySelector('#contact form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simuler l'envoi du formulaire
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Envoi en cours...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Message envoyé !';
                submitBtn.className = 'btn btn-success btn-lg';
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.className = 'btn btn-lg';
                    submitBtn.style.background = 'var(--primary-color)';
                    submitBtn.style.color = 'white';
                    submitBtn.disabled = false;
                    this.reset();
                }, 2000);
            }, 1500);
        });

        // Animation observer pour les éléments
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer les cartes de fonctionnalités
        document.querySelectorAll('.feature-card, .advantage-item').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Counter animation pour les statistiques
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target + (element.textContent.includes('%') ? '%' : '');
                    clearInterval(timer);
                } else {
                    element.textContent = Math.ceil(current) + (element.textContent.includes('%') ? '%' : '');
                }
            }, 20);
        }

        // Observer pour les statistiques
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumber = entry.target.querySelector('.stat-number');
                    const value = parseInt(statNumber.textContent);
                    if (!isNaN(value)) {
                        animateCounter(statNumber, value);
                    }
                    statsObserver.unobserve(entry.target);
                }
            });
        });

        document.querySelector('.stats-section').addEventListener('DOMContentLoaded', function() {
            statsObserver.observe(this);
        });
    </script>
</body>
</html>