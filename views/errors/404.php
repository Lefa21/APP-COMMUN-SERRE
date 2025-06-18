<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur 404 - Page non trouvée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2d5a27, #5d8a54);
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
        }
        .btn-home:hover {
            background-color: #7cb342;
            border-color: #7cb342;
            color: white;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-message">🌱 Oups ! Cette page n'existe pas</h1>
        <p class="lead mb-4">
            La page que vous recherchez semble avoir été déplacée ou supprimée.
            Retournons à l'accueil de votre serre connectée !
        </p>
        <a href="http://localhost/APP-COMMUN-SERRE/" class="btn btn-home">
            🏠 Retour à l'accueil
        </a>
    </div>
</body>
</html>