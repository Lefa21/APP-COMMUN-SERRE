<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>🌡️ Capteurs et Données</h1>
            <div>
                <?php if ($isAdmin): ?>
                    <a href="<?= BASE_URL ?>?controller=sensor&action=manage" class="btn btn-primary me-2">
                        <i class="bi bi-gear"></i> Administration
                    </a>
                <?php endif; ?>
                <button class="btn btn-success" onclick="refreshAllData()">
                    <i class="bi bi-arrow-clockwise"></i> Actualiser
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alertes système -->
<?php if (!empty($alerts)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <h5><i class="bi bi-exclamation-triangle"></i> Alertes Actives (<?= count($alerts) ?>)</h5>
            <div class="row">
                <?php foreach ($alerts as $alert): ?>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-<?= $alert['alert_level'] === 'critical' ? 'danger' : 'warning' ?> me-2">
                                <?= strtoupper($alert['alert_level']) ?>
                            </span>
                            <small>
                                <strong><?= htmlspecialchars($alert['name']) ?></strong>: 
                                <?= $alert['value'] ?> <?= $alert['unit'] ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filtres et options -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label for="typeFilter" class="form-label">Filtrer par type:</label>
                        <select id="typeFilter" class="form-select" onchange="filterSensors()">
                            <option value="">Tous les types</option>
                            <option value="temperature">Température</option>
                            <option value="humidity">Humidité</option>
                            <option value="soil_moisture">Humidité du sol</option>
                            <option value="light">Luminosité</option>
                            <option value="ph">pH</option>
                            <option value="co2">CO2</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Actions rapides:</label>
                        <div class="d-grid">
                            <button class="btn btn-outline-info btn-sm" onclick="simulateAllSensors()">
                                <i class="bi bi-lightning"></i> Simuler données
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grille des capteurs -->
<div class="row" id="sensorsGrid">
    <?php if (empty($sensors)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <h5>Aucun capteur configuré</h5>
                <p>
                    <?php if ($isAdmin): ?>
                        Aucun capteur n'a été configuré. 
                        <a href="<?= BASE_URL ?>?controller=sensor&action=manage" class="alert-link">
                            Ajoutez-en un depuis l'interface d'administration
                        </a>.
                    <?php else: ?>
                        Aucun capteur n'est disponible. Les équipes doivent d'abord configurer leurs capteurs.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($sensors as $sensor): ?>
            <div class="col-lg-4 col-md-6 mb-4 sensor-card" 
                 data-type="<?= $sensor['name'] ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><?= htmlspecialchars($sensor['name']) ?></h6>
                    </div>
                    
                    <div class="card-body">
                        <!-- Type et icône -->
                        <div class="d-flex align-items-center mb-3">
                            <?php
                            $icon = '';
                            $color = 'text-primary';
                            $status = 'normal';
                            
                            switch ($sensor['name']) {
                                case 'temperature':
                                    $icon = '🌡️';
                                    if ($sensor['value'] !== null) {
                                        if ($sensor['value'] < 15 || $sensor['value'] > 35) {
                                            $color = 'text-danger';
                                            $status = 'alert';
                                        } elseif ($sensor['value'] < 18 || $sensor['value'] > 30) {
                                            $color = 'text-warning';
                                            $status = 'warning';
                                        } else {
                                            $color = 'text-success';
                                        }
                                    }
                                    break;
                                case 'humidite':
                                    $icon = '💧';
                                    if ($sensor['value'] !== null) {
                                        if ($sensor['value'] < 30 || $sensor['value'] > 90) {
                                            $color = 'text-warning';
                                            $status = 'warning';
                                        } else {
                                            $color = 'text-success';
                                        }
                                    }
                                    break;
                                    /*
                                case 'soil_moisture':
                                    $icon = '🌱';
                                    if ($sensor['value'] !== null) {
                                        if ($sensor['value'] < 25) {
                                            $color = 'text-danger';
                                            $status = 'alert';
                                        } elseif ($sensor['value'] < 40) {
                                            $color = 'text-warning';
                                            $status = 'warning';
                                        } else {
                                            $color = 'text-success';
                                        }
                                    }
                                    break;
                                    */
                                case 'luminosite':
                                    $icon = '☀️';
                                    break;
                                    /*
                                case 'ph':
                                    $icon = '🧪';
                                    if ($sensor['value'] !== null) {
                                        if ($sensor['value'] < 6.0 || $sensor['value'] > 7.5) {
                                            $color = 'text-warning';
                                            $status = 'warning';
                                        } else {
                                            $color = 'text-success';
                                        }
                                    }
                                    break;
                                case 'co2':
                                    $icon = '🌬️';
                                    break;
                                    */
                            }
                            ?>
                            <span class="me-3" style="font-size: 2rem;"><?= $icon ?></span>
                            <div class="flex-grow-1">
                                <h6 class="mb-0"><?= ucfirst($sensor['type']) ?></h6>
                                <small class="text-muted">Capteur #<?= $sensor['id'] ?></small>
                            </div>
                            <?php if ($status !== 'normal'): ?>
                                <span class="badge bg-<?= $status === 'alert' ? 'danger' : 'warning' ?>">
                                    <?= strtoupper($status) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Valeur actuelle -->
                        <div class="text-center mb-3">
                            <?php if ($sensor['value'] !== null): ?>
                                <h3 class="<?= $color ?> mb-1">
                                    <?= number_format($sensor['value'], 1) ?> 
                                    <small><?= htmlspecialchars($sensor['unit']) ?></small>
                                </h3>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i>
                                    <?= $sensor['timestamp'] ? date('d/m H:i', strtotime($sensor['timestamp'])) : 'Pas de données' ?>
                                </small>
                            <?php else: ?>
                                <div class="text-muted">
                                    <i class="bi bi-question-circle" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Aucune donnée</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Graphique miniature (si données disponibles) -->
                        <?php if ($sensor['value'] !== null): ?>
                            <div class="mb-3">
                                <canvas id="chart-<?= $sensor['id'] ?>" width="100" height="40" style="max-height: 40px;"></canvas>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <a href="<?= BASE_URL ?>?controller=sensor&action=details&id=<?= $sensor['id'] ?>" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-graph-up"></i> Voir détails
                            </a>
                            
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-info btn-sm" 
                                        onclick="simulateSensor(<?= $sensor['id'] ?>)">
                                    <i class="bi bi-lightning"></i> Simuler
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" 
                                        onclick="exportSensorData(<?= $sensor['id'] ?>)">
                                    <i class="bi bi-download"></i> Exporter
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="status-indicator <?= $sensor['value'] !== null ? 'status-on' : 'status-off' ?>"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal d'export -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Exporter les données</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <input type="hidden" id="exportSensorId" name="sensor_id">
                    
                    <div class="mb-3">
                        <label for="exportPeriod" class="form-label">Période:</label>
                        <select id="exportPeriod" name="period" class="form-select">
                            <option value="1h">Dernière heure</option>
                            <option value="24h" selected>Dernières 24h</option>
                            <option value="7d">7 derniers jours</option>
                            <option value="30d">30 derniers jours</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Format:</label>
                        <select id="exportFormat" name="format" class="form-select">
                            <option value="csv">CSV (Excel)</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="doExport()">
                    <i class="bi bi-download"></i> Télécharger
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Variables globales
let sensorsData = <?= json_encode($sensors) ?>;
let charts = {};

// Initialisation des graphiques miniatures
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    sensorsData.forEach(sensor => {
        if (sensor.value !== null) {
            createMiniChart(sensor.id);
        }
    });
}

function createMiniChart(sensorId) {
    const ctx = document.getElementById(`chart-${sensorId}`);
    if (!ctx) return;
    
    // Générer des données factices pour la démo
    const data = Array.from({length: 24}, () => Math.random() * 10 + 20);
    
    charts[sensorId] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array.from({length: 24}, (_, i) => i),
            datasets: [{
                data: data,
                borderColor: 'rgb(45, 90, 39)',
                backgroundColor: 'rgba(45, 90, 39, 0.1)',
                borderWidth: 1,
                pointRadius: 0,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { display: false },
                y: { display: false }
            },
            elements: {
                point: { radius: 0 }
            }
        }
    });
}

function filterSensors() {
    const typeFilter = document.getElementById('typeFilter').value;
    const cards = document.querySelectorAll('.sensor-card');
    
    cards.forEach(card => {
        const cardType = card.dataset.type;
        
        const typeMatch = !typeFilter || cardType === typeFilter;
        
        if (typeMatch) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function refreshAllData() {
    window.location.reload();
}

function simulateSensor(sensorId) {
    if (confirm('Générer des données simulées pour ce capteur ?')) {
        const formData = new FormData();
        formData.append('sensor_id', sensorId);
        formData.append('count', 1);
        
        fetch('<?= BASE_URL ?>?controller=sensor&action=simulate', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.error, 'error');
            }
        })
        .catch(error => {
            showNotification('Erreur de communication', 'error');
        });
    }
}

function simulateAllSensors() {
    if (confirm('Générer des données pour tous les capteurs ?')) {
        sensorsData.forEach(sensor => {
            simulateSensor(sensor.id);
        });
    }
}

function exportSensorData(sensorId) {
    document.getElementById('exportSensorId').value = sensorId;
    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

function doExport() {
    const sensorId = document.getElementById('exportSensorId').value;
    const period = document.getElementById('exportPeriod').value;
    const format = document.getElementById('exportFormat').value;
    
    const url = `<?= BASE_URL ?>?controller=sensor&action=export&sensor_id=${sensorId}&period=${period}&format=${format}`;
    window.open(url, '_blank');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
    modal.hide();
}
</script>

<style>
.sensor-card {
    transition: all 0.3s ease;
}

.sensor-card:hover {
    transform: translateY(-2px);
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
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

/* Optimisation éco-responsable */
@media (prefers-reduced-motion: reduce) {
    .sensor-card {
        transition: none;
    }
    
    .status-on {
        animation: none;
    }
}
</style>