<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>👤 Mon Profil</h1>
            <div class="btn-group">
                <a href="<?= BASE_URL ?>?controller=profile&action=activity" class="btn btn-outline-info">
                    <i class="bi bi-activity"></i> Mon activité
                </a>
                <a href="<?= BASE_URL ?>?controller=profile&action=notifications" class="btn btn-outline-warning">
                    <i class="bi bi-bell"></i> Notifications
                </a>
            </div>
        </div>
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

<!-- Informations générales -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📋 Informations personnelles</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>?controller=profile&action=update">
                    <input type="hidden" name="action" value="update_info">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" 
                                       readonly>
                                <div class="form-text">Le nom d'utilisateur ne peut pas être modifié</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Mettre à jour les informations
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📊 Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <h4 class="text-primary"><?= $stats['total_actions'] ?></h4>
                        <small>Actions effectuées</small>
                    </div>
                    <div class="col-12 mb-3">
                        <h4 class="text-success"><?= $stats['actuators_controlled'] ?></h4>
                        <small>Actionneurs contrôlés</small>
                    </div>
                    <div class="col-12 mb-3">
                        <h4 class="text-info"><?= $stats['account_age_days'] ?></h4>
                        <small>Jours depuis l'inscription</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Rôle:</small>
                    <span class="badge bg-<?= $user['role_name'] === 'admin' ? 'warning' : 'primary' ?>">
                        <?= ucfirst($user['role_name'] ?? 'Utilisateur') ?>
                    </span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Équipe:</small>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Membre depuis:</small>
                    <small>
                        <?= $user['created_at'] ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sécurité -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🔐 Sécurité</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>?controller=profile&action=update">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel *</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe *</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" 
                               minlength="6" required>
                        <div class="form-text">Au moins 6 caractères</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe *</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               minlength="6" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-shield-check"></i> Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">⚙️ Préférences</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>?controller=profile&action=update">
                    <input type="hidden" name="action" value="update_preferences">
                    
                    <div class="mb-3">
                        <label class="form-label">Notifications</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notification_email" 
                                   name="notification_email" 
                                   <?= ($user['notification_email'] ?? false) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="notification_email">
                                Recevoir les notifications par email
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notification_browser" 
                                   name="notification_browser" 
                                   <?= ($user['notification_browser'] ?? true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="notification_browser">
                                Notifications dans le navigateur
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="theme_preference" class="form-label">Thème</label>
                        <select class="form-select" id="theme_preference" name="theme_preference">
                            <option value="auto" <?= ($user['theme_preference'] ?? 'auto') === 'auto' ? 'selected' : '' ?>>
                                Automatique (suit le système)
                            </option>
                            <option value="light" <?= ($user['theme_preference'] ?? '') === 'light' ? 'selected' : '' ?>>
                                Clair
                            </option>
                            <option value="dark" <?= ($user['theme_preference'] ?? '') === 'dark' ? 'selected' : '' ?>>
                                Sombre (éco-responsable)
                            </option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-gear"></i> Sauvegarder les préférences
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Zone dangereuse -->
<div class="row">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">⚠️ Zone dangereuse</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-danger">Supprimer mon compte</h6>
                        <p class="mb-0 text-muted">
                            Cette action est irréversible. Toutes vos données seront supprimées.
                        </p>
                    </div>
                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash"></i> Supprimer le compte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression de compte -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">⚠️ Supprimer définitivement le compte</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>Attention !</strong> Cette action est irréversible. Toutes vos données seront supprimées.
                </div>
                
                <form method="POST" action="<?= BASE_URL ?>?controller=profile&action=deleteAccount" id="deleteAccountForm">
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Confirmer avec votre mot de passe :</label>
                        <input type="password" class="form-control" id="delete_password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="delete_confirmation" class="form-label">
                            Tapez <strong>SUPPRIMER</strong> pour confirmer :
                        </label>
                        <input type="text" class="form-control" id="delete_confirmation" name="confirmation" 
                               placeholder="SUPPRIMER" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Validation des mots de passe en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function checkPasswordMatch() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    if (newPassword && confirmPassword) {
        newPassword.addEventListener('input', checkPasswordMatch);
        confirmPassword.addEventListener('input', checkPasswordMatch);
    }
    
    // Validation du formulaire de suppression
    const deleteForm = document.getElementById('deleteAccountForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            const confirmation = document.getElementById('delete_confirmation').value;
            if (confirmation !== 'SUPPRIMER') {
                e.preventDefault();
                alert('Veuillez taper exactement "SUPPRIMER" pour confirmer');
                return false;
            }
            
            if (!confirm('Êtes-vous absolument sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>

<style>
/* Styles spécifiques au profil */
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
}

.badge {
    font-size: 0.75rem;
}

/* Amélioration de l'accessibilité */
.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.25);
}

/* Mode éco-responsable */
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

/* Responsive */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
    }
}
</style>