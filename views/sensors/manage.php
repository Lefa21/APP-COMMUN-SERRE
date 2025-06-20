<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=home">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=sensor">Capteurs</a></li>
                <li class="breadcrumb-item active">Gestion</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1>🔧 Gestion des Capteurs</h1>
            <div class="btn-group">
                    <a href="<?= BASE_URL ?>?controller=sensor" class="btn btn-outline-primary btn-sm">Voir les détails des capteurs</a>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSensorModal">
                    <i class="bi bi-plus-circle"></i> Ajouter un capteur
                </button>
            </div>
        </div>
        <p class="text-muted">Gérez les capteurs de toutes les équipes</p>
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

<!-- Filtres et statistiques -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="typeFilter" class="form-label">Filtrer par type:</label>
                        <select id="typeFilter" class="form-select" onchange="filterSensors()">
                            <option value="">Tous les types</option>
                            <option value="temperature">Température</option>
                            <option value="humidite">Humidité</option>
                             <option value="humidite_sol">Humidité du sol</option>
                            <option value="luminosite">Luminosité</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="statusFilter" class="form-label">Filtrer par statut:</label>
                        <select id="statusFilter" class="form-select" onchange="filterSensors()">
                            <option value="">Tous</option>
                            <option value="1">Actifs</option>
                            <option value="0">Inactifs</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="text-primary"><?= count($sensors) ?></h4>
                <p class="mb-0">Capteurs configurés</p>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des capteurs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📋 Liste des capteurs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="sensorsTable">
                        <thead>
                            <tr>
                                <th>type</th>
                                <th>Unité</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sensors as $sensor): ?>
                                <tr  
                                    data-type="<?= $sensor['name'] ?>" 
                                    data-status="<?= $sensor['is_active'] ?>"
                                    class="sensor-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $icon = '';
                                            switch ($sensor['name']) {
                                                case 'temperature': $icon = '🌡️'; break;
                                                case 'humidite': $icon = '💧'; break;
                                                case 'luminosite': $icon = '☀️'; break;
                                                case 'humidite_sol': $icon = '🌱'; break;
                                                  /*
                                                case 'ph': $icon = '🧪'; break;
                                                case 'co2': $icon = '🌬️'; break;
                                                */
                                                default: $icon = '📊';
                                            }
                                            ?>
                                            <span class="me-2"><?= $icon ?></span>
                                            <?= htmlspecialchars($sensor['name']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($sensor['unit']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $sensor['is_active'] ? 'success' : 'secondary' ?>">
                                            <?= $sensor['is_active'] ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= BASE_URL ?>?controller=sensor&action=details&id=<?= $sensor['id'] ?>" 
                                               class="btn btn-outline-primary btn-sm" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-warning btn-sm" 
                                                    onclick="editSensor(<?= $sensor['id'] ?>)" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="deleteSensor(<?= $sensor['id'] ?>, '<?= htmlspecialchars($sensor['name']) ?>')" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($sensors)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3">Aucun capteur configuré</h5>
                        <p class="text-muted">Commencez par ajouter votre premier capteur</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSensorModal">
                            <i class="bi bi-plus-circle"></i> Ajouter un capteur
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter un capteur -->
<div class="modal fade" id="addSensorModal" tabindex="-1" aria-labelledby="addSensorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSensorModalLabel">➕ Ajouter un nouveau capteur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="management_action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sensorName" class="form-label">Type du capteur *</label>
                        <input type="text" class="form-control" id="sensorName" name="name" required 
                               placeholder="Ex: Température Serre A">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sensorType" class="form-label">Type *</label>
                                <select class="form-select" id="sensorType" name="type" required>
                                    <option value="">Choisir un type</option>
                                    <option value="temperature">🌡️ Température</option>
                                    <option value="humidite">💧 Humidité</option>
                                    <option value="humidite_sol">💧 Humidité du sol</option>
                                    <option value="luminosite">☀️ Luminosité</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sensorUnit" class="form-label">Unité *</label>
                                <input type="text" class="form-control" id="sensorUnit" name="unit" required 
                                       placeholder="Ex: °C, %, %">
                            </div>
                        </div>
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

<!-- Modal Modifier un capteur -->
<div class="modal fade" id="editSensorModal" tabindex="-1" aria-labelledby="editSensorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSensorModalLabel">✏️ Modifier le capteur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editSensorForm">
                <input type="hidden" name="management_action" value="edit">
                <input type="hidden" name="sensor_id" id="editSensorId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editSensorName" class="form-label">Type du capteur *</label>
                        <input type="text" class="form-control" id="editSensorName" name="name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editSensorUnit" class="form-label">Unité *</label>
                                <input type="text" class="form-control" id="editSensorUnit" name="unit" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editSensorActive" name="is_active" checked>
                            <label class="form-check-label" for="editSensorActive">
                                Capteur actif
                            </label>
                        </div>
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

<script>
// Données des capteurs pour JavaScript
const sensorsData = <?= json_encode($sensors) ?>;

// Filtrage des capteurs
function filterSensors() {
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('.sensor-row');
    
    rows.forEach(row => {
        const type = row.dataset.type;
        const status = row.dataset.status;
        
        const typeMatch = !typeFilter || type === typeFilter;
        const statusMatch = !statusFilter || status === statusFilter;
        
        if (typeMatch && statusMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Modifier un capteur
function editSensor(sensorId) {
    const sensor = sensorsData.find(s => s.id == sensorId);
    if (!sensor) return;
    
    document.getElementById('editSensorId').value = sensor.id;
    document.getElementById('editSensorName').value = sensor.name;
    document.getElementById('editSensorUnit').value = sensor.unit;
    document.getElementById('editSensorActive').checked = sensor.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editSensorModal')).show();
}

// Supprimer un capteur
function deleteSensor(sensorId, sensorName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le capteur "${sensorName}" ?\n\nCette action supprimera également toutes les données associées et ne peut pas être annulée.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="management_action" value="delete">
            <input type="hidden" name="sensor_id" value="${sensorId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Simuler un capteur individuel
function simulateSensor(sensorId) {
    const formData = new FormData();
    formData.append('sensor_id', sensorId);
    formData.append('count', 3);
    
    fetch('<?= BASE_URL ?>?controller=sensor&action=simulate', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        showNotification('Erreur de communication', 'error');
    });
}

// Simuler tous les capteurs
function simulateAllSensors() {
    new bootstrap.Modal(document.getElementById('batchSimulateModal')).show();
}

// Auto-complétion des unités selon le type
document.getElementById('sensorType').addEventListener('change', function() {
    const unitField = document.getElementById('sensorUnit');
    const units = {
        'temperature': '°C',
        'humidite': '%',
        'luminosite': '%',
        'humidite_sol': '%',

        /*
        'ph': 'pH',
        'co2': 'ppm'
        */
    };
    
    if (units[this.value]) {
        unitField.value = units[this.value];
    }
});
</script>