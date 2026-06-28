<?php
require_once '../Controller/SearchController.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche - Student Link</title>
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
        body { background-color: #f3f4f6; padding: 20px; padding-bottom: 80px; }
        .container { max-width: 500px; margin: 0 auto; }
        
        .back-link { display: inline-block; margin-bottom: 20px; color: #4f46e5; text-decoration: none; font-weight: 600; }
        h1 { font-size: 1.4rem; color: #111827; margin-bottom: 5px; }
        .search-term { color: #6b7280; font-size: 0.9rem; margin-bottom: 20px; }

        .student-card {
            background: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .avatar {
            width: 50px; height: 50px; border-radius: 50%;
            background: #eef2ff; color: #4f46e5;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 1.1rem;
        }
        .student-info { flex: 1; }
        .student-info h3 { font-size: 1rem; color: #111827; }
        .student-info p { font-size: 0.8rem; color: #6b7280; margin-top: 2px; }
        .tags { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 6px; }
        .tag { background: #f3f4f6; color: #4f46e5; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; }
        
        .btn-connect {
            background: #4f46e5; color: white; border: none;
            padding: 8px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
        }
        .no-results { text-align: center; color: #9ca3af; padding: 4px 0; margin-top: 40px; }

        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: white; height: 65px; display: flex;
            justify-content: space-around; align-items: center; border-top: 1px solid #e5e7eb;
        }
        .nav-item { text-decoration: none; color: #9ca3af; font-size: 0.8rem; text-align: center; }
        .nav-item.active { color: #4f46e5; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <a href="dashboard.php" class="back-link">⬅️ Retour au Dashboard</a>
        
        <h1>Résultats de recherche</h1>
        <p class="search-term">Pour le mot-clé : "<strong><?= htmlspecialchars($searchQuery); ?></strong>"</p>

        <?php if (empty($results)): ?>
            <div class="no-results">
                🔍 Aucun étudiant ou établissement ne correspond à votre recherche.
            </div>
        <?php else: ?>
            <?php foreach ($results as $student): 
                $initials = strtoupper(substr($student['firstname'], 0, 1) . substr($student['lastname'], 0, 1));
            ?>
                <div class="student-card">
                    <div class="avatar"><?= $initials; ?></div>
                    <div class="student-info">
                        <h3><?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></h3>
                        <p>🏫 <?= htmlspecialchars($student['school_name']); ?></p>
                        <p><img src="../../public/assets/img/instagram.png" alt="Instagram" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 5px; margin-top: 3px; margin-bottom: 3px"> <?= htmlspecialchars($student['instagram']?? 'Non renseigné'); ?></p>
                        <div class="tags">
                            <?php 
                            if(!empty($student['interests'])) {
                                $interestsArr = explode(',', $student['interests']);
                                foreach($interestsArr as $interest) {
                                    echo '<span class="tag">#' . htmlspecialchars(trim($interest)) . '</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <button class="btn-connect" onclick="sendFriendRequest(<?= $student['id']; ?>, this)">Ajouter</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item">
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

    <script>
function sendFriendRequest(userId, buttonElement) {
    buttonElement.innerText = "Demandé";
    buttonElement.style.backgroundColor = "#6b7280";
    buttonElement.disabled = true;

    // Envoi de la requête au contrôleur sans recharger la page (AJAX)
    fetch(`../Controller/FriendController.php?action=send&to=${userId}`)
        .then(response => {
            if (!response.ok) { // Pas ok donc erreur
                buttonElement.innerText = "Ajouter";
                buttonElement.style.backgroundColor = "#4f46e5";
                buttonElement.disabled = false;
                alert("Erreur lors de l'envoi de la demande.");
            }
        });
}
</script>
</body>
</html>