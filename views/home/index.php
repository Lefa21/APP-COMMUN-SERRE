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

                        <div class="d-flex align-items-center mb-2">
                            <?php
                            $icon = '';
                            $valueClass = 'text-primary';
                            switch ($sensor['name']) {
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
                                case 'humidite':
                                    $icon = '💧';
                                    if ($sensor['value'] !== null) {
                                        if ($sensor['value'] < 30 || $sensor['value'] > 90) {
                                            $valueClass = 'text-warning';
                                        } else {
                                            $valueClass = 'text-success';
                                        }
                                    }
                                    break;
                                case 'humidite_sol':
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
                                case 'luminosite':
                                    $icon = '☀️';
                                    break;
                                case 'bouton':
                                    $icon = '📊';
                                    break;
                                    /*
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
                                    */
                            }
                            ?>
                            <span class="me-2"><?= $icon ?></span>
                            <span><?= ucfirst($sensor['name']) ?></span>
                        </div>

                        <?php if (isset($sensor['value']) && $sensor['value'] !== null): ?>

                            <div class="mb-2">
                                <?php // --- DÉBUT DE LA CORRECTION --- 
                                ?>

                                <?php if (isset($sensor['name']) && $sensor['name'] === 'bouton'): ?>

                                    <?php // Affichage spécifique pour le capteur de type "bouton"
                                    $buttonStatusText = ($sensor['value'] == 1) ? 'EN MARCHE' : 'ARRÊT';
                                    $buttonStatusClass = ($sensor['value'] == 1) ? 'text-success' : 'text-secondary';
                                    ?>
                                    <h4 class="<?= $buttonStatusClass ?> mb-0">
                                        <?= $buttonStatusText ?>
                                    </h4>

                                <?php else: ?>

                                    <?php // Affichage par défaut pour tous les autres capteurs (température, humidité, etc.) 
                                    ?>
                                    <h4 class="<?= $valueClass ?? 'text-primary' ?> mb-0">
                                        <?= number_format((float)$sensor['value'], 1) ?> <?= htmlspecialchars($sensor['unit'] ?? '') ?>
                                    </h4>

                                <?php endif; ?>

                                <?php
                                ?>

                                <small class="text-muted">
                                    Dernière lecture à <?= isset($sensor['timestamp']) ? date('H:i:s', strtotime($sensor['timestamp'])) : 'N/A' ?>
                                </small>
                            </div>

                            <!-- Barre de progression pour certains types de capteurs -->
                            <?php if (isset($sensor['name']) && in_array($sensor['name'], ['humidity', 'humidite_sol', 'luminosite'])): ?>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar <?= (float)$sensor['value'] < 40 ? 'bg-warning' : 'bg-success' ?>"
                                        style="width: <?= min(100, max(0, (float)$sensor['value'])) ?>%"></div>
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
                <a href="<?= BASE_URL ?>?controller=actuator&action=manage" class="btn btn-outline-primary">
                    Voir tous les actionneurs
                </a>
            </div>
            <p class="text-muted"> Vous pouvez visualiser l'état de tous les actionneurs et contrôler ceux connectés à votre système (Bouton et Moteur).</p>
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
                            <div class="d-flex align-items-center mb-2">
                                <?php
                                $icon = '';
                                switch ($actuator['name']) {
                                    case 'moteur':
                                        $icon = '⚡';
                                        break;
                                    case 'led':
                                        $icon = '💡';
                                        break;
                                    default:
                                        $icon = '⚡';
                                }
                                ?>
                                <span class="me-2"><?= $icon ?></span>
                                <h6 class="card-title mb-0"><?= ucfirst($actuator['name']) ?></h6>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <!-- Partie gauche : Affichage du statut (visible pour TOUS les actionneurs) -->
                                <div class="d-flex align-items-center">
                                    <span class="status-indicator <?= $actuator['etat'] ? 'status-on' : 'status-off' ?> me-2"></span>
                                    <span class="text-muted">
                                        <?= $actuator['etat'] ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </div>

                                <!-- Partie droite : Le bouton de contrôle (logique conditionnelle) -->
                                <div>
                                    <?php
                                    // On définit le type d'actionneur que vous pouvez contrôler manuellement.
                                    $controllable_type = 'moteur';

                                    // On vérifie si l'actionneur affiché est bien votre moteur.
                                    if (isset($actuator['name']) && $actuator['name'] === $controllable_type):
                                    ?>
                                        <!-- Si OUI, on affiche le bouton de contrôle qui fonctionne. -->
                                        <button
                                            class="btn <?= $actuator['etat'] ? 'btn-danger' : 'btn-success' ?> btn-sm"
                                            onclick="commandHardware(<?= $actuator['id'] ?>, '<?= $actuator['etat'] ? 'OFF' : 'ON' ?>')"
                                            title="Envoyer une commande au moteur">
                                            <i class="bi bi-gear-wide-connected"></i>
                                            <?= $actuator['etat'] ? 'Arrêter' : 'Démarrer' ?>
                                        </button>
                                    <?php else: ?>
                                        <!-- Si NON, on affiche un bouton gris et désactivé. -->
                                        <button class="btn btn-secondary btn-sm" disabled>
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