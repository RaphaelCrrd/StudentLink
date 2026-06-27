<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?= htmlspecialchars($student['firstname']); ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f3f4f6; margin: 0; padding: 20px; display: flex; justify-content: center; }
        .profile-card { background: white; width: 100%; max-width: 400px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); padding: 25px; box-sizing: border-box; text-align: center; }
        .avatar { width: 80px; height: 80px; background: #e2e8f0; color: #475569; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: bold; margin: 0 auto 15px; }
        h1 { font-size: 1.4rem; color: #111827; margin: 0 0 5px; }
        .school { color: #64748b; font-size: 0.9rem; margin-bottom: 20px; }
        .info-link { display: block; background: #f8fafc; padding: 12px; border-radius: 8px; color: #1e1b4b; text-decoration: none; font-weight: 600; font-size: 0.9rem; margin-bottom: 10px; border: 1px solid #e2e8f0; }
        .btn-report-trigger { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; padding: 10px; border-radius: 8px; font-size: 0.85rem; width: 100%; margin-top: 25px; cursor: pointer; font-weight: 500; }
        .report-confirmation { background: #def7ec; color: #03543f; display: flex; flex-direction: column; align-items: center; padding: 12px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 15px; text-align: center; font-weight: 600; width: 100%; max-width: 400px; }
    </style>
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

</div> <script>
function toggleReportForm() {
    var form = document.getElementById('report-form-container');
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
}
</script>

</body>
</html>