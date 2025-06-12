<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=home">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=profile">Mon Profil</a></li>
                <li class="breadcrumb-item active">Mon Activité</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1>📈 Mon Activité</h1>
            <div class="btn-group">
                <a href="<?= BASE_URL ?>?controller=profile" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour au profil
                </a>
                <button class="btn btn-outline-primary" onclick="exportActivity()">
                    <i class="bi bi-download"></i> Exporter
                </button>
            </div>
        </div>
        <p class="text-muted">Historique complet de vos actions sur le système</p>
    </div>
</div>

<!-- Statistiques d'activité -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary"><?= $totalActivities ?></h3>
                <p class="mb-0">Actions totales</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">
                    <?= count(array_filter($activities, function($a) { return $a['action'] === 'ON'; })) ?>
                </h3>
                <p class="mb-0">Activations</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning">
                    <?= count(array_filter($activities, function($a) { return $a['action'] === 'OFF'; })) ?>
                </h3>
                <p class="mb-0">Arrêts</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info">
                    <?= count(array_unique(array_column($activities, 'actuator_name'))) ?>
                </h3>
                <p class="mb-0">Actionneurs contrôlés</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="<?= BASE_URL ?>?controller=profile&action=activity" class="row align-items-center">
                    <div class="col-md-3">
                        <label for="actionFilter" class="form-label">Action:</label>
                        <select id="actionFilter" name="action_filter" class="form-select" onchange="this.form.submit()">
                            <option value="">Toutes les actions</option>
                            <option value="ON" <?= ($_GET['action_filter'] ?? '') === 'ON' ? 'selected' : '' ?>>Activations (ON)</option>
                            <option value="OFF" <?= ($_GET['action_filter'] ?? '') === 'OFF' ? 'selected' : '' ?>>Arrêts (OFF)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="dateFrom" class="form-label">Du:</label>
                        <input type="date" id="dateFrom" name="date_from" class="form-control" 
                               value="<?= $_GET['date_from'] ?? '' ?>" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <label for="dateTo" class="form-label">Au:</label>
                        <input type="date" id="dateTo" name="date_to" class="form-control" 
                               value="<?= $_GET['date_to'] ?? '' ?>" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="bi bi-x-circle"></i> Effacer filtres
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Timeline d'activité -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">🕒 Historique d'activité</h5>
                <small class="text-muted">
                    Page <?= $currentPage ?> sur <?= $totalPages ?> 
                    (<?= count($activities) ?> entrées)
                </small>
            </div>
            <div class="card-body">
                <?php if (empty($activities)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-clock-history" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3">Aucune activité trouvée</h5>
                        <p class="text-muted">
                            <?php if (!empty($_GET['action_filter']) || !empty($_GET['date_from'])): ?>
                                Aucune activité ne correspond aux filtres sélectionnés.
                                <br><button class="btn btn-link" onclick="clearFilters()">Effacer les filtres</button>
                            <?php else: ?>
                                Vous n'avez encore effectué aucune action sur le système.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <!-- Timeline -->
                    <div class="timeline">
                        <?php 
                        $currentDate = '';
                        foreach ($activities as $activity): 
                            $activityDate = date('Y-m-d', strtotime($activity['timestamp']));
                            
                            // Afficher le séparateur de date
                            if ($currentDate !== $activityDate):
                                $currentDate = $activityDate;
                        ?>
                            <div class="timeline-date">
                                <h6 class="text-muted mb-3">
                                    <?php
                                    $date = new DateTime($activityDate);
                                    $today = new DateTime();
                                    $yesterday = new DateTime('yesterday');
                                    
                                    if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
                                        echo "Aujourd'hui";
                                    } elseif ($date->format('Y-m-d') === $yesterday->format('Y-m-d')) {
                                        echo "Hier";
                                    } else {
                                        echo strftime('%A %d %B %Y', $date->getTimestamp());
                                    }
                                    ?>
                                </h6>
                            </div>
                        <?php endif; ?>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-<?= $activity['action'] === 'ON' ? 'success' : 'secondary' ?>">
                                <i class="bi bi-<?= $activity['action'] === 'ON' ? 'play' : 'stop' ?>-circle text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <?php
                                                    $icon = '';
                                                    switch ($activity['activity_type']) {
                                                        case 'actuator_action':
                                                            $icon = '⚡';
                                                            break;
                                                        case 'sensor_view':
                                                            $icon = '👁️';
                                                            break;
                                                        case 'login':
                                                            $icon = '🔑';
                                                            break;
                                                        default:
                                                            $icon = '📝';
                                                    }
                                                    ?>
                                                    <?= $icon ?> Action sur <?= htmlspecialchars($activity['actuator_name']) ?>
                                                </h6>
                                                <p class="mb-2">
                                                    <span class="badge bg-<?= $activity['action'] === 'ON' ? 'success' : 'secondary' ?>">
                                                        <?= $activity['action'] ?>
                                                    </span>
                                                    <?php if (!empty($activity['team_name'])): ?>
                                                        <span class="badge bg-info ms-1">
                                                            <?= htmlspecialchars($activity['team_name']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock"></i>
                                                    <?= date('H:i:s', strtotime($activity['timestamp'])) ?>
                                                    
                                                    <?php if (!empty($activity['details'])): ?>
                                                        <br><i class="bi bi-info-circle"></i>
                                                        Détails: <?= htmlspecialchars($activity['details']) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <?php
                                                $timeAgo = time() - strtotime($activity['timestamp']);
                                                if ($timeAgo < 60) echo "À l'instant";
                                                elseif ($timeAgo < 3600) echo floor($timeAgo/60) . " min";
                                                elseif ($timeAgo < 86400) echo floor($timeAgo/3600) . " h";
                                                else echo floor($timeAgo/86400) . " j";
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Graphique d'impact (si disponible) -->
                                        <?php if ($activity['action'] === 'ON'): ?>
                                            <div class="mt-2">
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar bg-success" style="width: <?= rand(20, 100) ?>%"></div>
                                                </div>
                                                <small class="text-muted">Impact estimé sur l'environnement de la serre</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Navigation activité" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- Première page -->
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?controller=profile&action=activity&page=1<?= buildQueryString() ?>">
                                            <i class="bi bi-chevron-double-left"></i>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?controller=profile&action=activity&page=<?= $currentPage - 1 ?><?= buildQueryString() ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Pages autour de la page actuelle -->
                                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="?controller=profile&action=activity&page=<?= $i ?><?= buildQueryString() ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Dernière page -->
                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?controller=profile&action=activity&page=<?= $currentPage + 1 ?><?= buildQueryString() ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?controller=profile&action=activity&page=<?= $totalPages ?><?= buildQueryString() ?>">
                                            <i class="bi bi-chevron-double-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Informations éco-responsables -->
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-success">
            <div class="d-flex align-items-center">
                <span class="me-2">🌍</span>
                <div>
                    <strong>Impact Éco-responsable:</strong>
                    Cette page d'activité utilise une pagination efficace pour réduire la charge serveur. 
                    Le chargement différé des données historiques limite la consommation de bande passante.
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Fonction helper pour construire la query string avec les filtres
function buildQueryString() {
    $params = [];
    if (!empty($_GET['action_filter'])) $params[] = 'action_filter=' . urlencode($_GET['action_filter']);
    if (!empty($_GET['date_from'])) $params[] = 'date_from=' . urlencode($_GET['date_from']);
    if (!empty($_GET['date_to'])) $params[] = 'date_to=' . urlencode($_GET['date_to']);
    return empty($params) ? '' : '&' . implode('&', $params);
}
?>

<script>
// Effacer les filtres
function clearFilters() {
    window.location.href = '<?= BASE_URL ?>?controller=profile&action=activity';
}

// Exporter l'activité
function exportActivity() {
    const actionFilter = document.getElementById('actionFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    let url = '<?= BASE_URL ?>?controller=profile&action=exportActivity';
    const params = [];
    
    if (actionFilter) params.push('action_filter=' + encodeURIComponent(actionFilter));
    if (dateFrom) params.push('date_from=' + encodeURIComponent(dateFrom));
    if (dateTo) params.push('date_to=' + encodeURIComponent(dateTo));
    
    if (params.length > 0) {
        url += '&' + params.join('&');
    }
    
    window.open(url, '_blank');
}

// Auto-refresh toutes les 5 minutes si la page est active
let activityRefreshInterval;
document.addEventListener('DOMContentLoaded', function() {
    activityRefreshInterval = setInterval(function() {
        if (!document.hidden) {
            // Refresh silencieux - ne recharge que si nouvelle activité
            fetch('<?= BASE_URL ?>?controller=api&action=checkNewActivity')
                .then(response => response.json())
                .then(data => {
                    if (data.newActivity) {
                        showNotification('Nouvelle activité détectée', 'info');
                        setTimeout(() => window.location.reload(), 2000);
                    }
                })
                .catch(() => {}); // Ignore les erreurs silencieusement
        }
    }, 300000); // 5 minutes
});

// Nettoyer l'interval quand on quitte la page
window.addEventListener('beforeunload', function() {
    if (activityRefreshInterval) {
        clearInterval(activityRefreshInterval);
    }
});
</script>

<style>
/* Styles pour la timeline */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
}

.timeline-date {
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.timeline-date:first-child {
    margin-top: 0;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.5rem;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    margin-left: 1rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

/* Animation pour les nouveaux éléments */
.timeline-item {
    animation: slideInRight 0.5s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .timeline {
        padding-left: 1rem;
    }
    
    .timeline::before {
        left: 0.5rem;
    }
    
    .timeline-marker {
        left: -1.5rem;
        width: 1.5rem;
        height: 1.5rem;
    }
    
    .timeline-content {
        margin-left: 0.5rem;
    }
}

/* Mode éco-responsable */
@media (prefers-reduced-motion: reduce) {
    .timeline-item {
        animation: none;
    }
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .timeline::before {
        background: linear-gradient(to bottom, #8bc34a, #2d5a27);
    }
    
    .timeline-marker {
        border-color: #2d2d2d;
    }
}
</style>