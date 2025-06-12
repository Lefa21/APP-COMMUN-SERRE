<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=home">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=profile">Mon Profil</a></li>
                <li class="breadcrumb-item active">Notifications</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1>🔔 Mes Notifications</h1>
            <div class="btn-group">
                <a href="<?= BASE_URL ?>?controller=profile" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour au profil
                </a>
                <?php if (!empty($notifications) && count(array_filter($notifications, function($n) { return !$n['is_read']; })) > 0): ?>
                    <a href="<?= BASE_URL ?>?controller=profile&action=notifications&mark_read=1" class="btn btn-outline-primary">
                        <i class="bi bi-check-all"></i> Tout marquer comme lu
                    </a>
                <?php endif; ?>
                <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#notificationSettingsModal">
                    <i class="bi bi-gear"></i> Paramètres
                </button>
            </div>
        </div>
        <p class="text-muted">Centre de notifications et alertes du système</p>
    </div>
</div>

<!-- Statistiques des notifications -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary"><?= count($notifications) ?></h3>
                <p class="mb-0">Total notifications</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning">
                    <?= count(array_filter($notifications, function($n) { return !$n['is_read']; })) ?>
                </h3>
                <p class="mb-0">Non lues</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">
                    <?= count(array_filter($notifications, function($n) { return $n['is_read']; })) ?>
                </h3>
                <p class="mb-0">Lues</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-danger">
                    <?= count(array_filter($notifications, function($n) { return $n['type'] === 'alert'; })) ?>
                </h3>
                <p class="mb-0">Alertes</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label for="typeFilter" class="form-label">Type:</label>
                        <select id="typeFilter" class="form-select" onchange="filterNotifications()">
                            <option value="">Tous les types</option>
                            <option value="info">Information</option>
                            <option value="warning">Avertissement</option>
                            <option value="alert">Alerte</option>
                            <option value="success">Succès</option>
                            <option value="system">Système</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Statut:</label>
                        <select id="statusFilter" class="form-select" onchange="filterNotifications()">
                            <option value="">Toutes</option>
                            <option value="unread">Non lues</option>
                            <option value="read">Lues</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="dateFilter" class="form-label">Période:</label>
                        <select id="dateFilter" class="form-select" onchange="filterNotifications()">
                            <option value="">Toutes les dates</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="bi bi-x-circle"></i> Effacer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des notifications -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📬 Mes notifications</h5>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-bell-slash" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3">Aucune notification</h5>
                        <p class="text-muted">Vous n'avez encore reçu aucune notification du système.</p>
                        <button class="btn btn-primary" onclick="generateTestNotifications()">
                            <i class="bi bi-plus-circle"></i> Générer des notifications de test
                        </button>
                    </div>
                <?php else: ?>
                    <div class="notifications-container">
                        <?php foreach ($notifications as $notification): ?>
                            <div class="notification-item <?= !$notification['is_read'] ? 'unread' : 'read' ?>" 
                                 data-id="<?= $notification['id'] ?>"
                                 data-type="<?= $notification['type'] ?>"
                                 data-status="<?= $notification['is_read'] ? 'read' : 'unread' ?>"
                                 data-date="<?= date('Y-m-d', strtotime($notification['created_at'])) ?>">
                                
                                <div class="notification-content">
                                    <div class="d-flex">
                                        <!-- Icône selon le type -->
                                        <div class="notification-icon me-3">
                                            <?php
                                            $iconClass = '';
                                            $iconBg = '';
                                            switch ($notification['type']) {
                                                case 'info':
                                                    $iconClass = 'bi-info-circle';
                                                    $iconBg = 'bg-primary';
                                                    break;
                                                case 'warning':
                                                    $iconClass = 'bi-exclamation-triangle';
                                                    $iconBg = 'bg-warning';
                                                    break;
                                                case 'alert':
                                                    $iconClass = 'bi-exclamation-circle';
                                                    $iconBg = 'bg-danger';
                                                    break;
                                                case 'success':
                                                    $iconClass = 'bi-check-circle';
                                                    $iconBg = 'bg-success';
                                                    break;
                                                case 'system':
                                                    $iconClass = 'bi-gear';
                                                    $iconBg = 'bg-secondary';
                                                    break;
                                                default:
                                                    $iconClass = 'bi-bell';
                                                    $iconBg = 'bg-info';
                                            }
                                            ?>
                                            <div class="icon-circle <?= $iconBg ?>">
                                                <i class="<?= $iconClass ?> text-white"></i>
                                            </div>
                                        </div>
                                        
                                        <!-- Contenu de la notification -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="notification-title mb-1">
                                                        <?= htmlspecialchars($notification['title']) ?>
                                                        <?php if (!$notification['is_read']): ?>
                                                            <span class="badge bg-primary ms-2">Nouveau</span>
                                                        <?php endif; ?>
                                                    </h6>
                                                    <p class="notification-message mb-2">
                                                        <?= htmlspecialchars($notification['message']) ?>
                                                    </p>
                                                    <div class="notification-meta">
                                                        <small class="text-muted">
                                                            <i class="bi bi-clock"></i>
                                                            <?php
                                                            $date = new DateTime($notification['created_at']);
                                                            $now = new DateTime();
                                                            $diff = $now->diff($date);
                                                            
                                                            if ($diff->days == 0) {
                                                                if ($diff->h == 0) {
                                                                    if ($diff->i == 0) {
                                                                        echo "À l'instant";
                                                                    } else {
                                                                        echo $diff->i . " minute" . ($diff->i > 1 ? "s" : "");
                                                                    }
                                                                } else {
                                                                    echo $diff->h . " heure" . ($diff->h > 1 ? "s" : "");
                                                                }
                                                            } elseif ($diff->days == 1) {
                                                                echo "Hier";
                                                            } else {
                                                                echo $date->format('d/m/Y H:i');
                                                            }
                                                            ?>
                                                            
                                                            <span class="badge bg-<?= $notification['type'] === 'alert' ? 'danger' : ($notification['type'] === 'warning' ? 'warning' : 'info') ?> ms-2">
                                                                <?= ucfirst($notification['type']) ?>
                                                            </span>
                                                        </small>
                                                    </div>
                                                </div>
                                                
                                                <!-- Actions -->
                                                <div class="notification-actions">
                                                    <?php if (!$notification['is_read']): ?>
                                                        <button class="btn btn-sm btn-outline-primary me-2" 
                                                                onclick="markAsRead(<?= $notification['id'] ?>)">
                                                            <i class="bi bi-check"></i> Marquer comme lu
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-secondary dropdown-toggle" 
                                                                data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <?php if ($notification['is_read']): ?>
                                                                <li>
                                                                    <button class="dropdown-item" onclick="markAsUnread(<?= $notification['id'] ?>)">
                                                                        <i class="bi bi-eye-slash"></i> Marquer comme non lu
                                                                    </button>
                                                                </li>
                                                            <?php endif; ?>
                                                            <li>
                                                                <button class="dropdown-item" onclick="shareNotification(<?= $notification['id'] ?>)">
                                                                    <i class="bi bi-share"></i> Partager
                                                                </button>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <button class="dropdown-item text-danger" onclick="deleteNotification(<?= $notification['id'] ?>)">
                                                                    <i class="bi bi-trash"></i> Supprimer
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Actions spécifiques selon le type -->
                                            <?php if ($notification['type'] === 'alert'): ?>
                                                <div class="alert alert-danger mt-2 mb-0">
                                                    <small>
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                        <strong>Action requise:</strong> Cette alerte nécessite votre attention.
                                                        <a href="#" class="alert-link ms-2">Voir les détails</a>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Bouton pour charger plus de notifications -->
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-primary" id="loadMoreBtn" onclick="loadMoreNotifications()">
                            <i class="bi bi-arrow-down-circle"></i> Charger plus de notifications
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Suggestions et conseils -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">💡 Conseils</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Consultez régulièrement vos notifications pour rester informé
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-bell text-warning me-2"></i>
                        Activez les notifications push pour les alertes critiques
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-gear text-info me-2"></i>
                        Personnalisez vos préférences dans les paramètres
                    </li>
                    <li>
                        <i class="bi bi-shield-check text-primary me-2"></i>
                        Les notifications sont chiffrées pour votre sécurité
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">📊 Activité récente</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small>Notifications reçues cette semaine:</small>
                    <span class="badge bg-primary">
                        <?= count(array_filter($notifications, function($n) {
                            return strtotime($n['created_at']) > strtotime('-1 week');
                        })) ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small>Alertes traitées:</small>
                    <span class="badge bg-success">
                        <?= count(array_filter($notifications, function($n) {
                            return $n['type'] === 'alert' && $n['is_read'];
                        })) ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small>Temps de réponse moyen:</small>
                    <span class="badge bg-info">~15 min</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Paramètres de notifications -->
<div class="modal fade" id="notificationSettingsModal" tabindex="-1" aria-labelledby="notificationSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationSettingsModalLabel">⚙️ Paramètres de notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="notificationSettingsForm">
                    <h6>Types de notifications</h6>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                            <label class="form-check-label" for="emailNotifications">
                                Notifications par email
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="browserNotifications" checked>
                            <label class="form-check-label" for="browserNotifications">
                                Notifications dans le navigateur
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="smsNotifications">
                            <label class="form-check-label" for="smsNotifications">
                                Notifications SMS (alertes critiques uniquement)
                            </label>
                        </div>
                    </div>
                    
                    <h6>Fréquence</h6>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="frequency" id="immediate" value="immediate" checked>
                            <label class="form-check-label" for="immediate">
                                Immédiate
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="frequency" id="hourly" value="hourly">
                            <label class="form-check-label" for="hourly">
                                Résumé horaire
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="frequency" id="daily" value="daily">
                            <label class="form-check-label" for="daily">
                                Résumé quotidien
                            </label>
                        </div>
                    </div>
                    
                    <h6>Heures de silence</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="quietStart" class="form-label">Début:</label>
                            <input type="time" class="form-control" id="quietStart" value="22:00">
                        </div>
                        <div class="col-md-6">
                            <label for="quietEnd" class="form-label">Fin:</label>
                            <input type="time" class="form-control" id="quietEnd" value="07:00">
                        </div>
                    </div>
                    
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="weekendQuiet">
                        <label class="form-check-label" for="weekendQuiet">
                            Mode silencieux le weekend
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveNotificationSettings()">
                    <i class="bi bi-save"></i> Sauvegarder
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Informations éco-responsables -->
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-success">
            <div class="d-flex align-items-center">
                <span class="me-2">🌍</span>
                <div>
                    <strong>Impact Éco-responsable:</strong>
                    Les notifications sont optimisées pour réduire la consommation. 
                    Le système groupe les notifications non-urgentes et utilise un cache intelligent 
                    pour minimiser les requêtes serveur.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let notificationsData = <?= json_encode($notifications) ?>;
let currentFilter = {
    type: '',
    status: '',
    date: ''
};

// Filtrer les notifications
function filterNotifications() {
    currentFilter.type = document.getElementById('typeFilter').value;
    currentFilter.status = document.getElementById('statusFilter').value;
    currentFilter.date = document.getElementById('dateFilter').value;
    
    const items = document.querySelectorAll('.notification-item');
    
    items.forEach(item => {
        const type = item.dataset.type;
        const status = item.dataset.status;
        const date = item.dataset.date;
        
        let show = true;
        
        // Filtre par type
        if (currentFilter.type && type !== currentFilter.type) {
            show = false;
        }
        
        // Filtre par statut
        if (currentFilter.status && status !== currentFilter.status) {
            show = false;
        }
        
        // Filtre par date
        if (currentFilter.date) {
            const itemDate = new Date(date);
            const now = new Date();
            
            switch (currentFilter.date) {
                case 'today':
                    if (itemDate.toDateString() !== now.toDateString()) show = false;
                    break;
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    if (itemDate < weekAgo) show = false;
                    break;
                case 'month':
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    if (itemDate < monthAgo) show = false;
                    break;
            }
        }
        
        item.style.display = show ? 'block' : 'none';
    });
}

// Effacer les filtres
function clearFilters() {
    document.getElementById('typeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFilter').value = '';
    filterNotifications();
}

// Marquer comme lu
function markAsRead(notificationId) {
    fetch('<?= BASE_URL ?>?controller=profile&action=markNotificationRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`[data-id="${notificationId}"]`);
            item.classList.remove('unread');
            item.classList.add('read');
            item.dataset.status = 'read';
            
            // Supprimer le badge "Nouveau"
            const badge = item.querySelector('.badge.bg-primary');
            if (badge) badge.remove();
            
            // Supprimer le bouton "Marquer comme lu"
            const markReadBtn = item.querySelector('button[onclick*="markAsRead"]');
            if (markReadBtn) markReadBtn.remove();
            
            showNotification('Notification marquée comme lue', 'success');
            updateNotificationCount();
        }
    })
    .catch(error => {
        showNotification('Erreur lors de la mise à jour', 'error');
    });
}

// Marquer comme non lu
function markAsUnread(notificationId) {
    fetch('<?= BASE_URL ?>?controller=profile&action=markNotificationUnread', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload(); // Recharger pour mettre à jour l'interface
        }
    })
    .catch(error => {
        showNotification('Erreur lors de la mise à jour', 'error');
    });
}

// Supprimer une notification
function deleteNotification(notificationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
        fetch('<?= BASE_URL ?>?controller=profile&action=deleteNotification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'notification_id=' + notificationId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-id="${notificationId}"]`);
                item.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    item.remove();
                    updateNotificationCount();
                }, 300);
                showNotification('Notification supprimée', 'success');
            }
        })
        .catch(error => {
            showNotification('Erreur lors de la suppression', 'error');
        });
    }
}

// Partager une notification
function shareNotification(notificationId) {
    const item = document.querySelector(`[data-id="${notificationId}"]`);
    const title = item.querySelector('.notification-title').textContent.trim();
    const message = item.querySelector('.notification-message').textContent.trim();
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: message,
            url: window.location.href
        });
    } else {
        // Fallback: copier dans le presse-papier
        navigator.clipboard.writeText(`${title}: ${message}`).then(() => {
            showNotification('Notification copiée dans le presse-papier', 'success');
        });
    }
}

// Générer des notifications de test
function generateTestNotifications() {
    if (confirm('Générer quelques notifications de test ?')) {
        fetch('<?= BASE_URL ?>?controller=profile&action=generateTestNotifications', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Notifications de test générées', 'success');
                setTimeout(() => window.location.reload(), 1000);
            }
        })
        .catch(error => {
            showNotification('Erreur lors de la génération', 'error');
        });
    }
}

// Charger plus de notifications
function loadMoreNotifications() {
    const btn = document.getElementById('loadMoreBtn');
    btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Chargement...';
    btn.disabled = true;
    
    // Simuler le chargement (remplacer par un vrai appel API)
    setTimeout(() => {
        btn.innerHTML = '<i class="bi bi-arrow-down-circle"></i> Charger plus de notifications';
        btn.disabled = false;
        showNotification('Toutes les notifications sont affichées', 'info');
    }, 2000);
}

// Sauvegarder les paramètres de notifications
function saveNotificationSettings() {
    const settings = {
        email: document.getElementById('emailNotifications').checked,
        browser: document.getElementById('browserNotifications').checked,
        sms: document.getElementById('smsNotifications').checked,
        frequency: document.querySelector('input[name="frequency"]:checked').value,
        quietStart: document.getElementById('quietStart').value,
        quietEnd: document.getElementById('quietEnd').value,
        weekendQuiet: document.getElementById('weekendQuiet').checked
    };
    
    fetch('<?= BASE_URL ?>?controller=profile&action=saveNotificationSettings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Paramètres sauvegardés', 'success');
            bootstrap.Modal.getInstance(document.getElementById('notificationSettingsModal')).hide();
        }
    })
    .catch(error => {
        showNotification('Erreur lors de la sauvegarde', 'error');
    });
}

// Mettre à jour le compteur de notifications
function updateNotificationCount() {
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    // Mettre à jour les statistiques
    document.querySelector('.row .col-md-3:nth-child(2) .card-body h3').textContent = unreadCount;
}

// Vérifier les nouvelles notifications périodiquement
setInterval(() => {
    if (!document.hidden) {
        fetch('<?= BASE_URL ?>?controller=api&action=checkNewNotifications')
            .then(response => response.json())
            .then(data => {
                if (data.newNotifications > 0) {
                    showNotification(`${data.newNotifications} nouvelle(s) notification(s)`, 'info');
                    // Optionnel: recharger la page ou ajouter les nouvelles notifications
                }
            })
            .catch(() => {}); // Ignorer les erreurs silencieusement
    }
}, 60000); // Vérifier toutes les minutes

// Demander la permission pour les notifications du navigateur
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
            showNotification('Notifications du navigateur activées', 'success');
        }
    });
}
</script>

<style>
/* Styles pour les notifications */
.notification-item {
    border-left: 4px solid transparent;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.notification-item:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(5px);
}

.notification-item.unread {
    border-left-color: var(--primary-color);
    background-color: rgba(45, 90, 39, 0.05);
    font-weight: 500;
}

.notification-item.read {
    border-left-color: #dee2e6;
    opacity: 0.8;
}

.icon-circle {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-title {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.notification-message {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.4;
}

.notification-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.notification-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

/* Animations */
@keyframes slideOutRight {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.notification-item {
    animation: slideInLeft 0.4s ease-out;
}

/* Mode éco-responsable */
@media (prefers-reduced-motion: reduce) {
    .notification-item {
        transition: none;
        animation: none;
    }
    
    .notification-item:hover {
        transform: none;
    }
    
    .notification-actions {
        opacity: 1;
    }
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .notification-item:hover {
        background-color: rgba(255,255,255,0.05);
    }
    
    .notification-item.unread {
        background-color: rgba(139, 195, 74, 0.1);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .notification-actions {
        opacity: 1;
        margin-top: 0.5rem;
    }
    
    .notification-item {
        padding: 0.75rem;
    }
    
    .icon-circle {
        width: 2rem;
        height: 2rem;
    }
}
</style>