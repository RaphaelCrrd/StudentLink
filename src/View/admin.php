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
    <title>Panel Administration - Student Link</title>
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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
            margin: 0;
            padding: 15px;
            padding-bottom: 90px;
        }
        .admin-container {
            max-width: 100%;
            margin: 0 auto;
        }
        h1 {
            font-size: 1.6rem;
            color: #111827;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        h2 {
            font-size: 1.1rem;
            color: #374151;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .admin-section {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-top: 10px;
            font-size: 0.85rem;
        }
        .admin-table th, .admin-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #edf2f7;
        }
        .admin-table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
        }
        .row-error {
            background-color: #fef2f2;
            color: #991b1b;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: bold;
        }
        .badge-active {
            background: #dcfce7;
            color: #166534;
        }
        .badge-suspended {
            background: #fef2f2;
            color: #991b1b;
        }
        .btn {
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
        }
        .btn-suspend {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }
        .btn-activate { 
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #86efac;
        }
        .bottom-nav {
            position: fixed; 
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            height: 65px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            border-top: 1px solid #e5e7eb;
            z-index: 1000; /* Pour que la barre reste toujours au-dessus des tableaux */
        }
        .nav-item {
            text-decoration: none;
            color: #9ca3af;
            font-size: 0.8rem;
            text-align: center;
        }
        .nav-item.active {
            color: #4f46e5;
            font-weight: bold;
        }
    </style>
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