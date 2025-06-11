<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=home">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=actuator">Actionneurs</a></li>
                <li class="breadcrumb-item active">Gestion</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1>⚙️ Gestion des Actionneurs</h1>
            <div class="btn-group">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addActuatorModal">
                    <i class="bi bi-plus-circle"></i> Ajouter un actionneur
                </button>
                <button class="btn btn-warning" onclick="toggleAllActuators('OFF')">
                    <i class="bi bi-stop-circle"></i> Tout arrêter
                </button>
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <i class="bi bi-activity"></i> État du système
                </button>
            </div>
        </div>
        <p class="text-muted">Gérez et contrôlez tous les actionneurs du système</p>
    </div>
</div>

<!-- Messages de feedback -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['success_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<!-- Statistiques et filtres -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="teamFilter" class="form-label">Filtrer par équipe:</label>
                        <select id="teamFilter" class="form-select" onchange="filterActuators()">
                            <option value="">Toutes les équipes</option>
                            <?php foreach ($teams as $team): ?>
                                <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="typeFilter" class="form-label">Filtrer par type:</label>
                        <select id="typeFilter" class="form-select" onchange="filterActuators()">
                            <option value="">Tous les types</option>
                            <option value="irrigation">Irrigation</option>
                            <option value="ventilation">Ventilation</option>
                            <option value="heating">Chauffage</option>
                            <option value="lighting">Éclairage</option>
                            <option value="window">Ouverture</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="stateFilter" class="form-label">Filtrer par état:</label>
                        <select id="stateFilter" class="form-select" onchange="filterActuators()">
                            <option value="">Tous</option>
                            <option value="1">Actifs (ON)</option>
                            <option value="0">Inactifs (OFF)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-success" id="activeCount">
                            <?= count(array_filter($actuators, function($a) { return $a['current_state']; })) ?>
                        </h4>
                        <small>Actifs</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-primary"><?= count($actuators) ?></h4>
                        <small>Total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des actionneurs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🎛️ Panneau de contrôle</h5>
            </div>
            <div class="card-body">
                <?php if (empty($actuators)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-gear" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3">Aucun actionneur configuré</h5>
                        <p class="text-muted">Commencez par ajouter votre premier actionneur</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addActuatorModal">
                            <i class="bi bi-plus-circle"></i> Ajouter un actionneur
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="actuatorsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Équipe</th>
                                    <th>État</th>
                                    <th>Statut</th>
                                    <th>Dernière action</th>
                                    <th>Contrôles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($actuators as $actuator): ?>
                                    <tr data-team="<?= $actuator['team_id'] ?>" 
                                        data-type="<?= $actuator['type'] ?>" 
                                        data-state="<?= $actuator['current_state'] ?>"
                                        class="actuator-row" id="actuator-row-<?= $actuator['id'] ?>">
                                        <td><strong>#<?= $actuator['id'] ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php
                                                $icon = '';
                                                switch ($actuator['type']) {
                                                    case 'irrigation': $icon = '💧'; break;
                                                    case 'ventilation': $icon = '🌪️'; break;
                                                    case 'heating': $icon = '🔥'; break;
                                                    case 'lighting': $icon = '💡'; break;
                                                    case 'window': $icon = '🪟'; break;
                                                    default: $icon = '⚡';
                                                }
                                                ?>
                                                <span class="me-2"><?= $icon ?></span>
                                                <?= htmlspecialchars($actuator['name']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= ucfirst($actuator['type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($actuator['team_name'] ?? 'Non assignée') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="status-indicator <?= $actuator['current_state'] ? 'status-on' : 'status-off' ?> me-2" 
                                                      id="status-<?= $actuator['id'] ?>"></span>
                                                <strong class="<?= $actuator['current_state'] ? 'text-success' : 'text-secondary' ?>" 
                                                        id="state-text-<?= $actuator['id'] ?>">
                                                    <?= $actuator['current_state'] ? 'ON' : 'OFF' ?>
                                                </strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $actuator['is_active'] ? 'success' : 'secondary' ?>">
                                                <?= $actuator['is_active'] ? 'Actif' : 'Inactif' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted" id="last-action-<?= $actuator['id'] ?>">
                                                Inconnue
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($actuator['is_active']): ?>
                                                    <button class="btn <?= $actuator['current_state'] ? 'btn-danger' : 'btn-success' ?>" 
                                                            id="toggle-btn-<?= $actuator['id'] ?>"
                                                            onclick="toggleActuator(<?= $actuator['id'] ?>, '<?= $actuator['current_state'] ? 'OFF' : 'ON' ?>')"
                                                            data-actuator-id="<?= $actuator['id'] ?>">
                                                        <i class="bi bi-<?= $actuator['current_state'] ? 'stop' : 'play' ?>-circle"></i>
                                                        <?= $actuator['current_state'] ? 'Arrêter' : 'Démarrer' ?>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="bi bi-ban"></i> Désactivé
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button class="btn btn-outline-warning btn-sm" 
                                                        onclick="editActuator(<?= $actuator['id'] ?>)" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" 
                                                        onclick="deleteActuator(<?= $actuator['id'] ?>, '<?= htmlspecialchars($actuator['name']) ?>')" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter un actionneur -->
<div class="modal fade" id="addActuatorModal" tabindex="-1" aria-labelledby="addActuatorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addActuatorModalLabel">➕ Ajouter un nouvel actionneur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="management_action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="actuatorName" class="form-label">Nom de l'actionneur *</label>
                        <input type="text" class="form-control" id="actuatorName" name="name" required 
                               placeholder="Ex: Arrosage Automatique A">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="actuatorType" class="form-label">Type *</label>
                                <select class="form-select" id="actuatorType" name="type" required>
                                    <option value="">Choisir un type</option>
                                    <option value="irrigation">💧 Irrigation</option>
                                    <option value="ventilation">🌪️ Ventilation</option>
                                    <option value="heating">🔥 Chauffage</option>
                                    <option value="lighting">💡 Éclairage</option>
                                    <option value="window">🪟 Ouverture</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="actuatorTeam" class="form-label">Équipe *</label>
                                <select class="form-select" id="actuatorTeam" name="team_id" required>
                                    <option value="">Choisir une équipe</option>
                                    <?php foreach ($teams as $team): ?>
                                        <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            L'actionneur sera créé en état inactif par défaut. Vous pourrez l'activer après création.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier un actionneur -->
<div class="modal fade" id="editActuatorModal" tabindex="-1" aria-labelledby="editActuatorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editActuatorModalLabel">✏️ Modifier l'actionneur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editActuatorForm">
                <input type="hidden" name="management_action" value="edit">
                <input type="hidden" name="actuator_id" id="editActuatorId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editActuatorName" class="form-label">Nom de l'actionneur *</label>
                        <input type="text" class="form-control" id="editActuatorName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editActuatorActive" name="is_active">
                            <label class="form-check-label" for="editActuatorActive">
                                Actionneur actif
                            </label>
                            <small class="form-text text-muted">
                                Décochez pour désactiver temporairement l'actionneur
                            </small>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <small>
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Attention:</strong> Désactiver un actionneur l'arrêtera automatiquement et empêchera tout contrôle.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal État du système -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">📊 État du système</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>📈 Statistiques</h6>
                        <ul class="list-unstyled">
                            <li>🔧 Actionneurs configurés: <strong><?= count($actuators) ?></strong></li>
                            <li>✅ Actionneurs actifs: <strong><?= count(array_filter($actuators, function($a) { return $a['is_active']; })) ?></strong></li>
                            <li>⚡ Actionneurs en marche: <strong id="runningCount"><?= count(array_filter($actuators, function($a) { return $a['current_state']; })) ?></strong></li>
                            <li>👥 Équipes: <strong><?= count($teams) ?></strong></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🎛️ Actions système</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-warning" onclick="toggleAllActuators('OFF')">
                                <i class="bi bi-stop-circle"></i> Arrêter tout
                            </button>
                            <button class="btn btn-info" onclick="refreshSystemStatus()">
                                <i class="bi bi-arrow-clockwise"></i> Actualiser statut
                            </button>
                            <button class="btn btn-secondary" onclick="exportActuatorLogs()">
                                <i class="bi bi-download"></i> Exporter logs
                            </button>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h6>📋 Répartition par équipe</h6>
                <div class="row">
                    <?php
                    $actuatorsByTeam = [];
                    foreach ($actuators as $actuator) {
                        $teamName = $actuator['team_name'] ?? 'Non assignée';
                        if (!isset($actuatorsByTeam[$teamName])) {
                            $actuatorsByTeam[$teamName] = ['total' => 0, 'active' => 0];
                        }
                        $actuatorsByTeam[$teamName]['total']++;
                        if ($actuator['current_state']) {
                            $actuatorsByTeam[$teamName]['active']++;
                        }
                    }
                    ?>
                    <?php foreach ($actuatorsByTeam as $teamName => $stats): ?>
                        <div class="col-md-4 mb-2">
                            <div class="card card-body text-center">
                                <h6><?= htmlspecialchars($teamName) ?></h6>
                                <p class="mb-0">
                                    <span class="text-success"><?= $stats['active'] ?></span> / 
                                    <span class="text-primary"><?= $stats['total'] ?></span>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
// Données des actionneurs pour JavaScript
const actuatorsData = <?= json_encode($actuators) ?>;

// Filtrage des actionneurs
function filterActuators() {
    const teamFilter = document.getElementById('teamFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const stateFilter = document.getElementById('stateFilter').value;
    
    const rows = document.querySelectorAll('.actuator-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const team = row.dataset.team;
        const type = row.dataset.type;
        const state = row.dataset.state;
        
        const teamMatch = !teamFilter || team === teamFilter;
        const typeMatch = !typeFilter || type === typeFilter;
        const stateMatch = !stateFilter || state === stateFilter;
        
        if (teamMatch && typeMatch && stateMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
}

// Modifier un actionneur
function editActuator(actuatorId) {
    const actuator = actuatorsData.find(a => a.id == actuatorId);
    if (!actuator) return;
    
    document.getElementById('editActuatorId').value = actuator.id;
    document.getElementById('editActuatorName').value = actuator.name;
    document.getElementById('editActuatorActive').checked = actuator.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editActuatorModal')).show();
}

// Supprimer un actionneur
function deleteActuator(actuatorId, actuatorName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'actionneur "${actuatorName}" ?\n\nCette action supprimera également tous les logs associés et ne peut pas être annulée.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="management_action" value="delete">
            <input type="hidden" name="actuator_id" value="${actuatorId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Arrêter tous les actionneurs
function toggleAllActuators(action) {
    if (action === 'OFF' && !confirm('Êtes-vous sûr de vouloir arrêter tous les actionneurs ?')) {
        return;
    }
    
    const activeActuators = actuatorsData.filter(a => a.is_active && a.current_state);
    let completed = 0;
    
    activeActuators.forEach(actuator => {
        toggleActuator(actuator.id, action, () => {
            completed++;
            if (completed === activeActuators.length) {
                showNotification(`${activeActuators.length} actionneurs arrêtés`, 'success');
                updateActiveCount();
            }
        });
    });
}

// Mettre à jour le compteur d'actionneurs actifs
function updateActiveCount() {
    const activeCount = document.querySelectorAll('.status-on').length;
    document.getElementById('activeCount').textContent = activeCount;
    document.getElementById('runningCount').textContent = activeCount;
}

// Rafraîchir le statut du système
function refreshSystemStatus() {
    window.location.reload();
}

// Exporter les logs des actionneurs
function exportActuatorLogs() {
    window.open('<?= BASE_URL ?>?controller=actuator&action=exportLogs', '_blank');
}

// Override de la fonction toggleActuator pour mettre à jour l'interface
const originalToggleActuator = window.toggleActuator;
window.toggleActuator = function(actuatorId, action, callback) {
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
            updateActuatorInterface(actuatorId, data.newState);
            showNotification(data.message, 'success');
            updateActiveCount();
            
            // Mettre à jour la dernière action
            const lastActionElement = document.getElementById(`last-action-${actuatorId}`);
            if (lastActionElement) {
                lastActionElement.textContent = new Date().toLocaleTimeString('fr-FR');
            }
            
            if (callback) callback();
        } else {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        showNotification('Erreur de communication', 'error');
    });
};

// Mettre à jour l'interface d'un actionneur
function updateActuatorInterface(actuatorId, newState) {
    const button = document.getElementById(`toggle-btn-${actuatorId}`);
    const status = document.getElementById(`status-${actuatorId}`);
    const stateText = document.getElementById(`state-text-${actuatorId}`);
    const row = document.getElementById(`actuator-row-${actuatorId}`);
    
    if (button) {
        if (newState) {
            button.className = 'btn btn-danger';
            button.innerHTML = '<i class="bi bi-stop-circle"></i> Arrêter';
            button.onclick = () => toggleActuator(actuatorId, 'OFF');
        } else {
            button.className = 'btn btn-success';
            button.innerHTML = '<i class="bi bi-play-circle"></i> Démarrer';
            button.onclick = () => toggleActuator(actuatorId, 'ON');
        }
    }
    
    if (status) {
        status.className = `status-indicator ${newState ? 'status-on' : 'status-off'} me-2`;
    }
    
    if (stateText) {
        stateText.textContent = newState ? 'ON' : 'OFF';
        stateText.className = newState ? 'text-success' : 'text-secondary';
    }
    
    if (row) {
        row.dataset.state = newState ? '1' : '0';
    }
}

// Mettre à jour les dernières actions au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Simuler des heures d'action récentes
    actuatorsData.forEach(actuator => {
        const lastActionElement = document.getElementById(`last-action-${actuator.id}`);
        if (lastActionElement) {
            const randomMinutes = Math.floor(Math.random() * 60);
            const time = new Date(Date.now() - randomMinutes * 60000);
            lastActionElement.textContent = time.toLocaleTimeString('fr-FR');
        }
    });
});
</script>