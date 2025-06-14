<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>⚡ Gestion des Actionneurs</h1>
            <?php if ($isAdmin): ?>
                <a href="<?= BASE_URL ?>?controller=actuator&action=manage" class="btn btn-primary">
                    <i class="bi bi-gear"></i> Administration
                </a>
            <?php endif; ?>
        </div>
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
                    <?php if ($isAdmin): ?>
                        En tant qu'administrateur, vous pouvez contrôler tous les actionneurs de toutes les équipes.
                    <?php else: ?>
                        Vous pouvez contrôler uniquement les actionneurs de votre équipe. 
                        Contactez un administrateur pour modifier la configuration.
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grille des actionneurs -->
<div class="row">
    <?php if (empty($actuators)): ?>
        <div class="col-12">
            <div class="alert alert-warning">
                <h5>Aucun actionneur disponible</h5>
                <p>
                    <?php if ($isAdmin): ?>
                        Aucun actionneur n'a été configuré. 
                        <a href="<?= BASE_URL ?>?controller=actuator&action=manage" class="alert-link">
                            Ajoutez-en un depuis l'interface d'administration
                        </a>.
                    <?php else: ?>
                        Aucun actionneur n'est disponible pour votre équipe. 
                        Contactez un administrateur pour en configurer.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($actuators as $actuator): ?>
            <div class="col-lg-4 col-md-6 mb-4">
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
                                    <span class="status-indicator <?= $actuator['current_state'] ? 'status-on' : 'status-off' ?> me-2"></span>
                                    <strong class="<?= $actuator['current_state'] ? 'text-success' : 'text-secondary' ?>">
                                        <?= $actuator['current_state'] ? 'ACTIF' : 'INACTIF' ?>
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
                            <?php if ($isAdmin): ?>
                                <?php if ($actuator['current_state']): ?>
                                    <button 
                                        class="btn btn-danger"
                                        data-actuator-id="<?= $actuator['id'] ?>"
                                        onclick="toggleActuator(<?= $actuator['id'] ?>, 'OFF')"
                                        <?= !$actuator['is_active'] ? 'disabled' : '' ?>>
                                        <i class="bi bi-stop-circle"></i> Arrêter
                                    </button>
                                <?php else: ?>
                                    <button 
                                        class="btn btn-success"
                                        data-actuator-id="<?= $actuator['id'] ?>"
                                        onclick="toggleActuator(<?= $actuator['id'] ?>, 'ON')"
                                        <?= !$actuator['is_active'] ? 'disabled' : '' ?>>
                                        <i class="bi bi-play-circle"></i> Démarrer
                                    </button>
                                <?php endif; ?>
                                
                                <!-- Bouton simulation pour les tests -->
                                <button 
                                    class="btn btn-outline-info btn-sm"
                                    onclick="simulateActuator(<?= $actuator['id'] ?>)">
                                    <i class="bi bi-lightning"></i> Test Simulation
                                </button>
                            <?php else: ?>
                                <div class="alert alert-light text-center">
                                    <small class="text-muted">
                                        <i class="bi bi-lock"></i> Accès restreint à votre équipe
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!$actuator['is_active']): ?>
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

<!-- Section d'aide -->
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
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🌍 Éco-responsabilité</h6>
                        <ul class="list-unstyled">
                            <li>• Optimisez l'usage selon les besoins réels</li>
                            <li>• Évitez les activations inutiles</li>
                            <li>• Surveillez la consommation énergétique</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour simuler un actionneur (utile pour les tests)
function simulateActuator(actuatorId) {
    // Alterner l'état pour la simulation
    const button = document.querySelector(`[data-actuator-id="${actuatorId}"]`);
    const currentAction = button.textContent.includes('Démarrer') ? 'ON' : 'OFF';
    
    if (confirm('Voulez-vous simuler l\'action sur cet actionneur ?')) {
        toggleActuator(actuatorId, currentAction);
    }
}

// Mettre à jour l'heure de dernière modification
function updateLastModified(actuatorId) {
    const element = document.getElementById(`last-update-${actuatorId}`);
    if (element) {
        element.textContent = new Date().toLocaleString('fr-FR');
    }
}

// Override de la fonction toggleActuator pour mettre à jour l'heure
const originalToggleActuator = window.toggleActuator;
window.toggleActuator = function(actuatorId, action) {
    originalToggleActuator(actuatorId, action);
    updateLastModified(actuatorId);
};
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
    animation: pulse 2s infinite;
}

.status-on {
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
}

.status-off {
    opacity: 0.7;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
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
</style>