<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Link - Connexion</title>
    <link rel="manifest" href="/manifest.json">

    <script>
    // Verificaton de la compatibilité du navigateur
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
        .then(() => console.log('Service Worker Enregistré !'))
        .catch((err) => console.log('Erreur SW :', err));
    }
    </script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            background-color: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            border-radius: 16px;
            text-align: center;
        }

        .logo-area h1 {
            color: #4f46e5;
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .logo-area p {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 32px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4f46e5;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #4338ca;
        }

        .separator {
            margin: 24px 0;
            display: flex;
            align-items: center;
            text-align: center;
            color: #9ca3af;
            font-size: 0.85rem;
        }

        .separator::before, .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }

        .separator:not(:empty)::before { margin-right: .5em; }
        .separator:not(:empty)::after { margin-left: .5em; }

        .btn-register {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: transparent;
            color: #4f46e5;
            border: 2px solid #4f46e5;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-register:hover {
            background-color: #f5f3ff;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo-area">
            <h1>Student Link</h1>
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