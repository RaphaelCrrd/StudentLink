<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réglages - Student Link</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <script>
    // Verificaton de la compatibilité du navigateur
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
        .then(() => console.log('Service Worker Enregistré !'))
        .catch((err) => console.log('Erreur SW :', err));
    }
    </script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f3f4f6; padding: 20px; padding-bottom: 90px; }
        .container { max-width: 500px; margin: 0 auto; }
        
        h1 { font-size: 1.5rem; color: #111827; margin-bottom: 20px; }
        .card { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); margin-bottom: 20px; }
        .card-title { font-size: 1.1rem; color: #374151; font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; color: #4b5563; font-size: 0.85rem; font-weight: 600; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; }

        .btn-submit { width: 100%; background: #4f46e5; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; }
        .btn-submit:hover { background: #4338ca; }

        .danger-zone { border: 1px solid #fca5a5; background: #fff5f5; }
        .danger-zone .card-title { color: #dc2626; }
        .danger-text { font-size: 0.85rem; color: #7f1d1d; margin-bottom: 15px; line-height: 1.4; }
        .btn-danger { width: 100%; background: #dc2626; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; }
        .btn-danger:hover { background: #b91c1c; }

        .alert { padding: 12px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 15px; text-align: center; }
        .alert-success { background: #def7ec; color: #03543f; }
        .alert-error { background: #fde8e8; color: #9b1c1c; }

        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: white; height: 65px; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #e5e7eb; }
        .nav-item { text-decoration: none; color: #9ca3af; font-size: 0.8rem; text-align: center; }
        .nav-item.active { color: #4f46e5; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <h1>⚙️ Réglages</h1>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'wrong_password'): ?>
            <div class="alert alert-error">❌ L'ancien mot de passe est incorrect.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'empty'): ?>
            <div class="alert alert-error">❌ Veuillez remplir tous les champs.</div>
        <?php endif; ?>
        <?php if (isset($_GET['status']) && $_GET['status'] === 'password_updated'): ?>
            <div class="alert alert-success">🔒 Mot de passe modifié avec succès !</div>
        <?php endif; ?>

        <div class="card">
            <div class="card-title">🔒 Modifier le mot de passe</div>
            <form action="../Controller/SettingController.php" method="POST">
                <input type="hidden" name="action" value="update_password">
                
                <div class="form-group">
                    <label for="old_password">Mot de passe actuel</label>
                    <input type="password" id="old_password" name="old_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <button type="submit" class="btn-submit">Mettre à jour le mot de passe</button>
            </form>
        </div>

        <div class="card danger-zone">
            <div class="card-title">Supprimer mon compte</div>
            <p class="danger-text">
                Si vous décidez de désactiver votre compte, votre profil ne sera plus visible par les autres étudiants de votre établissement et vous serez déconnecté.
            </p>
            <form action="../Controller/SettingController.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir désactiver votre compte ?');">
                <input type="hidden" name="action" value="disable_account">
                <button type="submit" class="btn-danger">Désactiver mon compte</button>
            </form>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item">
            <span class="material-symbols-outlined">home</span>
        </a>
        <a href="profile.php" class="nav-item">
            <span class="material-symbols-outlined">account_circle</span>
        </a>
        <a href="setting.php" class="nav-item active">
            <span class="material-symbols-outlined">settings</span>
        </a>
        <a href="../Controller/LogoutController.php" class="nav-item" style="color: #e74c3c;">
            <span class="material-symbols-outlined">exit_to_app</span>
        </a>
    </nav>

</body>
</html>