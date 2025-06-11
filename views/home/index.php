<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>🌱 Tableau de Bord - Serres Connectées</h1>
            <div class="text-muted">
                <small>Dernière mise à jour: <?= date('H:i:s') ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Vue d'ensemble -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📊 Vue d'ensemble des Serres</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-<?= $isAdmin ? '3' : '4' ?>">
                        <h3 class="text-primary"><?= count($sensors) ?></h3>
                        <p class="mb-0">Capteurs Actifs</p>
                    </div>
                    <?php if ($isAdmin): ?>
                        <div class="col-md-3">
                            <h3 class="text-success"><?= count($actuators) ?></h3>
                            <p class="mb-0">Actionneurs</p>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-<?= $isAdmin ? '3' : '4' ?>">
                        <h3 class="text-info">5</h3>
                        <p class="mb-0">Équipes</p>
                    </div>
                    <div class="col-md-<?= $isAdmin ? '3' : '4' ?>">
                        <h3 class="text-warning"><?= count($recentActivity) ?></h3>
                        <p class="mb-0">Actions Récentes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Capteurs -->
<div class="row mb-4">
    <div class="col-12">
        <h3>🌡️ État des Capteurs</h3>
        <p class="text-muted">Données en temps réel de toutes les équipes</p>
    </div>
</div>

<div class="row">
    <?php if (empty($sensors)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <h5>Aucun capteur actif</h5>
                <p>Les capteurs n'ont pas encore été configurés.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($sensors as $sensor): ?>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card sensor-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0"><?= htmlspecialchars($sensor['name']) ?></h6>
                            <span class="badge bg-secondary"><?= htmlspecialchars($sensor['team_name'] ?? 'Équipe 1') ?></span>
                        </div>
                        
                        <div class="d-flex align-items-center mb-2">
                            <?php
                            $icon = '';
                            $valueClass = 'text-primary';
                            switch ($sensor['type']) {
                                case 'temperature':
                                    $icon = '🌡️';
                                    if ($sensor['value'] !== null) {
                                        if ($sensor['value'] < 15 || $sensor['value'] > 35) {
                                            $valueClass = 'text-danger';
                                        } elseif ($sensor['value'] < 18 || $sensor['value'] > 30) {
                                            $valueClass = 'text-warning';
                                        } else {
                                            $valueClass = 'text-success';
                                        }
                                    }
                                    break;
                                case 'humidity':
                                    $icon = '💧';
                                    if ($sensor['value'] !== null) {
                                        if ($sensor['value'] < 30 || $sensor['value'] > 90) {
                                            $valueClass = 'text-warning';
                                        } else {
                                            $valueClass = 'text-success';
                                        }
                                    }
                                    break;
                                case 'soil_moisture':
                                    $icon = '🌱';
                                    if ($sensor['value'] !== null) {
                                        if ($sensor['value'] < 25) {
                                            $valueClass = 'text-danger';
                                        } elseif ($sensor['value'] < 40) {
                                            $valueClass = 'text-warning';
                                        } else {
                                            $valueClass = 'text-success';
                                        }
                                    }
                                    break;
                                case 'light':
                                    $icon = '☀️';
                                    break;
                                case 'ph':
                                    $icon = '🧪';
                                    if ($sensor['value'] !== null) {
                                        if ($sensor['value'] < 6.0 || $sensor['value'] > 7.5) {
                                            $valueClass = 'text-warning';
                                        } else {
                                            $valueClass = 'text-success';
                                        }
                                    }
                                    break;
                                case 'co2':
                                    $icon = '🌬️';
                                    break;
                            }
                            ?>
                            <span class="me-2"><?= $icon ?></span>
                            <span><?= ucfirst($sensor['type']) ?></span>
                        </div>
                        
                        <?php if ($sensor['value'] !== null): ?>
                            <div class="mb-2">
                                <h4 class="<?= $valueClass ?> mb-0">
                                    <?= number_format($sensor['value'], 1) ?> <?= htmlspecialchars($sensor['unit']) ?>
                                </h4>
                                <small class="text-muted">
                                    <?= $sensor['timestamp'] ? date('H:i', strtotime($sensor['timestamp'])) : 'Pas de données' ?>
                                </small>
                            </div>
                            
                            <!-- Barre de progression pour certains capteurs -->
                            <?php if (in_array($sensor['type'], ['humidity', 'soil_moisture'])): ?>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar <?= $sensor['value'] < 40 ? 'bg-danger' : 'bg-success' ?>" 
                                         style="width: <?= min(100, max(0, $sensor['value'])) ?>%"></div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-muted">
                                <small>Aucune donnée disponible</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Section Actionneurs - SEULEMENT pour les administrateurs -->
<?php if ($isAdmin): ?>
    <div class="row mb-4 mt-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3>⚡ Contrôle des Actionneurs</h3>
                <a href="<?= BASE_URL ?>?controller=actuator" class="btn btn-outline-primary">
                    Voir tous les actionneurs
                </a>
            </div>
            <p class="text-muted">Vous avez les privilèges administrateur pour contrôler tous les actionneurs</p>
        </div>
    </div>

    <div class="row">
        <?php if (empty($actuators)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <h5>Aucun actionneur configuré</h5>
                    <p>Les actionneurs n'ont pas encore été ajoutés.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($actuators as $actuator): ?>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="card-title mb-0"><?= htmlspecialchars($actuator['name']) ?></h6>
                                <span class="badge bg-info"><?= htmlspecialchars($actuator['team_name'] ?? 'Équipe 1') ?></span>
                            </div>
                            
                            <div class="d-flex align-items-center mb-2">
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
                                <span><?= ucfirst($actuator['type']) ?></span>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="status-indicator <?= $actuator['current_state'] ? 'status-on' : 'status-off' ?> me-2"></span>
                                    <span class="text-muted">
                                        <?= $actuator['current_state'] ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </div>
                                
                                <button 
                                    class="btn <?= $actuator['current_state'] ? 'btn-danger' : 'btn-success' ?> btn-sm"
                                    data-actuator-id="<?= $actuator['id'] ?>"
                                    onclick="toggleActuator(<?= $actuator['id'] ?>, '<?= $actuator['current_state'] ? 'OFF' : 'ON' ?>')">
                                    <?= $actuator['current_state'] ? 'Arrêter' : 'Démarrer' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Message pour les utilisateurs non-admin -->
    <div class="row mb-4 mt-5">
        <div class="col-12">
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <span class="me-3">🔒</span>
                    <div>
                        <h5 class="mb-1">Accès Restreint</h5>
                        <p class="mb-0">
                            La gestion des actionneurs est réservée aux administrateurs. 
                            Vous pouvez consulter les données des capteurs et surveiller l'état de votre serre.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Activité récente - SEULEMENT pour les administrateurs -->
<?php if ($isAdmin): ?>
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📋 Activité Récente des Actionneurs</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recentActivity)): ?>
                        <p class="text-muted mb-0">Aucune activité récente</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Actionneur</th>
                                        <th>Utilisateur</th>
                                        <th>Équipe</th>
                                        <th>Date/Heure</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentActivity as $activity): ?>
                                        <tr>
                                            <td>
                                                <span class="badge <?= $activity['action'] === 'ON' ? 'bg-success' : 'bg-secondary' ?>">
                                                    <?= $activity['action'] ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($activity['actuator_name']) ?></td>
                                            <td>👤 <?= htmlspecialchars($activity['username']) ?></td>
                                            <td><?= htmlspecialchars($activity['team_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m H:i', strtotime($activity['timestamp'])) ?>
                                                </small>
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
<?php endif; ?>

<!-- Informations éco-responsables -->
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-success">
            <div class="d-flex align-items-center">
                <span class="me-2">🌍</span>
                <div>
                    <strong>Impact Éco-responsable:</strong>
                    Cette page utilise un design optimisé pour réduire la consommation d'énergie. 
                    Les données sont actualisées intelligemment toutes les 30 secondes uniquement si la page est active.
                    <?php if (!$isAdmin): ?>
                        L'accès limité aux fonctionnalités réduit également l'empreinte énergétique.
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>