<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studou - Connexion</title>
    <link rel="stylesheet" href="../../public/assets/css/login.css">
    <link rel="manifest" href="/manifest.json">

    <script>
    // Verificaton de la compatibilité du navigateur
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
        .then(() => console.log('Service Worker Enregistré !'))
        .catch((err) => console.log('Erreur SW :', err));
    }
    </script>
</head>
<body>

    <div class="login-container">
        <div class="logo-area">
            <h1>Studou</h1>
            <p>Connecte-toi avec tes futurs camarades</p>
        </div>

        <form action="../Controller/LoginController.php" method="POST" id="loginForm">
            
            <div class="form-group">
                <label for="email">Adresse mail étudiante / personnelle</label>
                <type-input>
                    <input type="email" id="email" name="email" placeholder="exemple@mail.com" required>
                </type-input>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <type-input>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </type-input>
            </div>

            <button type="submit" class="btn-submit">Se connecter</button>
        </form>

        <div class="separator">ou</div>

        <a href="./register.php" class="btn-register">Créer un compte</a>
    </div>

    <script src="/js/FormValidator.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formValidator = new FormValidator('loginForm');
        });
    </script>
</body>
</html>