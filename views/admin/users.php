<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=home">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?controller=admin&action=dashboard">Administration</a></li>
                <li class="breadcrumb-item active">Gestion des utilisateurs</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1>👥 Gestion des Utilisateurs</h1>
            <div class="btn-group">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="bi bi-person-plus"></i> Créer un utilisateur
                </button>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> Exporter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=admin&action=exportUsers&format=csv">
                            <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                        </a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>?controller=admin&action=exportUsers&format=json">
                            <i class="bi bi-file-earmark-code"></i> JSON
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <p class="text-muted">Administration des comptes utilisateurs du système</p>
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

<!-- Affichage des mots de passe réinitialisés -->
<?php if (isset($_SESSION['reset_passwords'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5><i class="bi bi-key"></i> Mots de passe réinitialisés</h5>
        <p>Communiquez ces mots de passe aux utilisateurs concernés :</p>
        <ul class="mb-0">
            <?php foreach ($_SESSION['reset_passwords'] as $userId => $password): ?>
                <li><strong>ID <?= htmlspecialchars($userId) ?>:</strong> <code><?= htmlspecialchars($password) ?></code></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['reset_passwords']); ?>
<?php endif; ?>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary"><?= $stats['total_users'] ?></h3>
                <p class="mb-0">Total utilisateurs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success"><?= $stats['active_users'] ?></h3>
                <p class="mb-0">Utilisateurs actifs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info"><?= $stats['new_users_month'] ?></h3>
                <p class="mb-0">Nouveaux ce mois</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning"><?= $stats['admin_users'] ?></h3>
                <p class="mb-0">Administrateurs</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtres et actions en lot -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label for="roleFilter" class="form-label">Filtrer par rôle:</label>
                        <select id="roleFilter" class="form-select" onchange="filterUsers()">
                            <option value="">Tous les rôles</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['name'] ?>"><?= ucfirst($role['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Filtrer par statut:</label>
                        <select id="statusFilter" class="form-select" onchange="filterUsers()">
                            <option value="">Tous</option>
                            <option value="1">Actifs</option>
                            <option value="0">Inactifs</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="searchFilter" class="form-label">Rechercher:</label>
                        <input type="text" id="searchFilter" class="form-control" placeholder="Nom, email..." 
                               onkeyup="filterUsers()">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <label class="form-label">Actions en lot:</label>
                <form method="POST" action="<?= BASE_URL ?>?controller=admin&action=bulkActions" id="bulkActionsForm">
                    <div class="input-group">
                        <select name="bulk_action" class="form-select" required>
                            <option value="">Choisir une action</option>
                            <option value="activate">Activer</option>
                            <option value="deactivate">Désactiver</option>
                            <option value="reset_passwords">Réinitialiser mots de passe</option>
                            <option value="delete">Supprimer</option>
                        </select>
                        <button type="submit" class="btn btn-warning" onclick="return confirmBulkAction()">
                            Exécuter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Liste des utilisateurs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📋 Liste des utilisateurs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll" 
                                               onchange="toggleAllUsers()">
                                    </div>
                                </th>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Équipe</th>
                                <th>Statut</th>
                                <th>Activité</th>
                                <th>Inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr data-role="<?= $user['role_name'] ?>" 
                                    data-status="<?= $user['is_active'] ?>"
                                    data-search="<?= strtolower($user['username'] . ' ' . $user['email'] . ' ' . $user['first_name'] . ' ' . $user['last_name']) ?>"
                                    class="user-row">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" 
                                                   name="user_ids[]" value="<?= $user['id_user'] ?>"
                                                   <?= $user['id_user'] === $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                <div class="avatar-circle">
                                                    <?= strtoupper(substr($user['username'], 0, 2)) ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($user['username']) ?></div>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'Nom non défini') ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($user['email']): ?>
                                            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($user['email']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Non défini</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $user['role_name'] === 'admin' ? 'warning' : 'primary' ?>">
                                            <?= ucfirst($user['role_name'] ?? 'Aucun') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $user['is_active'] ? 'success' : 'secondary' ?>">
                                            <?= $user['is_active'] ? 'Actif' : 'Inactif' ?>
                                        </span>
                                        <?php if ($user['id_user'] === $_SESSION['user_id']): ?>
                                            <small class="text-muted">(Vous)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <div class="fw-bold text-primary"><?= $user['total_actions'] ?></div>
                                            <small class="text-muted">actions</small>
                                            <?php if ($user['last_activity']): ?>
                                                <div class="small text-muted">
                                                    Dernière: <?= date('d/m H:i', strtotime($user['last_activity'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            <?= $user['created_at'] ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-sm" 
                                                    onclick="editUser('<?= $user['id_user'] ?>')" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            
                                            <?php if ($user['id_user'] !== $_SESSION['user_id']): ?>
                                                <button class="btn btn-outline-warning btn-sm" 
                                                        onclick="resetPassword('<?= $user['id_user'] ?>', '<?= htmlspecialchars($user['username']) ?>')" 
                                                        title="Réinitialiser mot de passe">
                                                    <i class="bi bi-key"></i>
                                                </button>
                                                
                                                <button class="btn btn-outline-<?= $user['is_active'] ? 'secondary' : 'success' ?> btn-sm" 
                                                        onclick="toggleUserStatus('<?= $user['id_user'] ?>', '<?= htmlspecialchars($user['username']) ?>', <?= $user['is_active'] ?>)" 
                                                        title="<?= $user['is_active'] ? 'Désactiver' : 'Activer' ?>">
                                                    <i class="bi bi-<?= $user['is_active'] ? 'pause' : 'play' ?>-circle"></i>
                                                </button>
                                                
                                                <button class="btn btn-outline-danger btn-sm" 
                                                        onclick="deleteUser('<?= $user['id_user'] ?>', '<?= htmlspecialchars($user['username']) ?>')" 
                                                        title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="badge bg-info">Votre compte</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($users)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3">Aucun utilisateur trouvé</h5>
                        <p class="text-muted">Commencez par créer votre premier utilisateur</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="bi bi-person-plus"></i> Créer un utilisateur
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Créer un utilisateur -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">➕ Créer un nouvel utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>?controller=admin&action=manageUser">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createUsername" class="form-label">Nom d'utilisateur *</label>
                                <input type="text" class="form-control" id="createUsername" name="username" required 
                                       minlength="3" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createEmail" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="createEmail" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createFirstName" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="createFirstName" name="first_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createLastName" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="createLastName" name="last_name">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createRole" class="form-label">Rôle *</label>
                                <select class="form-select" id="createRole" name="role_id" required>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>" <?= $role['name'] === 'etudiant' ? 'selected' : '' ?>>
                                            <?= ucfirst($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="createPassword" class="form-label">Mot de passe *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="createPassword" name="password" 
                                   required minlength="6">
                            <button type="button" class="btn btn-outline-secondary" onclick="generatePassword()">
                                <i class="bi bi-arrow-clockwise"></i> Générer
                            </button>
                        </div>
                        <div class="form-text">Au moins 6 caractères</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier un utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">✏️ Modifier l'utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>?controller=admin&action=manageUser" id="editUserForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUsername" class="form-label">Nom d'utilisateur *</label>
                                <input type="text" class="form-control" id="editUsername" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="editEmail" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFirstName" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="editFirstName" name="first_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editLastName" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="editLastName" name="last_name">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editRole" class="form-label">Rôle *</label>
                                <select class="form-select" id="editRole" name="role_id" required>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>"><?= ucfirst($role['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Données des utilisateurs pour JavaScript
const usersData = <?= json_encode($users) ?>;

// Filtrage des utilisateurs
function filterUsers() {
    const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    
    const rows = document.querySelectorAll('.user-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const role = row.dataset.role ? row.dataset.role.toLowerCase() : '';
        const status = row.dataset.status;
        const searchText = row.dataset.search;
        
        const roleMatch = !roleFilter || role.includes(roleFilter);
        const statusMatch = !statusFilter || status === statusFilter;
        const searchMatch = !searchFilter || searchText.includes(searchFilter);
        
        if (roleMatch && statusMatch && searchMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
}

// Sélection en lot
function toggleAllUsers() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Confirmation des actions en lot
function confirmBulkAction() {
    const selectedUsers = document.querySelectorAll('.user-checkbox:checked').length;
    const action = document.querySelector('[name="bulk_action"]').value;
    
    if (selectedUsers === 0) {
        alert('Veuillez sélectionner au moins un utilisateur');
        return false;
    }
    
    const actionText = {
        'activate': 'activer',
        'deactivate': 'désactiver',
        'reset_passwords': 'réinitialiser les mots de passe de',
        'delete': 'supprimer'
    };
    
    return confirm(`Êtes-vous sûr de vouloir ${actionText[action]} ${selectedUsers} utilisateur(s) ?`);
}

// Modifier un utilisateur
function editUser(userId) {
    const user = usersData.find(u => u.id_user === userId);
    if (!user) return;
    
    document.getElementById('editUserId').value = user.id_user;
    document.getElementById('editUsername').value = user.username;
    document.getElementById('editEmail').value = user.email || '';
    document.getElementById('editFirstName').value = user.first_name || '';
    document.getElementById('editLastName').value = user.last_name || '';
    document.getElementById('editRole').value = user.role_id || '';
    
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

// Supprimer un utilisateur
function deleteUser(userId, username) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${username}" ?\n\nCette action désactivera le compte et ne peut pas être annulée.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>?controller=admin&action=manageUser';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="user_id" value="${userId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Réinitialiser le mot de passe
function resetPassword(userId, username) {
    if (confirm(`Réinitialiser le mot de passe de "${username}" ?\n\nUn nouveau mot de passe sera généré automatiquement.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>?controller=admin&action=manageUser';
        form.innerHTML = `
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="user_id" value="${userId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Changer le statut d'un utilisateur
function toggleUserStatus(userId, username, currentStatus) {
    const action = currentStatus ? 'désactiver' : 'activer';
    if (confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} l'utilisateur "${username}" ?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>?controller=admin&action=manageUser';
        form.innerHTML = `
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="user_id" value="${userId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Générer un mot de passe aléatoire
function generatePassword() {
    const length = 8;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    document.getElementById('createPassword').value = password;
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Mise à jour du compteur de sélection
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionCount);
    });
    
    function updateSelectionCount() {
        const selected = document.querySelectorAll('.user-checkbox:checked').length;
        const bulkForm = document.getElementById('bulkActionsForm');
        const bulkButton = bulkForm.querySelector('button[type="submit"]');
        
        if (selected > 0) {
            bulkButton.textContent = `Exécuter (${selected})`;
            bulkButton.disabled = false;
        } else {
            bulkButton.textContent = 'Exécuter';
            bulkButton.disabled = true;
        }
    }
    
    // Initialiser l'état du bouton
    updateSelectionCount();
});
</script>

<style>
/* Styles spécifiques à la gestion des utilisateurs */
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.8rem;
}

.user-row {
    transition: background-color 0.2s ease;
}

.user-row:hover {
    background-color: rgba(45, 90, 39, 0.05);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.4rem;
    font-size: 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: var(--primary-color);
}

.badge {
    font-size: 0.7rem;
}

/* Amélioration de l'accessibilité */
.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.25);
}

.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.25);
}

/* Mode éco-responsable */
@media (prefers-color-scheme: dark) {
    .user-row:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    .avatar-circle {
        background: linear-gradient(45deg, #2d5a27, #5d8a54);
    }
    
    .table th {
        color: #8bc34a;
    }
}

/* Optimisation pour réduire les animations */
@media (prefers-reduced-motion: reduce) {
    .user-row {
        transition: none;
    }
    
    .card {
        transition: none;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }
    
    .btn-group-sm .btn {
        padding: 0.125rem 0.25rem;
        font-size: 0.7rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .avatar-circle {
        width: 30px;
        height: 30px;
        font-size: 0.7rem;
    }
}

/* Amélioration de la lisibilité */
.small {
    font-size: 0.75rem;
}

.text-muted {
    opacity: 0.7;
}

/* Animation de chargement pour les actions */
.btn.loading {
    pointer-events: none;
    position: relative;
}

.btn.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>