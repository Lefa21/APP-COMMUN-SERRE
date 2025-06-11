<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>📈 Tableaux de Bord Avancés</h1>
            <div class="btn-group">
                <button class="btn btn-outline-primary" onclick="refreshAllCharts()">
                    <i class="bi bi-arrow-clockwise"></i> Actualiser
                </button>
                <button class="btn btn-outline-success" onclick="exportAllData()">
                    <i class="bi bi-download"></i> Exporter tout
                </button>
                <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    <i class="bi bi-gear"></i> Paramètres
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Contrôles de période globaux -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label for="globalPeriod" class="form-label">Période d'affichage:</label>
                        <select id="globalPeriod" class="form-select" onchange="updateAllCharts()">
                            <option value="1h">Dernière heure</option>
                            <option value="24h" selected>Dernières 24h</option>
                            <option value="7d">7 derniers jours</option>
                            <option value="30d">30 derniers jours</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="refreshInterval" class="form-label">Auto-actualisation:</label>
                        <select id="refreshInterval" class="form-select" onchange="setAutoRefresh()">
                            <option value="0">Désactivée</option>
                            <option value="30">30 secondes</option>
                            <option value="60" selected>1 minute</option>
                            <option value="300">5 minutes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">État du système:</label>
                        <div class="d-flex align-items-center">
                            <span class="status-indicator status-on me-2" id="systemStatus"></span>
                            <span id="systemStatusText">En ligne</span>
                            <small class="text-muted ms-2" id="lastUpdate">Mis à jour: maintenant</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques principaux -->
<div class="row mb-4">
    <!-- Graphique de température -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">🌡️ Température</h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="toggleChartType('temperatureChart')">
                        <i class="bi bi-bar-chart"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="exportChart('temperatureChart')">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="temperatureChart" width="100" height="60"></canvas>
                <div class="mt-2">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Moyenne</small>
                            <div class="h6 mb-0" id="tempAvg">--°C</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Minimum</small>
                            <div class="h6 mb-0 text-primary" id="tempMin">--°C</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Maximum</small>
                            <div class="h6 mb-0 text-danger" id="tempMax">--°C</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graphique d'humidité -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">💧 Humidité</h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="toggleChartType('humidityChart')">
                        <i class="bi bi-bar-chart"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="exportChart('humidityChart')">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="humidityChart" width="100" height="60"></canvas>
                <div class="mt-2">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Moyenne</small>
                            <div class="h6 mb-0" id="humAvg">--%</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Minimum</small>
                            <div class="h6 mb-0 text-warning" id="humMin">--%</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Maximum</small>
                            <div class="h6 mb-0 text-info" id="humMax">--%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques secondaires -->
<div class="row mb-4">
    <!-- Graphique multi-capteurs -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📊 Vue d'ensemble multicapteurs</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="chartSensors" class="form-label">Capteurs à afficher:</label>
                            <select id="chartSensors" class="form-select" multiple onchange="updateMultiChart()">
                                <!-- Options dynamiques ajoutées par JavaScript -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="chartNormalize" class="form-label">Normalisation:</label>
                            <select id="chartNormalize" class="form-select" onchange="updateMultiChart()">
                                <option value="none">Aucune</option>
                                <option value="percent">Pourcentage (0-100)</option>
                                <option value="zscore">Z-Score</option>
                            </select>
                        </div>
                    </div>
                </div>
                <canvas id="multiChart" width="100" height="50"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Graphique en secteurs - État des actionneurs -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">⚡ État des Actionneurs</h5>
            </div>
            <div class="card-body">
                <canvas id="actuatorsPieChart" width="100" height="100"></canvas>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center">
                            <span class="badge bg-success me-2"></span>
                            Actifs
                        </span>
                        <span id="activeActuators">0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="d-flex align-items-center">
                            <span class="badge bg-secondary me-2"></span>
                            Inactifs
                        </span>
                        <span id="inactiveActuators">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau de bord en temps réel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">⚡ Données en Temps Réel</h5>
            </div>
            <div class="card-body">
                <div class="row" id="realtimeData">
                    <!-- Données ajoutées dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphique de corrélation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🔗 Analyse de Corrélation</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="correlationX" class="form-label">Axe X:</label>
                        <select id="correlationX" class="form-select" onchange="updateCorrelationChart()">
                            <!-- Options dynamiques -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="correlationY" class="form-label">Axe Y:</label>
                        <select id="correlationY" class="form-select" onchange="updateCorrelationChart()">
                            <!-- Options dynamiques -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Coefficient de corrélation:</label>
                        <div class="h4 mb-0" id="correlationCoef">R² = --</div>
                    </div>
                </div>
                <canvas id="correlationChart" width="100" height="50"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Modal Paramètres -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">⚙️ Paramètres d'affichage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="themeSelect" class="form-label">Thème:</label>
                    <select id="themeSelect" class="form-select">
                        <option value="light">Clair</option>
                        <option value="dark">Sombre</option>
                        <option value="auto">Automatique</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="animationSpeed" class="form-label">Vitesse d'animation:</label>
                    <select id="animationSpeed" class="form-select">
                        <option value="0">Aucune</option>
                        <option value="500">Rapide</option>
                        <option value="1000" selected>Normale</option>
                        <option value="2000">Lente</option>
                    </select>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="showGrid" checked>
                    <label class="form-check-label" for="showGrid">
                        Afficher la grille
                    </label>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="showLegend" checked>
                    <label class="form-check-label" for="showLegend">
                        Afficher les légendes
                    </label>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="ecoMode">
                    <label class="form-check-label" for="ecoMode">
                        Mode éco-responsable (réduit les animations)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="applySettings()">
                    Appliquer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/date-fns@2.29.3/index.min.js"></script>

<script>
// Variables globales
let charts = {};
let sensorsData = [];
let actuatorsData = [];
let autoRefreshInterval = null;
let currentPeriod = '24h';

// Configuration des couleurs
const colors = {
    primary: '#2d5a27',
    secondary: '#5d8a54',
    accent: '#8bc34a',
    success: '#28a745',
    warning: '#ffc107',
    danger: '#dc3545',
    info: '#17a2b8'
};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    loadSensorsData();
    loadActuatorsData();
    setAutoRefresh();
});

// Initialiser tous les graphiques
function initializeCharts() {
    initTemperatureChart();
    initHumidityChart();
    initMultiChart();
    initActuatorsPieChart();
    initCorrelationChart();
}

// Graphique de température
function initTemperatureChart() {
    const ctx = document.getElementById('temperatureChart').getContext('2d');
    charts.temperature = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Température',
                data: [],
                borderColor: colors.danger,
                backgroundColor: colors.danger + '20',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: getChartOptions('°C', 'Température au fil du temps')
    });
}

// Graphique d'humidité
function initHumidityChart() {
    const ctx = document.getElementById('humidityChart').getContext('2d');
    charts.humidity = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Humidité',
                data: [],
                borderColor: colors.info,
                backgroundColor: colors.info + '20',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: getChartOptions('%', 'Humidité au fil du temps')
    });
}

// Graphique multicapteurs
function initMultiChart() {
    const ctx = document.getElementById('multiChart').getContext('2d');
    charts.multi = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: []
        },
        options: getChartOptions('Valeurs', 'Évolution multicapteurs')
    });
}

// Graphique en secteurs des actionneurs
function initActuatorsPieChart() {
    const ctx = document.getElementById('actuatorsPieChart').getContext('2d');
    charts.actuatorsPie = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Actifs', 'Inactifs'],
            datasets: [{
                data: [0, 0],
                backgroundColor: [colors.success, colors.secondary],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Graphique de corrélation
function initCorrelationChart() {
    const ctx = document.getElementById('correlationChart').getContext('2d');
    charts.correlation = new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Données',
                data: [],
                backgroundColor: colors.accent,
                borderColor: colors.primary
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Analyse de corrélation'
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom'
                }
            }
        }
    });
}

// Options communes pour les graphiques
function getChartOptions(unit, title) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: title
            },
            legend: {
                display: document.getElementById('showLegend')?.checked !== false
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                title: {
                    display: true,
                    text: unit
                },
                grid: {
                    display: document.getElementById('showGrid')?.checked !== false
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Temps'
                },
                grid: {
                    display: document.getElementById('showGrid')?.checked !== false
                }
            }
        },
        animation: {
            duration: parseInt(document.getElementById('animationSpeed')?.value || 1000)
        }
    };
}

// Charger les données des capteurs
async function loadSensorsData() {
    try {
        const response = await fetch('<?= BASE_URL ?>?controller=api&action=sensors');
        const data = await response.json();
        
        if (data.success) {
            sensorsData = data.data;
            updateSensorSelects();
            updateRealtimeData();
            updateTemperatureChart();
            updateHumidityChart();
        }
    } catch (error) {
        console.error('Erreur lors du chargement des capteurs:', error);
        updateSystemStatus('error');
    }
}

// Charger les données des actionneurs
async function loadActuatorsData() {
    try {
        const response = await fetch('<?= BASE_URL ?>?controller=api&action=actuators');
        const data = await response.json();
        
        if (data.success) {
            actuatorsData = data.data;
            updateActuatorsPieChart();
        }
    } catch (error) {
        console.error('Erreur lors du chargement des actionneurs:', error);
    }
}

// Mettre à jour le graphique de température
async function updateTemperatureChart() {
    const tempSensors = sensorsData.filter(s => s.type === 'temperature');
    if (tempSensors.length === 0) return;
    
    try {
        const response = await fetch(`<?= BASE_URL ?>?controller=api&action=chartData&sensor_id=${tempSensors[0].id}&period=${currentPeriod}&interval=hour`);
        const data = await response.json();
        
        if (data.success) {
            charts.temperature.data.labels = data.chartData.labels;
            charts.temperature.data.datasets[0].data = data.chartData.datasets[0].data;
            charts.temperature.update('none');
            
            // Mettre à jour les statistiques
            updateTemperatureStats(data.chartData.datasets[0].data);
        }
    } catch (error) {
        console.error('Erreur mise à jour température:', error);
    }
}

// Mettre à jour le graphique d'humidité
async function updateHumidityChart() {
    const humSensors = sensorsData.filter(s => s.type === 'humidity');
    if (humSensors.length === 0) return;
    
    try {
        const response = await fetch(`<?= BASE_URL ?>?controller=api&action=chartData&sensor_id=${humSensors[0].id}&period=${currentPeriod}&interval=hour`);
        const data = await response.json();
        
        if (data.success) {
            charts.humidity.data.labels = data.chartData.labels;
            charts.humidity.data.datasets[0].data = data.chartData.datasets[0].data;
            charts.humidity.update('none');
            
            // Mettre à jour les statistiques
            updateHumidityStats(data.chartData.datasets[0].data);
        }
    } catch (error) {
        console.error('Erreur mise à jour humidité:', error);
    }
}

// Mettre à jour le graphique des actionneurs
function updateActuatorsPieChart() {
    const activeCount = actuatorsData.filter(a => a.current_state).length;
    const inactiveCount = actuatorsData.length - activeCount;
    
    charts.actuatorsPie.data.datasets[0].data = [activeCount, inactiveCount];
    charts.actuatorsPie.update('none');
    
    document.getElementById('activeActuators').textContent = activeCount;
    document.getElementById('inactiveActuators').textContent = inactiveCount;
}

// Mettre à jour les sélecteurs de capteurs
function updateSensorSelects() {
    const multiSelect = document.getElementById('chartSensors');
    const corrXSelect = document.getElementById('correlationX');
    const corrYSelect = document.getElementById('correlationY');
    
    const selects = [multiSelect, corrXSelect, corrYSelect];
    
    selects.forEach(select => {
        select.innerHTML = '';
        sensorsData.forEach(sensor => {
            const option = document.createElement('option');
            option.value = sensor.id;
            option.textContent = `${sensor.name} (${sensor.type})`;
            select.appendChild(option);
        });
    });
    
    // Sélectionner les premiers capteurs par défaut
    if (sensorsData.length >= 2) {
        corrXSelect.value = sensorsData[0].id;
        corrYSelect.value = sensorsData[1].id;
    }
}

// Mettre à jour les données temps réel
function updateRealtimeData() {
    const container = document.getElementById('realtimeData');
    container.innerHTML = '';
    
    sensorsData.forEach(sensor => {
        const col = document.createElement('div');
        col.className = 'col-md-3 mb-3';
        
        const value = sensor.value !== null ? sensor.value.toFixed(1) : '--';
        const status = getValueStatus(sensor.type, sensor.value);
        
        col.innerHTML = `
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="mb-2">${getTypeIcon(sensor.type)}</div>
                    <h6 class="card-title">${sensor.name}</h6>
                    <h4 class="mb-1 ${status.class}">${value} ${sensor.unit}</h4>
                    <small class="text-muted">${sensor.timestamp ? timeAgo(sensor.timestamp) : 'Pas de données'}</small>
                </div>
            </div>
        `;
        
        container.appendChild(col);
    });
}

// Mettre à jour les statistiques de température
function updateTemperatureStats(data) {
    const values = data.filter(v => v !== null);
    if (values.length === 0) return;
    
    const avg = values.reduce((a, b) => a + b, 0) / values.length;
    const min = Math.min(...values);
    const max = Math.max(...values);
    
    document.getElementById('tempAvg').textContent = avg.toFixed(1) + '°C';
    document.getElementById('tempMin').textContent = min.toFixed(1) + '°C';
    document.getElementById('tempMax').textContent = max.toFixed(1) + '°C';
}

// Mettre à jour les statistiques d'humidité
function updateHumidityStats(data) {
    const values = data.filter(v => v !== null);
    if (values.length === 0) return;
    
    const avg = values.reduce((a, b) => a + b, 0) / values.length;
    const min = Math.min(...values);
    const max = Math.max(...values);
    
    document.getElementById('humAvg').textContent = avg.toFixed(1) + '%';
    document.getElementById('humMin').textContent = min.toFixed(1) + '%';
    document.getElementById('humMax').textContent = max.toFixed(1) + '%';
}

// Obtenir le statut d'une valeur
function getValueStatus(type, value) {
    if (value === null) return { class: 'text-muted', status: 'unknown' };
    
    switch (type) {
        case 'temperature':
            if (value < 15 || value > 35) return { class: 'text-danger', status: 'critical' };
            if (value < 18 || value > 30) return { class: 'text-warning', status: 'warning' };
            return { class: 'text-success', status: 'normal' };
        case 'humidity':
        case 'soil_moisture':
            if (value < 30) return { class: 'text-danger', status: 'critical' };
            if (value > 90) return { class: 'text-warning', status: 'warning' };
            return { class: 'text-success', status: 'normal' };
        default:
            return { class: 'text-primary', status: 'normal' };
    }
}

// Obtenir l'icône selon le type
function getTypeIcon(type) {
    const icons = {
        'temperature': '🌡️',
        'humidity': '💧',
        'soil_moisture': '🌱',
        'light': '☀️',
        'ph': '🧪',
        'co2': '🌬️'
    };
    return icons[type] || '📊';
}

// Fonctions utilitaires
function timeAgo(timestamp) {
    const now = new Date();
    const time = new Date(timestamp);
    const diff = Math.floor((now - time) / 1000);
    
    if (diff < 60) return 'À l\'instant';
    if (diff < 3600) return Math.floor(diff / 60) + ' min';
    if (diff < 86400) return Math.floor(diff / 3600) + ' h';
    return Math.floor(diff / 86400) + ' j';
}

// Fonctions d'interaction
function updateAllCharts() {
    currentPeriod = document.getElementById('globalPeriod').value;
    loadSensorsData();
    loadActuatorsData();
}

function refreshAllCharts() {
    loadSensorsData();
    loadActuatorsData();
    updateSystemStatus('online');
    document.getElementById('lastUpdate').textContent = 'Mis à jour: ' + new Date().toLocaleTimeString();
}

function setAutoRefresh() {
    const interval = parseInt(document.getElementById('refreshInterval').value);
    
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    
    if (interval > 0) {
        autoRefreshInterval = setInterval(refreshAllCharts, interval * 1000);
    }
}

function updateSystemStatus(status) {
    const indicator = document.getElementById('systemStatus');
    const text = document.getElementById('systemStatusText');
    
    switch (status) {
        case 'online':
            indicator.className = 'status-indicator status-on me-2';
            text.textContent = 'En ligne';
            break;
        case 'error':
            indicator.className = 'status-indicator status-off me-2';
            text.textContent = 'Hors ligne';
            break;
    }
}

function toggleChartType(chartId) {
    const chart = charts[chartId.replace('Chart', '')];
    if (!chart) return;
    
    chart.config.type = chart.config.type === 'line' ? 'bar' : 'line';
    chart.update();
}

function exportChart(chartId) {
    const chart = charts[chartId.replace('Chart', '')];
    if (!chart) return;
    
    const url = chart.toBase64Image();
    const link = document.createElement('a');
    link.download = chartId + '_' + new Date().toISOString().slice(0, 10) + '.png';
    link.href = url;
    link.click();
}

function exportAllData() {
    // Créer un CSV avec toutes les données
    let csv = 'Capteur,Type,Valeur,Unité,Équipe,Timestamp\n';
    
    sensorsData.forEach(sensor => {
        csv += `"${sensor.name}","${sensor.type}",${sensor.value || ''},"${sensor.unit}","${sensor.team_name || ''}","${sensor.timestamp || ''}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.download = 'donnees_serre_' + new Date().toISOString().slice(0, 10) + '.csv';
    link.href = url;
    link.click();
    window.URL.revokeObjectURL(url);
}

function applySettings() {
    const theme = document.getElementById('themeSelect').value;
    const speed = parseInt(document.getElementById('animationSpeed').value);
    const showGrid = document.getElementById('showGrid').checked;
    const showLegend = document.getElementById('showLegend').checked;
    const ecoMode = document.getElementById('ecoMode').checked;
    
    // Appliquer les paramètres à tous les graphiques
    Object.values(charts).forEach(chart => {
        if (chart.options.animation) {
            chart.options.animation.duration = ecoMode ? 0 : speed;
        }
        
        if (chart.options.scales) {
            Object.values(chart.options.scales).forEach(scale => {
                if (scale.grid) scale.grid.display = showGrid;
            });
        }
        
        if (chart.options.plugins && chart.options.plugins.legend) {
            chart.options.plugins.legend.display = showLegend;
        }
        
        chart.update();
    });
    
    // Fermer le modal
    bootstrap.Modal.getInstance(document.getElementById('settingsModal')).hide();
    
    showNotification('Paramètres appliqués avec succès', 'success');
}

// Gestion de la corrélation
function updateCorrelationChart() {
    const xSensorId = document.getElementById('correlationX').value;
    const ySensorId = document.getElementById('correlationY').value;
    
    if (!xSensorId || !ySensorId || xSensorId === ySensorId) return;
    
    // Cette fonction nécessiterait des données historiques
    // Pour l'instant, on simule des données
    const data = [];
    for (let i = 0; i < 50; i++) {
        data.push({
            x: Math.random() * 100,
            y: Math.random() * 100
        });
    }
    
    charts.correlation.data.datasets[0].data = data;
    charts.correlation.update();
    
    // Calculer le coefficient de corrélation (simulé)
    const r2 = Math.random().toFixed(3);
    document.getElementById('correlationCoef').textContent = `R² = ${r2}`;
}

// Initialiser l'auto-refresh au démarrage
setTimeout(() => {
    refreshAllCharts();
}, 1000);
</script>

<style>
/* Styles pour les graphiques */
.chart-container {
    position: relative;
    height: 300px;
}

.status-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

/* Mode éco-responsable */
@media (prefers-reduced-motion: reduce) {
    .status-indicator {
        animation: none;
    }
}

/* Responsive pour les graphiques */
@media (max-width: 768px) {
    .chart-container {
        height: 250px;
    }
}
</style>