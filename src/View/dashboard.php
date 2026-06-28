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
    <title>Student Link - Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <script>
    // Verification de la compatifilibé du navigateur
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
        .then(() => console.log('Service Worker Enregistré !'))
        .catch((err) => console.log('Erreur SW :', err));
    }
    </script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { background-color: #f3f4f6; padding-bottom: 80px; }
        
        header {
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        header h1 { font-size: 1.5rem; }
        header p { font-size: 0.9rem; opacity: 0.9; margin-top: 4px; }

        .container { padding: 20px; max-width: 500px; margin: 0 auto; }

        .search-box {
            background: white;
            padding: 12px;
            border-radius: 12px;
            margin-top: -15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: flex;
            gap: 10px;
        }
        .search-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .search-box button {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 0 15px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .section-title { margin: 25px 0 15px 0; color: #374151; font-size: 1.1rem; }

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
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #4f46e5;
            font-size: 1.2rem;
        }
        .student-info { flex: 1; }
        .student-info h3 { font-size: 1rem; color: #111827; }
        .student-info p { font-size: 0.8rem; color: #6b7280; margin-top: 2px; }
        .tags { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 6px; }
        .tag { background: #eef2ff; color: #4f46e5; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; }

        .btn-connect {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: white;
            height: 65px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            border-top: 1px solid #e5e7eb;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.03);
        }
        
        
        .empty-state { text-align: center; color: #9ca3af; padding: 30px 0; font-size: 0.9rem; }
    </style>
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