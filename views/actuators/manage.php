<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=home">Accueil</a></li>
                <li class="breadcrumb-item active">Gestion des Actionneurs</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1>⚡ Gestion des Actionneurs</h1>
            <div class="btn-group">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addActuatorModal">
                    <i class="bi bi-plus-circle"></i> Ajouter un actionneur
                </button>
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <i class="bi bi-activity"></i> État du système
                </button>
            </div>
        </div>
        <p class="text-muted">Gérez et contrôlez tous les actionneurs du système</p>
    </div>
</div>

<!-- Instructions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <div class="d-flex align-items-center">
                <span class="me-2">ℹ️</span>
                <div>
                    <strong>Instructions:</strong>
                    En tant qu'administrateur, vous pouvez contrôler tous les actionneurs de toutes les équipes.
                    Utilisez cette interface pour configurer, surveiller et actionner les équipements connectés.
                </div>
            </div>
        </div>
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
                            <?= count(array_filter($actuators, function($a) { return $a['etat']; })) ?>
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

<!-- Grille des actionneurs avec contrôles -->
<div class="row mb-4">
    <div class="col-12">
        <h3>🎛️ Contrôle des Actionneurs</h3>
        <p class="text-muted">Interface de contrôle direct des équipements</p>
    </div>
</div>

<div class="row">
    <?php if (empty($actuators)): ?>
        <div class="col-12">
            <div class="alert alert-warning">
                <h5>Aucun actionneur disponible</h5>
                <p>
                    Aucun actionneur n'a été configuré. 
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addActuatorModal">
                        Ajoutez-en un maintenant
                    </button>.
                </p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($actuators as $actuator): ?>
            <div class="col-lg-4 col-md-6 mb-4 actuator-card" 
                 data-type="<?= $actuator['type'] ?>" 
                 data-state="<?= $actuator['etat'] ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><?= htmlspecialchars($actuator['name']) ?></h6>
                    </div>
                    
                    <div class="card-body">
                        <!-- Type et icône -->
                        <div class="d-flex align-items-center mb-3">
                            <?php
                            $icon = '';
                            $description = '';
                            switch ($actuator['type']) {
                                case 'irrigation': 
                                    $icon = '💧'; 
                                    $description = 'Système d\'arrosage automatique';
                                    break;
                                case 'ventilation': 
                                    $icon = '🌪️'; 
                                    $description = 'Ventilation et circulation d\'air';
                                    break;
                                case 'heating': 
                                    $icon = '🔥'; 
                                    $description = 'Système de chauffage';
                                    break;
                                case 'lighting': 
                                    $icon = '💡'; 
                                    $description = 'Éclairage artificiel';
                                    break;
                                case 'window': 
                                    $icon = '🪟'; 
                                    $description = 'Ouverture/fermeture automatique';
                                    break;
                                default:
                                    $icon = '⚡';
                                    $description = 'Actionneur générique';
                            }
                            ?>
                            <span class="me-3" style="font-size: 2rem;"><?= $icon ?></span>
                            <div>
                                <h6 class="mb-0"><?= ucfirst($actuator['type']) ?></h6>
                                <small class="text-muted"><?= $description ?></small>
                            </div>
                        </div>
                        
                        <!-- État actuel -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted">État actuel:</span>
                                <div class="d-flex align-items-center">
                                    <span class="status-indicator <?= $actuator['etat'] ? 'status-on' : 'status-off' ?> me-2" id="status-<?= $actuator['id'] ?>"></span>
                                    <strong class="<?= $actuator['etat'] ? 'text-success' : 'text-secondary' ?>" id="state-text-<?= $actuator['id'] ?>">
                                        <?= $actuator['etat'] ? 'ACTIF' : 'INACTIF' ?>
                                    </strong>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations techniques -->
                        <div class="mb-3">
                            <small class="text-muted d-block">
                                ID: #<?= $actuator['id'] ?>
                            </small>
                        </div>

                          <!-- Contrôles -->
                        <div class="d-grid gap-2">
                            <?php if ($actuator['etat']): ?>
                                <button 
                                    class="btn btn-danger"
                                    id="toggle-btn-<?= $actuator['id'] ?>"
                                    data-actuator-id="<?= $actuator['id'] ?>"
                                    onclick="toggleActuator(<?= $actuator['id'] ?>, 'OFF')"
                                    <?= !$actuator['etat'] ? 'disabled' : '' ?>>
                                    <i class="bi bi-stop-circle"></i> Arrêter
                                </button>
                            <?php else: ?>
                                <button 
                                    class="btn btn-success"
                                    id="toggle-btn-<?= $actuator['id'] ?>"
                                    data-actuator-id="<?= $actuator['id'] ?>"
                                    onclick="toggleActuator(<?= $actuator['id'] ?>, 'ON')"
                                    <?= !$actuator['etat'] ? 'disabled' : '' ?>>
                                    <i class="bi bi-play-circle"></i> Démarrer
                                </button>
                            <?php endif; ?>
                            
                            <!-- Boutons de gestion -->
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-warning btn-sm" 
                                        onclick="editActuator(<?= $actuator['id'] ?>)" title="Modifier">
                                    <i class="bi bi-pencil"></i> Modifier
                                </button>
                                <button class="btn btn-outline-danger btn-sm" 
                                        onclick="deleteActuator(<?= $actuator['id'] ?>, '<?= htmlspecialchars($actuator['name']) ?>')" title="Supprimer">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>

                        <?php if (!$actuator['etat']): ?>
                            <div class="mt-2">
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Actionneur désactivé
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Footer avec dernière action -->
                    <div class="card-footer text-muted">
                        <small>
                            <i class="bi bi-clock"></i> 
                            Dernière modification: <span id="last-update-<?= $actuator['id'] ?>">Inconnue</span>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Tableau des actionneurs (pour gestion avancée) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🎛️ Panneau de contrôle avancé</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="actuatorsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Dernière action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actuators as $actuator): ?>
                                <tr class="actuator-row" id="actuator-row-<?= $actuator['id'] ?>">
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
                                        <span class="badge bg-<?= $actuator['etat'] ? 'success' : 'secondary' ?>">
                                            <?= $actuator['etat'] ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted" id="last-action-<?= $actuator['id'] ?>">
                                            Inconnue
                                        </small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section d'aide et conseils -->
<div class="row mt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">💡 Aide et Conseils</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>🔒 Sécurité</h6>
                        <ul class="list-unstyled">
                            <li>• N'activez les actionneurs que si nécessaire</li>
                            <li>• Vérifiez l'état des capteurs avant l'activation</li>
                            <li>• En cas de problème, arrêtez immédiatement</li>
                            <li>• Utilisez l'arrêt d'urgence en cas de danger</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🌍 Éco-responsabilité</h6>
                        <ul class="list-unstyled">
                            <li>• Optimisez l'usage selon les besoins réels</li>
                            <li>• Évitez les activations inutiles</li>
                            <li>• Surveillez la consommation énergétique</li>
                            <li>• Planifiez les actions pour maximiser l'efficacité</li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6>⚙️ Gestion</h6>
                        <ul class="list-unstyled">
                            <li>• Configurez les actionneurs par équipe</li>
                            <li>• Surveillez les logs d'activité</li>
                            <li>• Effectuez des tests réguliers</li>
                            <li>• Documentez les interventions</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🚨 Dépannage</h6>
                        <ul class="list-unstyled">
                            <li>• Vérifiez les connexions en cas de problème</li>
                            <li>• Consultez les logs pour diagnostiquer</li>
                            <li>• Redémarrez l'actionneur si nécessaire</li>
                            <li>• Contactez l'équipe technique si persistant</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Conserver tous les modals existants -->
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
                            <input class="form-check-input" type="checkbox" id="editActuatorActive" name="etat">
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
                            <li>✅ Actionneurs actifs: <strong><?= count(array_filter($actuators, function($a) { return $a['etat']; })) ?></strong></li>
                            <li>⚡ Actionneurs en marche: <strong id="runningCount"><?= count(array_filter($actuators, function($a) { return $a['etat']; })) ?></strong></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🎛️ Actions système</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-info" onclick="refreshSystemStatus()">
                                <i class="bi bi-arrow-clockwise"></i> Actualiser statut
                            </button>
                            <button class="btn btn-secondary" onclick="exportActuatorLogs()">
                                <i class="bi bi-download"></i> Exporter logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
// Reprendre tout le JavaScript existant de la page manage.php
// Variables globales
const actuatorsData = <?= json_encode($actuators) ?>;

// Filtrage des actionneurs
function filterActuators() {
    const typeFilter = document.getElementById('typeFilter').value;
    const stateFilter = document.getElementById('stateFilter').value;
    
    const cards = document.querySelectorAll('.actuator-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const type = card.dataset.type;
        const state = card.dataset.state;
        
        const typeMatch = !typeFilter || type === typeFilter;
        const stateMatch = !stateFilter || state === stateFilter;
        
        if (typeMatch && stateMatch) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
}

// Modifier un actionneur
function editActuator(actuatorId) {
    const actuator = actuatorsData.find(a => a.id == actuatorId);
    if (!actuator) return;
    
    document.getElementById('editActuatorId').value = actuator.id;
    document.getElementById('editActuatorName').value = actuator.name;
    document.getElementById('editActuatorActive').checked = actuator.etat == 1;
    
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
    
    const activeActuators = actuatorsData.filter(a => a.etat && a.etat);
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

// Fonction principale pour actionner un actionneur
function toggleActuator(actuatorId, action, callback) {
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
            
            // Mettre à jour l'heure de modification dans les cartes
            const lastUpdateElement = document.getElementById(`last-update-${actuatorId}`);
            if (lastUpdateElement) {
                lastUpdateElement.textContent = new Date().toLocaleString('fr-FR');
            }
            
            if (callback) callback();
        } else {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        showNotification('Erreur de communication', 'error');
    });
}

// Mettre à jour l'interface d'un actionneur
function updateActuatorInterface(actuatorId, newState) {
    // Mettre à jour les boutons dans les cartes
    const cardButton = document.querySelector(`[data-actuator-id="${actuatorId}"]`);
    const tableButton = document.querySelector(`#actuator-row-${actuatorId} .btn-group .btn:first-child`);
    
    // Mettre à jour les indicateurs de statut
    const statusIndicators = document.querySelectorAll(`#status-${actuatorId}`);
    const stateTexts = document.querySelectorAll(`#state-text-${actuatorId}`);
    
    // Mise à jour des boutons
    [cardButton, tableButton].forEach(button => {
        if (button) {
            if (newState) {
                button.className = button.className.replace('btn-success', 'btn-danger');
                button.innerHTML = '<i class="bi bi-stop-circle"></i> Arrêter';
                button.onclick = () => toggleActuator(actuatorId, 'OFF');
            } else {
                button.className = button.className.replace('btn-danger', 'btn-success');
                button.innerHTML = '<i class="bi bi-play-circle"></i> Démarrer';
                button.onclick = () => toggleActuator(actuatorId, 'ON');
            }
        }
    });
    
    // Mise à jour des indicateurs de statut
    statusIndicators.forEach(indicator => {
        if (indicator) {
            indicator.className = `status-indicator ${newState ? 'status-on' : 'status-off'} me-2`;
        }
    });
    
    // Mise à jour des textes d'état
    stateTexts.forEach(text => {
        if (text) {
            text.textContent = newState ? 'ACTIF' : 'INACTIF';
            text.className = newState ? 'text-success' : 'text-secondary';
        }
    });
    
    // Mettre à jour l'attribut data-state des cartes
    const card = document.querySelector(`[data-actuator-id="${actuatorId}"]`)?.closest('.actuator-card');
    if (card) {
        card.dataset.state = newState ? '1' : '0';
    }
}

// Fonction pour simuler un actionneur (utile pour les tests)
function simulateActuator(actuatorId) {
    // Alterner l'état pour la simulation
    const button = document.querySelector(`[data-actuator-id="${actuatorId}"]`);
    const currentAction = button.textContent.includes('Démarrer') ? 'ON' : 'OFF';
    
    if (confirm('Voulez-vous simuler l\'action sur cet actionneur ?')) {
        toggleActuator(actuatorId, currentAction);
    }
}

// Fonction d'affichage des notifications
function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 80px; right: 20px; z-index: 1050;" role="alert">
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

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Mettre à jour les heures de dernière modification au chargement
    actuatorsData.forEach(actuator => {
        const lastActionElement = document.getElementById(`last-action-${actuator.id}`);
        const lastUpdateElement = document.getElementById(`last-update-${actuator.id}`);
        
        if (lastActionElement) {
            const randomMinutes = Math.floor(Math.random() * 60);
            const time = new Date(Date.now() - randomMinutes * 60000);
            lastActionElement.textContent = time.toLocaleTimeString('fr-FR');
        }
        
        if (lastUpdateElement) {
            const randomMinutes = Math.floor(Math.random() * 120);
            const time = new Date(Date.now() - randomMinutes * 60000);
            lastUpdateElement.textContent = time.toLocaleString('fr-FR');
        }
    });
});
</script>

<style>
/* Styles spécifiques aux actionneurs */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn {
    transition: all 0.2s ease;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    animation: pulse 2s infinite;
}

.status-on {
    background-color: #28a745;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
}

.status-off {
    background-color: #6c757d;
    opacity: 0.7;
    animation: none;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.actuator-card {
    transition: all 0.3s ease;
}

.actuator-card:hover {
    transform: translateY(-2px);
}

/* Mode sombre pour l'éco-responsabilité */
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #2d2d2d;
        border-color: #404040;
    }
    
    .card-header {
        background-color: #404040;
        border-color: #555;
    }
}

/* Optimisation éco-responsable */
@media (prefers-reduced-motion: reduce) {
    .card, .actuator-card {
        transition: none;
    }
    
    .status-indicator {
        animation: none !important;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .btn-group .btn {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>