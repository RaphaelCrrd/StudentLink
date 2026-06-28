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
    <title>Réglages - Studou</title>
    <link rel="stylesheet" href="../../public/assets/css/setting.css">
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