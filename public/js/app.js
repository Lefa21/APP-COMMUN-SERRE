// public/js/app.js

// --- Fonctions globales (depuis layout.php) ---

/**
 * Affiche une notification toast en bas à droite.
 * @param {string} message Le message à afficher.
 * @param {string} type 'success' ou 'error'.
 */
function showNotification(message, type = 'info') {
    const alertClass = {
        success: 'alert-success',
        error: 'alert-danger',
        info: 'alert-info'
    }[type];

    const iconClass = {
        success: 'bi-check-circle',
        error: 'bi-exclamation-triangle',
        info: 'bi-info-circle'
    }[type];

    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 80px; right: 20px; z-index: 1050;" role="alert">
            <i class="bi ${iconClass}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-destruction de l'alerte
    const alertElement = document.body.lastElementChild;
    setTimeout(() => {
        if (alertElement && alertElement.classList.contains('alert')) {
            bootstrap.Alert.getOrCreateInstance(alertElement).close();
        }
    }, 5000);
}


/**
 * Gère l'action sur un actionneur via un appel API.
 * @param {number} actuatorId ID de l'actionneur.
 * @param {string} action 'ON' ou 'OFF'.
 */
function toggleActuator(actuatorId, action) {
    fetch('?controller=actuator&action=toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `actuator_id=${actuatorId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Recharger la page pour voir les changements
            setTimeout(() => window.location.reload(), 500); 
        } else {
            showNotification(data.error || 'Une erreur est survenue', 'error');
        }
    })
    .catch(() => showNotification('Erreur de communication', 'error'));
}


// --- Fonctions spécifiques aux pages ---

// Pour views/sensors/manage.php
function initSensorManagement(sensorsData) {
    window.filterSensors = () => {
        const team = document.getElementById('teamFilter').value;
        const type = document.getElementById('typeFilter').value;
        const status = document.getElementById('statusFilter').value;
        document.querySelectorAll('.sensor-row').forEach(row => {
            const teamMatch = !team || row.dataset.team === team;
            const typeMatch = !type || row.dataset.type === type;
            const statusMatch = !status || row.dataset.status === status;
            row.style.display = (teamMatch && typeMatch && statusMatch) ? '' : 'none';
        });
    };

    window.editSensor = (id) => {
        const sensor = sensorsData.find(s => s.id == id);
        if (!sensor) return;
        document.getElementById('editSensorId').value = sensor.id;
        document.getElementById('editSensorName').value = sensor.name;
        document.getElementById('editSensorType').value = sensor.type;
        document.getElementById('editSensorUnit').value = sensor.unit;
        document.getElementById('editSensorActive').checked = sensor.is_active == 1;
        new bootstrap.Modal(document.getElementById('editSensorModal')).show();
    };

    window.deleteSensor = (id, name) => {
        if (confirm(`Supprimer le capteur "${name}" et toutes ses données ?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="management_action" value="delete"><input type="hidden" name="sensor_id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    };
    
    // Auto-complétion de l'unité
    document.getElementById('sensorType')?.addEventListener('change', function() {
        const unitField = document.getElementById('sensorUnit');
        const units = { temperature: '°C', humidity: '%', soil_moisture: '%', light: '%', ph: 'pH', co2: 'ppm' };
        if (units[this.value]) unitField.value = units[this.value];
    });
}


// Pour views/sensors/details.php et autres pages avec graphiques
function initChart(canvasId, chartData, sensorName, unit) {
    const ctx = document.getElementById(canvasId)?.getContext('2d');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => new Date(item.time_group).toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})),
            datasets: [{
                label: sensorName,
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
            scales: { y: { title: { display: true, text: unit } } }
        }
    });
}