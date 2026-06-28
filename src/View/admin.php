<?php
// Si la variable $users n'est pas définie, c'est qu'on n'est pas passé par le contrôleur
if (!isset($users)) {
    header('Location: ../Controller/AdminController.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panel Administration - Studou</title>
    <link rel="stylesheet" href="../../public/assets/css/admin.css">
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

<div class="admin-container">
    <h1>Tableau de bord de Modération</h1>

    <div class="admin-grid">
        
        <section class="admin-section full-width">
            <h2>⚠️ Comptes signalés par la communauté</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Signalé par</th>
                        <th>Utilisateur ciblé</th>
                        <th>Raison du signalement</th>
                        <th>Statut actuel</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reports)): ?>
                        <tr><td colspan="6" style="text-align: center; color: #64748b;">Aucun signalement en attente.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($report['created_at'])); ?></td>
                                <td><?= htmlspecialchars($report['reporter_fn'] . ' ' . $report['reporter_ln']); ?></td>
                                <td><strong><?= htmlspecialchars($report['reported_fn'] . ' ' . $report['reported_ln']); ?></strong></td>
                                <td><em style="color: #4b5563;">"<?= htmlspecialchars($report['reason']); ?>"</em></td>
                                <td><span class="badge badge-suspended">En attente</span></td>
                                <td>
                                    <a href="../Controller/AdminController.php?action=toggle_status&user_id=<?= $report['reported_user_id']; ?>&report_id=<?= $report['report_id']; ?>" class="btn btn-suspend" onclick="return confirm('Suspendre le compte de cet utilisateur suite au signalement ?')">
                                        Suspendre
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="admin-section">
            <h2>👥 Gestion des Étudiants</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom / Prénom</th>
                        <th>École</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></strong><br>
                                <span style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($user['email']); ?></span>
                            </td>
                            <td><?= htmlspecialchars($user['school_name'] ?? 'Non renseignée'); ?></td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="badge badge-active">Actif</span>
                                <?php else: ?>
                                    <span class="badge badge-suspended">Suspendu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <a href="../Controller/AdminController.php?action=toggle_status&user_id=<?= $user['id']; ?>" class="btn btn-suspend" onclick="return confirm('Êtes-vous sûr de vouloir suspendre ce compte ? Il ne pourra plus se connecter.')">
                                        Suspendre
                                    </a>
                                <?php else: ?>
                                    <a href="../Controller/AdminController.php?action=toggle_status&user_id=<?= $user['id']; ?>" class="btn btn-activate" onclick="return confirm('Réactiver le compte de cet étudiant ?')">
                                        Réactiver
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="admin-section">
            <h2>📜 Activités & Erreurs Système</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Horodatage</th>
                        <th>Type</th>
                        <th>Détails de l'événement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="3" style="text-align: center; color: #64748b;">Aucune activité enregistrée.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="<?= $log['action_type'] === 'SYSTEM_ERROR' ? 'row-error' : ''; ?>">
                                <td style="white-space: nowrap; color: #64748b;"><?= date('d/m H:i:s', strtotime($log['created_at'])); ?></td>
                                <td><strong><?= htmlspecialchars($log['action_type']); ?></strong></td>
                                <td><?= htmlspecialchars($log['description']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </div>
</div>
    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item active">
            <span class="material-symbols-outlined">home</span>
        </a>
        <a href="profile.php" class="nav-item">
            <span class="material-symbols-outlined">account_circle</span>
        </a>
        <a href="setting.php" class="nav-item">
            <span class="material-symbols-outlined">settings</span>
        </a>
        <a href="../Controller/LogoutController.php" class="nav-item" style="color: #e74c3c;">
            <span class="material-symbols-outlined">exit_to_app</span>
        </a>
    </nav>
</body>
</html>