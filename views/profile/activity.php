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
                <div class="btn-group">
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> Exporter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=profile&action=exportActivity&period=<?= $period ?>&format=csv">
                            <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                        </a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=profile&action=exportActivity&period=<?= $period ?>&format=json">
                            <i class="bi bi-file-earmark-code"></i> JSON
                        </a></li>
                    </ul>
                </div>
                <button class="btn btn-outline-warning" onclick="clearOldActivity()">
                    <i class="bi bi-trash"></i> Nettoyer
                </button>
            </div>
        </div>
        <p class="text-muted">Historique de vos actions sur les actionneurs du système</p>
    </div>
</div>

<!-- Statistiques de l'activité -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary"><?= $stats['total_actions'] ?></h3>
                <p class="mb-0">Actions Total</p>
                <small class="text-muted">Sur <?= $period ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success"><?= $stats['on_actions'] ?></h3>
                <p class="mb-0">Activations</p>
                <small class="text-muted">Actions ON</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-secondary"><?= $stats['off_actions'] ?></h3>
                <p class="mb-0">Arrêts</p>
                <small class="text-muted">Actions OFF</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="h3 text-info">
                    <?= $stats['total_actions'] > 0 ? round(($stats['on_actions'] / $stats['total_actions']) * 100, 1) : 0 ?>%
                </div>
                <p class="mb-0">Ratio ON/OFF</p>
                <small class="text-muted">Taux d'activation</small>
            </div>
        </div>
    </div>
</div>

<!-- Informations détaillées -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">📊 Statistiques détaillées</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <strong>Actionneur le plus utilisé :</strong><br>
                        <span class="text-muted">
                            <?= $stats['most_used_actuator'] ?? 'Aucune donnée' ?>
                        </span>
                    </div>
                    <div class="col-12 mb-3">
                        <strong>Jour le plus actif :</strong><br>
                        <span class="text-muted">
                            <?= $stats['busiest_day'] ?? 'Aucune donnée' ?>
                        </span>
                    </div>
                    <div class="col-12">
                        <strong>Heure préférée :</strong><br>
                        <span class="text-muted">
                            <?= $stats['busiest_hour'] ?? 'Aucune donnée' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">🔧 Actions de maintenance</h6>
            </div>
            <div class="card-body">
                <p class="text-muted">Gérez votre historique d'activité :</p>
                <div class="d-grid gap-2">
                    <form method="POST" action="<?= BASE_URL ?>?controller=profile&action=update" class="d-inline">
                        <input type="hidden" name="action" value="clear_activity">
                        <button type="submit" class="btn btn-outline-warning w-100" 
                                onclick="return confirm('Supprimer l\'activité de plus de 90 jours ?')">
                            <i class="bi bi-trash"></i> Nettoyer l'historique ancien
                        </button>
                    </form>
                    <button class="btn btn-outline-info w-100" onclick="refreshActivity()">
                        <i class="bi bi-arrow-clockwise"></i> Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label for="periodFilter" class="form-label">Période :</label>
                        <select id="periodFilter" class="form-select" onchange="changePeriod()">
                            <option value="7d" <?= $period === '7d' ? 'selected' : '' ?>>7 derniers jours</option>
                            <option value="30d" <?= $period === '30d' ? 'selected' : '' ?>>30 derniers jours</option>
                            <option value="90d" <?= $period === '90d' ? 'selected' : '' ?>>90 derniers jours</option>
                            <option value="all" <?= $period === 'all' ? 'selected' : '' ?>>Tout l'historique</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="actionFilter" class="form-label">Action :</label>
                        <select id="actionFilter" class="form-select" onchange="changeFilter()">
                            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Toutes les actions</option>
                            <option value="on" <?= $filter === 'on' ? 'selected' : '' ?>>Activations (ON)</option>
                            <option value="off" <?= $filter === 'off' ? 'selected' : '' ?>>Arrêts (OFF)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Actions :</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleAutoRefresh()">
                                <i class="bi bi-arrow-clockwise"></i> <span id="autoRefreshText">Auto-refresh OFF</span>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="toggleCompactView()">
                                <i class="bi bi-list"></i> Vue compacte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste de l'activité -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 Historique des actions</h5>
                <small class="text-muted">
                    <?= $totalActivities ?> action(s) au total
                    <?php if ($filter !== 'all' || $period !== 'all'): ?>
                        - Filtré
                    <?php endif; ?>
                </small>
            </div>
            <div class="card-body">
                <?php if (empty($activities)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-activity" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3">Aucune activité</h5>
                        <p class="text-muted">
                            <?php if ($filter !== 'all' || $period !== 'all'): ?>
                                Aucune activité trouvée pour ces critères.
                                <br><a href="<?= BASE_URL ?>?controller=profile&action=activity" class="btn btn-outline-primary btn-sm mt-2">
                                    Voir toute l'activité
                                </a>
                            <?php else: ?>
                                Vous n'avez encore effectué aucune action sur les actionneurs.
                                <br><a href="<?= BASE_URL ?>?controller=actuator" class="btn btn-outline-primary btn-sm mt-2">
                                    Découvrir les actionneurs
                                </a>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="activityTable">
                            <thead>
                                <tr>
                                    <th>Date/Heure</th>
                                    <th>Action</th>
                                    <th>Actionneur</th>
                                    <th>Type</th>
                                    <th>Équipe</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activities as $activity): ?>
                                    <tr data-action="<?= strtolower($activity['action']) ?>">
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span><?= date('d/m/Y', strtotime($activity['timestamp'])) ?></span>
                                                <small class="text-muted"><?= date('H:i:s', strtotime($activity['timestamp'])) ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $activity['action'] === 'ON' ? 'success' : 'secondary' ?> fs-6">
                                                <?php if ($activity['action'] === 'ON'): ?>
                                                    <i class="bi bi-play-circle"></i> ON
                                                <?php else: ?>
                                                    <i class="bi bi-stop-circle"></i> OFF
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php
                                                $icon = '';
                                                switch ($activity['actuator_type']) {
                                                    case 'irrigation': $icon = '💧'; break;
                                                    case 'ventilation': $icon = '🌪️'; break;
                                                    case 'heating': $icon = '🔥'; break;
                                                    case 'lighting': $icon = '💡'; break;
                                                    case 'window': $icon = '🪟'; break;
                                                    default: $icon = '⚡';
                                                }
                                                ?>
                                                <span class="me-2"><?= $icon ?></span>
                                                <div>
                                                    <div class="fw-medium"><?= htmlspecialchars($activity['actuator_name']) ?></div>
                                                    <small class="text-muted"><?= ucfirst($activity['actuator_type']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= ucfirst($activity['actuator_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= htmlspecialchars($activity['team_name'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="status-indicator <?= $activity['action'] === 'ON' ? 'status-on' : 'status-off' ?> me-2"></span>
                                                <span class="text-<?= $activity['action'] === 'ON' ? 'success' : 'muted' ?>">
                                                    <?= $activity['action'] === 'ON' ? 'Activé' : 'Arrêté' ?>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Navigation de l'activité" class="mt-4">
                            <ul class="pagination pagination-sm justify-content-center">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?controller=profile&action=activity&page=<?= $currentPage - 1 ?>&filter=<?= $filter ?>&period=<?= $period ?>">
                                            <i class="bi bi-chevron-left"></i> Précédent
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php 
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                ?>
                                
                                <?php if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?controller=profile&action=activity&page=1&filter=<?= $filter ?>&period=<?= $period ?>">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="?controller=profile&action=activity&page=<?= $i ?>&filter=<?= $filter ?>&period=<?= $period ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?controller=profile&action=activity&page=<?= $totalPages ?>&filter=<?= $filter ?>&period=<?= $period ?>"><?= $totalPages ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?controller=profile&action=activity&page=<?= $currentPage + 1 ?>&filter=<?= $filter ?>&period=<?= $period ?>">
                                            Suivant <i class="bi bi-chevron-right"></i>
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
                    <strong>Impact Éco-responsable :</strong>
                    Cette page d'activité vous aide à optimiser votre utilisation des actionneurs. 
                    En analysant vos habitudes, vous pouvez réduire la consommation énergétique inutile.
                    <br><small class="mt-1">
                        Astuce : Un ratio ON/OFF équilibré indique une utilisation efficace des équipements.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let autoRefreshInterval = null;
let compactView = false;

// Fonctions de filtrage
function changePeriod() {
    const period = document.getElementById('periodFilter').value;
    const filter = document.getElementById('actionFilter').value;
    window.location.href = `?controller=profile&action=activity&period=${period}&filter=${filter}`;
}

function changeFilter() {
    const period = document.getElementById('periodFilter').value;
    const filter = document.getElementById('actionFilter').value;
    window.location.href = `?controller=profile&action=activity&period=${period}&filter=${filter}`;
}

// Auto-refresh
function toggleAutoRefresh() {
    const button = document.getElementById('autoRefreshText');
    
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
        button.textContent = 'Auto-refresh OFF';
    } else {
        autoRefreshInterval = setInterval(refreshActivity, 30000); // 30 secondes
        button.textContent = 'Auto-refresh ON';
    }
}

function refreshActivity() {
    window.location.reload();
}

// Vue compacte
function toggleCompactView() {
    compactView = !compactView;
    const table = document.getElementById('activityTable');
    
    if (compactView) {
        table.classList.add('table-sm');
        // Masquer certaines colonnes en mode compact
        const cells = table.querySelectorAll('th:nth-child(4), td:nth-child(4), th:nth-child(5), td:nth-child(5)');
        cells.forEach(cell => cell.style.display = 'none');
    } else {
        table.classList.remove('table-sm');
        const cells = table.querySelectorAll('th:nth-child(4), td:nth-child(4), th:nth-child(5), td:nth-child(5)');
        cells.forEach(cell => cell.style.display = '');
    }
}

// Nettoyage de l'activité
function clearOldActivity() {
    if (confirm('Êtes-vous sûr de vouloir supprimer l\'activité de plus de 90 jours ?\n\nCette action est irréversible.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>?controller=profile&action=update';
        form.innerHTML = '<input type="hidden" name="action" value="clear_activity">';
        document.body.appendChild(form);
        form.submit();
    }
}

// Animation des lignes
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#activityTable tbody tr');
    rows.forEach((row, index) => {
        row.style.animationDelay = (index * 0.05) + 's';
        row.classList.add('fade-in');
    });
});
</script>

<style>
/* Styles pour l'activité */
.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.status-on {
    background-color: #28a745;
    animation: pulse 2s infinite;
}

.status-off {
    background-color: #6c757d;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.fade-in {
    animation: fadeIn 0.5s ease-in-out forwards;
    opacity: 0;
    transform: translateY(10px);
}

@keyframes fadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.badge.fs-6 {
    font-size: 0.875rem !important;
}

/* Mode éco-responsable */
@media (prefers-reduced-motion: reduce) {
    .fade-in {
        animation: none;
        opacity: 1;
        transform: none;
    }
    
    .status-on {
        animation: none;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .d-flex.flex-column span {
        font-size: 0.875rem;
    }
}
</style>