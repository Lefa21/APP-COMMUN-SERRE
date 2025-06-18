<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur 403 - Accès refusé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2d5a27, #5d8a54);
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
        .btn-home {
            background-color: #8bc34a;
            border-color: #8bc34a;
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            margin: 0.5rem;
        }
        .btn-home:hover {
            background-color: #7cb342;
            border-color: #7cb342;
            color: white;
            text-decoration: none;
        }
        .permissions-info {
            background-color: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <h1 class="error-message">🔒 Accès refusé</h1>
        <p class="lead mb-4">
            Vous n'avez pas les permissions nécessaires pour accéder à cette page.
        </p>
        
        <div class="permissions-info">
            <h5>🔐 Information sur les permissions</h5>
            <ul class="text-start">
                <li><strong>Utilisateurs :</strong> Accès aux capteurs et données</li>
                <li><strong>Administrateurs :</strong> Gestion complète du système</li>
                <li><strong>Équipes :</strong> Accès limité aux équipements de leur équipe</li>
            </ul>
            <p class="mb-0">
                <small>
                    Si vous pensez que c'est une erreur, contactez un administrateur ou 
                    vérifiez que vous êtes connecté avec le bon compte.
                </small>
            </p>
        </div>
        
        <div>
            <a href="<?= BASE_URL ?? 'https://green-pulse.herogu.garageisep.com/' ?>" class="btn-home">
                🏠 Retour à l'accueil
            </a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?? 'https://green-pulse.herogu.garageisep.com/' ?>?controller=profile" class="btn-home">
                    👤 Mon profil
                </a>
                <a href="<?= BASE_URL ?? 'https://green-pulse.herogu.garageisep.com/' ?>?controller=auth&action=logout" class="btn-home">
                    🚪 Se déconnecter
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?? 'https://green-pulse.herogu.garageisep.com/' ?>?controller=auth&action=login" class="btn-home">
                    🔑 Se connecter
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Information éco-responsable -->
        <div class="text-center mt-4">
            <small class="text-muted">
                🌍 Page d'erreur éco-conçue avec un design minimaliste
            </small>
        </div>
    </div>
</body>
</html>