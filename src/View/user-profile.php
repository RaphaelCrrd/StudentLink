<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?= htmlspecialchars($student['firstname']); ?></title>
    <link rel="stylesheet" href="../../public/assets/css/user-profile.css">
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
    
<div style="width: 100%; max-width: 400px; display: flex; flex-direction: column; align-items: center;">

    <?php if (isset($_GET['report']) && $_GET['report'] === 'success'): ?>
        <div id="flash-message" class="report-confirmation">
            ✅ Le signalement a été envoyé avec succès.
        </div>
        
        <script>
            setTimeout(function() {
                var msg = document.getElementById('flash-message');
                if (msg) {
                    msg.style.transition = "opacity 0.5s ease";
                    msg.style.opacity = "0";
                    setTimeout(function() { msg.remove(); }, 500);
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <div class="profile-card">
        <div class="avatar">
            <?= strtoupper(substr($student['firstname'], 0, 1) . substr($student['lastname'], 0, 1)); ?>
        </div>

        <h1><?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></h1>
        <div class="school">🏫 <?= htmlspecialchars($student['school_name'] ?? 'Aucune école'); ?></div>

        <p><img src="../../public/assets/img/instagram.png" alt="Instagram" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 5px; margin-top: 3px; margin-bottom: 3px"> <?= htmlspecialchars($student['instagram'] ?? 'Non renseigné'); ?></p>

        <a href="mailto:<?= htmlspecialchars($student['email']); ?>" class="info-link">
            ✉️ Contacter par email
        </a>

        <button onclick="toggleReportForm()" class="btn-report-trigger">⚠️ Signaler ce compte</button>

        <div id="report-form-container" style="display: none; background: #fff5f5; border: 1px solid #fca5a5; padding: 15px; border-radius: 8px; margin-top: 15px; text-align: left;">
            <form action="../Controller/ReportController.php" method="POST">
                <input type="hidden" name="reported_id" value="<?= $student['id']; ?>">
                <label for="reason" style="display: block; font-size: 0.8rem; font-weight: bold; margin-bottom: 5px; color: #991b1b;">Raison du signalement :</label>
                <textarea name="reason" id="reason" rows="3" required placeholder="Ex: Contenu inapproprié, faux compte..." style="width: 100%; border: 1px solid #fca5a5; border-radius: 6px; padding: 8px; font-size: 0.85rem; box-sizing: border-box; resize: none;"></textarea>
                <button type="submit" style="background: #dc2626; color: white; border: none; padding: 8px 12px; border-radius: 6px; font-size: 0.8rem; margin-top: 8px; width: 100%; font-weight: bold; cursor: pointer;">Envoyer le signalement</button>
            </form>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="../View/dashboard.php" class="nav-item">
            <span class="material-symbols-outlined">home</span>
        </a>
        <a href="../View/profile.php" class="nav-item">
            <span class="material-symbols-outlined">account_circle</span>
        </a>
        <a href="../View/setting.php" class="nav-item">
            <span class="material-symbols-outlined">settings</span>
        </a>
        <a href="../Controller/LogoutController.php" class="nav-item" style="color: #e74c3c;">
            <span class="material-symbols-outlined">exit_to_app</span>
        </a>
    </nav>

</div> <script>
function toggleReportForm() {
    var form = document.getElementById('report-form-container');
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
}
</script>

</body>
</html>