<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=home">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=sensor">Capteurs</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($sensor['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1>
                <?php
                $icon = '';
                switch ($sensor['type']) {
                    case 'temperature': $icon = '🌡️'; break;
                    case 'humidity': $icon = '💧'; break;
                    case 'soil_moisture': $icon = '🌱'; break;
                    case 'light': $icon = '☀️'; break;
                    case 'ph': $icon = '🧪'; break;
                    case 'co2': $icon = '🌬️'; break;
                    default: $icon = '📊';
                }
                ?>
                <?= $icon ?> <?= htmlspecialchars($sensor['name']) ?>
            </h1>
            <div class="btn-group">
                <button class="btn btn-outline-primary" onclick="simulateData()">
                    <i class="bi bi-lightning"></i> Simuler données
                </button>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> Exporter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=sensor&action=export&sensor_id=<?= $sensor['id'] ?>&period=<?= $period ?>&format=csv">CSV</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=sensor&action=export&sensor_id=<?= $sensor['id'] ?>&period=<?= $period ?>&format=json">JSON</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informations du capteur -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📋 Informations du capteur</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td><?= ucfirst($sensor['type']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Unité:</strong></td>
                                <td><?= htmlspecialchars($sensor['unit']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Équipe:</strong></td>
                                <td><?= htmlspecialchars($sensor['team_name'] ?? 'Non assignée') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>#<?= $sensor['id'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>État:</strong></td>
                                <td>
                                    <span class="badge bg-success">Actif</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dernière lecture:</strong></td>
                                <td>
                                    <?php if ($sensor['value'] !== null): ?>
                                        <?= date('d/m/Y H:i:s', strtotime($sensor['last_reading'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Aucune donnée</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📊 Valeur actuelle</h5>
            </div>
            <div class="card-body text-center">
                <?php if ($sensor['value'] !== null): ?>
                    <?php
                    $valueClass = 'text-primary';
                    $status = 'Normal';
                    
                    // Déterminer la classe CSS selon la valeur et le type
                    switch ($sensor['type']) {
                        case 'temperature':
                            if ($sensor['value'] < 15 || $sensor['value'] > 35) {
                                $valueClass = 'text-danger';
                                $status = 'Critique';
                            } elseif ($sensor['value'] < 18 || $sensor['value'] > 30) {
                                $valueClass = 'text-warning';
                                $status = 'Attention';
                            } else {
                                $valueClass = 'text-success';
                                $status = 'Optimal';
                            }
                            break;
                        case 'humidity':
                        case 'soil_moisture':
                            if ($sensor['value'] < 30) {
                                $valueClass = 'text-danger';
                                $status = 'Trop bas';
                            } elseif ($sensor['value'] > 90) {
                                $valueClass = 'text-warning';
                                $status = 'Trop élevé';
                            } else {
                                $valueClass = 'text-success';
                                $status = 'Optimal';
                            }
                            break;
                        case 'ph':
                            if ($sensor['value'] < 6.0 || $sensor['value'] > 7.5) {
                                $valueClass = 'text-warning';
                                $status = 'À surveiller';
                            } else {
                                $valueClass = 'text-success';
                                $status = 'Optimal';
                            }
                            break;
                    }
                    ?>
                    <h2 class="<?= $valueClass ?> mb-1">
                        <?= number_format($sensor['value'], 1) ?>
                        <small><?= htmlspecialchars($sensor['unit']) ?></small>
                    </h2>
                    <p class="mb-0">
                        <span class="badge bg-<?= $valueClass === 'text-success' ? 'success' : ($valueClass === 'text-warning' ? 'warning' : 'danger') ?>">
                            <?= $status ?>
                        </span>
                    </p>
                <?php else: ?>
                    <div class="text-muted">
                        <i class="bi bi-question-circle" style="font-size: 3rem;"></i>
                        <p>Aucune donnée disponible</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Filtres de période -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Période d'affichage:</h6>
                    <div class="btn-group" role="group">
                        <a href="?controller=sensor&action=details&id=<?= $sensor['id'] ?>&period=1h" 
                           class="btn btn-<?= $period === '1h' ? 'primary' : 'outline-primary' ?> btn-sm">1h</a>
                        <a href="?controller=sensor&action=details&id=<?= $sensor['id'] ?>&period=24h" 
                           class="btn btn-<?= $period === '24h' ? 'primary' : 'outline-primary' ?> btn-sm">24h</a>
                        <a href="?controller=sensor&action=details&id=<?= $sensor['id'] ?>&period=7d" 
                           class="btn btn-<?= $period === '7d' ? 'primary' : 'outline-primary' ?> btn-sm">7j</a>
                        <a href="?controller=sensor&action=details&id=<?= $sensor['id'] ?>&period=30d" 
                           class="btn btn-<?= $period === '30d' ? 'primary' : 'outline-primary' ?> btn-sm">30j</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphique -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📈 Évolution - <?= $period ?></h5>
            </div>
            <div class="card-body">
                <canvas id="sensorChart" width="100" height="40"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques -->
<?php if ($stats && $stats['total_readings'] > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📊 Statistiques - <?= $period ?></h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-primary"><?= number_format($stats['avg_value'], 1) ?></h4>
                        <p class="mb-0">Moyenne</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-success"><?= number_format($stats['min_value'], 1) ?></h4>
                        <p class="mb-0">Minimum</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning"><?= number_format($stats['max_value'], 1) ?></h4>
                        <p class="mb-0">Maximum</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-info"><?= $stats['total_readings'] ?></h4>
                        <p class="mb-0">Mesures</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Tableau des données récentes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 Données récentes</h5>
                <small class="text-muted"><?= count($data) ?> entrées</small>
            </div>
            <div class="card-body">
                <?php if (empty($data)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">Aucune donnée disponible pour cette période</p>
                        <button class="btn btn-primary" onclick="simulateData()">
                            Générer des données de test
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Valeur</th>
                                    <th>Unité</th>
                                    <th>Date/Heure</th>
                                    <th>État</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($data, 0, 20) as $reading): ?>
                                    <tr>
                                        <td>
                                            <strong><?= number_format($reading['value'], 2) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($sensor['unit']) ?></td>
                                        <td>
                                            <small><?= date('d/m/Y H:i:s', strtotime($reading['timestamp'])) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'bg-primary';
                                            if ($sensor['type'] === 'temperature') {
                                                if ($reading['value'] < 15 || $reading['value'] > 35) {
                                                    $badgeClass = 'bg-danger';
                                                } elseif ($reading['value'] < 18 || $reading['value'] > 30) {
                                                    $badgeClass = 'bg-warning';
                                                } else {
                                                    $badgeClass = 'bg-success';
                                                }
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">Normal</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (count($data) > 20): ?>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Affichage des 20 dernières mesures sur <?= count($data) ?> total
                            </small>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour le graphique -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique
    const chartData = <?= json_encode($chartData) ?>;
    
    const ctx = document.getElementById('sensorChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => {
                const date = new Date(item.time_group);
                return date.toLocaleDateString('fr-FR') + ' ' + date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
            }),
            datasets: [{
                label: '<?= htmlspecialchars($sensor['name']) ?>',
                data: chartData.map(item => item.avg_value),
                borderColor: 'rgb(45, 90, 39)',
                backgroundColor: 'rgba(45, 90, 39, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Évolution sur <?= $period ?>'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: '<?= htmlspecialchars($sensor['unit']) ?>'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Temps'
                    }
                }
            }
        }
    });
    
    // Redimensionner le graphique
    chart.canvas.parentNode.style.height = '400px';
});

// Fonction de simulation
function simulateData() {
    const formData = new FormData();
    formData.append('sensor_id', <?= $sensor['id'] ?>);
    formData.append('count', 5);
    
    fetch('<?= BASE_URL ?>?controller=sensor&action=simulate', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        showNotification('Erreur de communication', 'error');
    });
}
</script>