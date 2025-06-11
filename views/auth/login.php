<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h2 class="h4">🌱 Connexion</h2>
                    <p class="text-muted">Accédez à votre serre connectée</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>?controller=auth&action=login">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person"></i> Nom d'utilisateur ou Email
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="username" 
                            name="username" 
                            required 
                            autocomplete="username"
                            value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Mot de passe
                        </label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                        >
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="mb-2">Pas encore de compte ?</p>
                    <a href="<?= BASE_URL ?>?controller=auth&action=register" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus"></i> Créer un compte
                    </a>
                </div>

                <!-- Informations de test -->
                <div class="mt-4">
                    <div class="alert alert-info">
                        <small>
                            <strong>Comptes de test:</strong><br>
                            <strong>Admin:</strong> admin / admin123<br>
                            <strong>Utilisateur:</strong> user1 / user123
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information éco-responsable -->
        <div class="text-center mt-3">
            <small class="text-muted">
                🌍 Connexion sécurisée éco-conçue
            </small>
        </div>
    </div>
</div>

<style>
/* Styles spécifiques à la page de connexion */
.card {
    border: none;
    border-radius: 12px;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.25);
}

.btn-primary {
    padding: 12px;
    font-weight: 500;
}

.alert-info {
    background-color: rgba(45, 90, 39, 0.1);
    border-color: var(--primary-color);
    color: var(--primary-color);
}
</style>