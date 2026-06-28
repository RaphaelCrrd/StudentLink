<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("SELECT u.*, s.name as school_name FROM users u LEFT JOIN schools s ON u.school_id = s.id WHERE u.id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupère les demandes reçues
    $reqRequests = $bdd->prepare("
        SELECT f.id as friendship_id, u.id as sender_id, u.firstname, u.lastname 
        FROM friendships f 
        JOIN users u ON f.sender_id = u.id 
        WHERE f.receiver_id = :my_id AND f.status = 'pending'
    ");
    $reqRequests->execute(['my_id' => $_SESSION['user_id']]);
    $demandes = $reqRequests->fetchAll(PDO::FETCH_ASSOC);

    // Récupéree les contacts acceptés
    $reqFriends = $bdd->prepare("
        SELECT u.id, u.firstname, u.lastname, u.interests, u.instagram
        FROM friendships f 
        JOIN users u ON (f.sender_id = u.id OR f.receiver_id = u.id) 
        WHERE (f.sender_id = :my_id OR f.receiver_id = :my_id) 
        AND f.status = 'accepted' 
        AND u.id != :my_id
    ");
    $reqFriends->execute(['my_id' => $_SESSION['user_id']]);
    $contacts = $reqFriends->fetchAll(PDO::FETCH_ASSOC);
    
    $nbContacts = count($contacts);

} catch (Exception $e) {
        $logSql = "INSERT INTO logs (action_type, description) VALUES ('SYSTEM_ERROR', :desc)";
        $logStmt = $bdd->prepare($logSql);
        $logStmt->execute(['desc' => "Erreur SQL : " . $e->getMessage()]);
    
        die('Une erreur technique est survenue.');
    }

$initials = strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Studou</title>
    <link rel="stylesheet" href="../../public/assets/css/profile.css">
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
        
        <div class="profile-header">
            <div class="avatar-large">
                <?php if (!empty($user['avatar']) && file_exists('../../public/uploads/profile_picture/' . $user['avatar'])): ?>
                    <img src="../../public/uploads/profile_picture/<?= htmlspecialchars($user['avatar']); ?>" alt="Profile picture" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <?= $initials; ?>
                <?php endif; ?>
            </div>
            
            <h1><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h1>
            <p>🏫 <?= htmlspecialchars($user['school_name'] ?? 'Aucun établissement lié'); ?></p>
        </div>

        <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
            <div class="alert-success">✅ Profil mis à jour avec succès !</div>
        <?php endif; ?>

        <div class="card">
            <form action="../Controller/ProfileController.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="avatar">Changer ma photo de profil</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>Adresse mail (Non modifiable)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="age">Âge</label>
                    <input type="number" id="age" name="age" value="<?= htmlspecialchars($user['age']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="instagram">Instagram</label> <!-- J'avais mis for="age" et ça fonctionnait-->
                    <input type="string" id="instagram" name="instagram" value="<?= htmlspecialchars($user['instagram'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="interests">Mes centres d'intérêt / Passions (séparés par des virgules)</label>
                    <textarea id="interests" name="interests" placeholder="Ex: Informatique, Basket, Jeux vidéo..."><?= htmlspecialchars($user['interests']); ?></textarea>
                    <span style="font-size: 0.75rem; color: #9ca3af;">Ajoute des tags en les séparant par des virgules.</span>
                </div>

                <button type="submit" class="btn-save">Enregistrer les modifications</button>
            </form>
        </div>

        <div class="card" style="text-align: center; margin: 20px 0px; padding: 15px; background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <span style="font-size: 1.5rem; font-weight: bold; color: #4f46e5;"><?= $nbContacts; ?></span>
            <p style="font-size: 0.85rem; color: #6b7280; font-weight: 600;">Contacts validés</p>
        </div>

        <?php if (!empty($demandes)): ?>
            <div class="card" style="margin: 20px 0px; border-left: 4px solid #4f46e5; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <h3 style="font-size: 0.95rem; margin-bottom: 10px; color: #374151;">🔔 Demandes reçues</h3>
                <?php foreach ($demandes as $demande): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <a href="../Controller/UserProfileController.php?id=<?= $demande['sender_id']; ?>" style="text-decoration: none; color: #4f46e5; font-size: 0.9rem; font-weight: 600;">
                            👤 <?= htmlspecialchars($demande['firstname'] . ' ' . $demande['lastname']); ?>
                        </a>
                        
                        <a href="../Controller/FriendController.php?action=accept&id=<?= $demande['friendship_id']; ?>" 
                        style="background: #16a34a; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
                        Accepter
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 20px; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <h3 style="font-size: 0.95rem; margin-bottom: 12px; color: #374151;">👥 Mes contacts</h3>
            <?php if (empty($contacts)): ?>
                <p style="font-size: 0.85rem; color: #9ca3af; text-align: center;">Aucun contact pour le moment.</p>
            <?php else: ?>
                <?php foreach ($contacts as $contact): ?>
                    <a href="../Controller/UserProfileController.php?id=<?= $contact['id']; ?>" style="display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #f3f4f6; text-decoration: none; color: unset;">
                        <div style="width: 30px; height: 30px; border-radius: 50%; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;">
                            <?= strtoupper(substr($contact['firstname'], 0, 1) . substr($contact['lastname'], 0, 1)); ?>
                        </div>
                        <span style="font-size: 0.9rem; font-weight: 600; color: #111827;"><?= htmlspecialchars($contact['firstname'] . ' ' . $contact['lastname']); ?></span>
                        <span style="margin-left: auto; color: #9ca3af; font-size: 0.8rem;">➔</span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item">
            <span class="material-symbols-outlined">home</span>
        </a>
        <a href="profile.php" class="nav-item active">
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