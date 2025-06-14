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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Capteurs -->
<div class="row mb-4 mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h3>🌡️ État des Capteurs</h3>
            <a href="<?= BASE_URL ?>?controller=sensor&action=manage" class="btn btn-outline-primary">
                Voir tous les capteurs
            </a>
        </div>
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

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>⚡ Actionneurs</h1>
            <?php if ($isAdmin): ?>
                <a href="<?= BASE_URL ?>?controller=actuator&action=manage" class="btn btn-outline-primary">
                    Voir tous les actionneurs
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill me-2"></i>
            Vous pouvez visualiser l'état de tous les actionneurs et contrôler ceux connectés à votre système (Bouton et Moteur).
        </div>
    </div>
</div>

<div class="row">
    <?php if (empty($actuators)): ?>
        <div class="col-12">
            <div class="alert alert-warning"><h5>Aucun actionneur disponible sur le réseau.</h5></div>
        </div>
    <?php else: ?>
        <?php foreach ($actuators as $actuator): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><?= htmlspecialchars($actuator['name']) ?></h6>
                        <span class="badge bg-secondary"><?= htmlspecialchars($actuator['type']) ?></span>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <span class="me-3 fs-2">
                                <?php
                                // Define icons for different actuator types
                                $icons = [
                                    'button' => '🔘', 
                                    'motor' => '⚙️', 
                                    'irrigation' => '💧', 
                                    'ventilation' => '🌪️', 
                                    'heating' => '🔥', 
                                    'lighting' => '💡', 
                                    'window' => '🪟'
                                ];
                                echo $icons[$actuator['type']] ?? '⚡';
                                ?>
                            </span>
                            <div>
                                <h6 class="mb-0"><?= ucfirst($actuator['type']) ?></h6>
                                <small class="text-muted">Actionneur #<?= $actuator['id'] ?></small>
                            </div>
                        </div>
                        
                        <div class="mt-auto">
                             <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-muted">État :</span>
                                <div class="d-flex align-items-center">
                                    <span class="status-indicator <?= $actuator['current_state'] ? 'status-on' : 'status-off' ?> me-2"></span>
                                    <strong class="<?= $actuator['current_state'] ? 'text-success' : 'text-secondary' ?>">
                                        <?= $actuator['current_state'] ? 'ACTIF' : 'INACTIF' ?>
                                    </strong>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <?php
                                // --- LOGIC TO DISPLAY THE BUTTON BASED ON TYPE ---
                                // Define which actuator types are controllable by this instance
                                $controllable_types = ['button', 'motor'];

                                if (in_array($actuator['type'], $controllable_types)):
                                ?>
                                    <button 
                                        class="btn <?= $actuator['current_state'] ? 'btn-danger' : 'btn-success' ?>"
                                        onclick="toggleActuator(<?= $actuator['id'] ?>, '<?= $actuator['current_state'] ? 'OFF' : 'ON' ?>')"
                                        <?= !$actuator['is_active'] ? 'disabled' : '' ?>>
                                        <i class="bi bi-<?= $actuator['current_state'] ? 'stop-circle' : 'play-circle' ?>"></i> 
                                        <?= $actuator['current_state'] ? 'Arrêter' : 'Démarrer' ?>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>
                                        <i class="bi bi-eye-fill"></i> Lecture Seule
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

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