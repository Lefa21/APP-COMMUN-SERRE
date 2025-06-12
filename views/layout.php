<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Serres Connectées</title>
    
    <!-- CSS Bootstrap pour un design responsable -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- CSS personnalisé éco-responsable -->
    <style>
        /* Design minimaliste pour réduire l'impact environnemental */
        :root {
            --primary-color: #2d5a27;
            --secondary-color: #5d8a54;
            --accent-color: #8bc34a;
            --text-color: #333;
            --bg-color: #f8f9fa;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand, .navbar-nav .nav-link {
            color: white !important;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .sensor-card {
            transition: transform 0.2s ease;
        }
        
        .sensor-card:hover {
            transform: translateY(-2px);
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-on { background-color: #28a745; }
        .status-off { background-color: #dc3545; }
        
        .eco-badge {
            background-color: var(--accent-color);
            color: white;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 12px;
        }
        
        /* Optimisation pour réduire la consommation */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Mode sombre automatique pour économiser l'énergie */
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #1a1a1a;
                --text-color: #e0e0e0;
            }
            
            body {
                background-color: var(--bg-color);
                color: var(--text-color);
            }
            
            .card {
                background-color: #2d2d2d;
                color: var(--text-color);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                🌱 Serres Connectées
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>?controller=home">
                                <i class="bi bi-house"></i> Tableau de Bord
                            </a>
                        </li>
                        </li>
                        
                        <!-- ADMINISTRATION : Seulement pour les admins -->
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear"></i> Administration
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=actuator&action=manage">
                                        <i class="bi bi-lightning"></i> Gérer Actionneurs
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=sensor&action=manage">
                                        <i class="bi bi-thermometer-half"></i> Gérer Capteurs
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=admin&action=users">
                                        <i class="bi bi-people"></i> Gestion Utilisateurs
                                    </a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                                <span class="eco-badge">Éco</span>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                    <span class="badge bg-warning text-dark ms-1">Admin</span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=profile">
                                    <i class="bi bi-person"></i> Mon Profil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=auth&action=logout">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>?controller=auth&action=login">
                                <i class="bi bi-box-arrow-in-right"></i> Connexion
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>?controller=auth&action=register">
                                <i class="bi bi-person-plus"></i> Inscription
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="container mt-4">
        <?php echo $content; ?>
    </main>

    <!-- Footer éco-responsable -->
    <footer class="mt-5 py-4 bg-light text-center">
        <div class="container">
            <p class="mb-2">🌍 Site éco-conçu - Consommation optimisée</p>
            <small class="text-muted">
                Projet Serres Connectées - <?= date('Y') ?> - 
                <a href="<?= BASE_URL ?>?controller=api&action=health" class="text-decoration-none">Statut système</a>
            </small>
        </div>
    </footer>

    <!-- JavaScript Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript personnalisé optimisé -->
    <script>
        // Optimisation: Chargement différé des scripts non critiques
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh des données toutes les 30 secondes (seulement sur la page d'accueil)
            if (window.location.search.includes('controller=home') || window.location.search === '') {
                setInterval(function() {
                    if (!document.hidden) {
                        refreshSensorData();
                    }
                }, 30000);
            }
        });
        
        // Fonction pour rafraîchir les données des capteurs via AJAX
        function refreshSensorData() {
            fetch('<?= BASE_URL ?>?controller=api&action=sensors')
                .then(response => response.json())
                .then(data => {
                    updateSensorCards(data);
                })
                .catch(error => console.log('Refresh silencieux échoué'));
        }
        
        // Fonction pour actionner les actionneurs
        function toggleActuator(actuatorId, action) {
            const formData = new FormData();
            formData.append('actuator_id', actuatorId);
            formData.append('action', action);
            
            fetch('<?= BASE_URL ?>?controller=actuator&action=toggle', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateActuatorButton(actuatorId, data.newState);
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.error, 'error');
                }
            })
            .catch(error => {
                showNotification('Erreur de communication', 'error');
            });
        }
        
        // Fonction pour afficher les notifications
        function showNotification(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                     style="top: 80px; right: 20px; z-index: 1050;" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', alertHtml);
            
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) alert.remove();
            }, 3000);
        }
        
        // Fonction pour mettre à jour l'état des boutons d'actionneurs
        function updateActuatorButton(actuatorId, newState) {
            const button = document.querySelector(`[data-actuator-id="${actuatorId}"]`);
            if (!button) return;
            
            const statusIndicator = button.closest('.card').querySelector('.status-indicator');
            
            if (newState) {
                button.textContent = 'Arrêter';
                button.className = button.className.replace('btn-success', 'btn-danger');
                button.onclick = () => toggleActuator(actuatorId, 'OFF');
                if (statusIndicator) statusIndicator.className = 'status-indicator status-on me-2';
            } else {
                button.textContent = 'Démarrer';
                button.className = button.className.replace('btn-danger', 'btn-success');
                button.onclick = () => toggleActuator(actuatorId, 'ON');
                if (statusIndicator) statusIndicator.className = 'status-indicator status-off me-2';
            }
        }
        
        // Fonction pour mettre à jour les cartes de capteurs (appelée par refreshSensorData)
        function updateSensorCards(sensorsData) {
            if (!sensorsData.success || !sensorsData.data) return;
            
            sensorsData.data.forEach(sensor => {
                const card = document.querySelector(`[data-sensor-id="${sensor.id}"]`);
                if (card) {
                    const valueElement = card.querySelector('.sensor-value');
                    const timestampElement = card.querySelector('.sensor-timestamp');
                    
                    if (valueElement && sensor.value !== null) {
                        valueElement.textContent = `${sensor.value} ${sensor.unit}`;
                    }
                    
                    if (timestampElement && sensor.timestamp) {
                        const time = new Date(sensor.timestamp).toLocaleTimeString('fr-FR', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        timestampElement.textContent = time;
                    }
                }
            });
        }
    </script>
</body>
</html>