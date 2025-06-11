<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h2 class="h4">🌱 Inscription</h2>
                    <p class="text-muted">Rejoignez votre équipe de gestion de serres</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <?= htmlspecialchars($success) ?>
                        <div class="mt-2">
                            <a href="<?= BASE_URL ?>?controller=auth&action=login" class="btn btn-success btn-sm">
                                Se connecter maintenant
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>?controller=auth&action=register">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person"></i> Nom d'utilisateur *
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="username" 
                                    name="username" 
                                    required 
                                    minlength="3"
                                    maxlength="50"
                                    autocomplete="username"
                                    value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                                >
                                <div class="form-text">Au moins 3 caractères</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope"></i> Email *
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    required 
                                    autocomplete="email"
                                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Mot de passe *
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    minlength="6"
                                    autocomplete="new-password"
                                >
                                <div class="form-text">Au moins 6 caractères</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="bi bi-lock-fill"></i> Confirmer le mot de passe *
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    required 
                                    minlength="6"
                                    autocomplete="new-password"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="team_id" class="form-label">
                            <i class="bi bi-people"></i> Équipe *
                        </label>
                        <select class="form-select" id="team_id" name="team_id" required>
                            <option value="">Choisissez votre équipe</option>
                            <?php foreach ($teams as $team): ?>
                                <option value="<?= $team['id'] ?>" 
                                    <?= (isset($_POST['team_id']) && $_POST['team_id'] == $team['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($team['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Sélectionnez l'équipe à laquelle vous appartenez</div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            J'accepte les <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">conditions d'utilisation</a> 
                            et la politique de confidentialité *
                        </label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="eco_commitment" name="eco_commitment">
                        <label class="form-check-label" for="eco_commitment">
                            🌍 Je m'engage à utiliser ce service de manière éco-responsable
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-person-plus"></i> Créer mon compte
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="mb-0">Déjà un compte ?</p>
                    <a href="<?= BASE_URL ?>?controller=auth&action=login" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </a>
                </div>
            </div>
        </div>

        <!-- Information éco-responsable -->
        <div class="text-center mt-3">
            <small class="text-muted">
                🌍 Inscription sécurisée avec engagement éco-responsable
            </small>
        </div>
    </div>
</div>

<!-- Modal des conditions d'utilisation -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Conditions d'utilisation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <h6>🌱 Plateforme de Gestion de Serres Connectées</h6>
                <p>En utilisant cette plateforme, vous acceptez les conditions suivantes :</p>
                
                <h6>1. Utilisation responsable</h6>
                <ul>
                    <li>Utiliser le système uniquement pour la gestion des serres de votre équipe</li>
                    <li>Ne pas interférer avec les équipements d'autres équipes</li>
                    <li>Signaler tout dysfonctionnement immédiatement</li>
                </ul>

                <h6>2. Données partagées</h6>
                <ul>
                    <li>Les données des capteurs sont partagées entre toutes les équipes</li>
                    <li>Respect de la confidentialité des informations d'autres équipes</li>
                    <li>Usage uniquement dans le cadre du projet pédagogique</li>
                </ul>

                <h6>3. Éco-responsabilité</h6>
                <ul>
                    <li>Utilisation optimisée des ressources système</li>
                    <li>Activation des actionneurs uniquement quand nécessaire</li>
                    <li>Respect des bonnes pratiques environnementales</li>
                </ul>

                <h6>4. Sécurité</h6>
                <ul>
                    <li>Maintenir la confidentialité de vos identifiants</li>
                    <li>Signaler toute activité suspecte</li>
                    <li>Utiliser des mots de passe sécurisés</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles spécifiques à la page d'inscription */
.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.25);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary {
    padding: 12px;
    font-weight: 500;
}

.alert-success {
    background-color: rgba(45, 90, 39, 0.1);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.modal-content {
    border-radius: 12px;
}

.form-text {
    font-size: 0.8rem;
}
</style>

<script>
// Validation côté client pour améliorer l'UX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    // Vérification en temps réel de la correspondance des mots de passe
    function checkPasswordMatch() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);
    
    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        checkPasswordMatch();
        
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});
</script>