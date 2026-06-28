<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userStmt = $bdd->prepare("SELECT u.school_id, s.name as school_name FROM users u LEFT JOIN schools s ON u.school_id = s.id WHERE u.id = :id");
    $userStmt->execute(['id' => $_SESSION['user_id']]);
    $currentUserInfo = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    $schoolId = $currentUserInfo['school_id'];
    $schoolName = $currentUserInfo['school_name'] ?? "votre établissement";

    $studentsStmt = $bdd->prepare("SELECT id, firstname, lastname, avatar, interests FROM users WHERE school_id = :school_id AND id != :my_id AND status = 'active' ORDER BY id DESC LIMIT 10");
    $studentsStmt->execute([
        'school_id' => $schoolId,
        'my_id' => $_SESSION['user_id']
    ]);
    $camarades = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
        $logSql = "INSERT INTO logs (action_type, description) VALUES ('SYSTEM_ERROR', :desc)";
        $logStmt = $bdd->prepare($logSql);
        $logStmt->execute(['desc' => "Erreur SQL : " . $e->getMessage()]);
    
        die('Une erreur technique est survenue.');
    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studou - Dashboard</title>
    <link rel="stylesheet" href="../../public/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <script>
    // Verification de la compatifilibé du navigateur
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
        .then(() => console.log('Service Worker Enregistré !'))
        .catch((err) => console.log('Erreur SW :', err));
    }
    </script>
</head>
<body>

    <header>
        <p>Ravi de te revoir,</p>
        <h1>👋 <?= htmlspecialchars($_SESSION['firstname']); ?></h1>
        <p>Futur étudiant à : <strong><?= htmlspecialchars($schoolName); ?></strong></p>
    </header>

    <div class="container">
        <form action="search-results.php" method="GET" class="search-box">
            <input type="text" name="q" placeholder="Chercher un nom ou une école...">
            <button type="submit">Go</button>
        </form>

        <h2 class="section-title">Nouveaux inscrits dans ton école</h2>

        <?php if (empty($result)): ?>
            <div class="empty-state">
                Aucun autre étudiant de ton école n'est encore inscrit. Sois le premier à partager l'application !
            </div>
        <?php else: ?>
            <?php foreach ($result as $student): 
                // Extraction des initiales pour créer un avatar par défaut
                $initials = strtoupper(substr($student['firstname'], 0, 1) . substr($student['lastname'], 0, 1));
            ?>
                <div class="student-card">
                    <div class="avatar"><?= $initials; ?></div>
                    <div class="student-info">
                        <h3><?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></h3>
                        <p>🏫 <?= htmlspecialchars($student['school_name']); ?></p>
                        <p><img src="../../public/assets/img/instagram.png" alt="Instagram" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 5px; margin-top: 3px; margin-bottom: 3px"> <?= htmlspecialchars($student['instagram'] ?? 'Non renseigné'); ?></p>
                        <div class="tags">
                            <?php 
                            if(!empty($student['interests'])) {
                                $interestsArr = explode(',', $student['interests']);
                                foreach(array_slice($interestsArr, 0, 3) as $interest) {
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