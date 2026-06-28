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
    <link rel="stylesheet" href="../../public/assets/css/search-results.css">
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
                    <div class="avatar">
                        <?php if (!empty($student['avatar']) && file_exists('../../public/uploads/profile_picture/' . $student['avatar'])): ?>
                            <img src="../../public/uploads/profile_picture/<?= htmlspecialchars($student['avatar']); ?>" alt="Profile picture" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?= $initials; ?>
                        <?php endif; ?>
                    </div>
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