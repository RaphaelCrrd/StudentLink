<?php
session_start();

// Sécurité : si pas connecté, retour à la case départ
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupération des données actuelles de l'étudiant
try {
    $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("SELECT u.*, s.name as school_name FROM users u LEFT JOIN schools s ON u.school_id = s.id WHERE u.id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Utilisateur introuvable.");
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}


$initials = strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Student Link</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f3f4f6; padding: 20px; padding-bottom: 90px; }
        .container { max-width: 500px; margin: 0 auto; }
        
        .profile-header { text-align: center; margin: 20px 0; }
        .avatar-large {
            width: 90px; height: 90px; border-radius: 50%;
            background: #4f46e5; color: white; margin: 0 auto 15px auto;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; font-weight: bold; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }
        .profile-header h1 { font-size: 1.5rem; color: #111827; }
        .profile-header p { color: #6b7280; font-size: 0.9rem; }

        .card { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; color: #374151; font-size: 0.85rem; font-weight: 600; }
        .form-group input, .form-group textarea {
            width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; background-color: #f9fafb;
        }
        .form-group input:disabled { background-color: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
        .form-group textarea { resize: vertical; height: 80px; }

        .btn-save {
            width: 100%; background: #4f46e5; color: white; border: none; padding: 12px;
            border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-top: 10px;
        }
        .btn-save:hover { background: #4338ca; }
        
        .alert-success { background: #def7ec; color: #03543f; padding: 12px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 15px; text-align: center; }

        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; background: white; height: 65px;
            display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #e5e7eb;
        }
        .nav-item { text-decoration: none; color: #9ca3af; font-size: 0.8rem; text-align: center; }
        .nav-item.active { color: #4f46e5; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="profile-header">
            <div class="avatar-large"><?= $initials; ?></div>
            <h1><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h1>
            <p>🏫 <?= htmlspecialchars($user['school_name'] ?? 'Aucun établissement lié'); ?></p>
        </div>

        <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
            <div class="alert-success">✅ Profil mis à jour avec succès !</div>
        <?php endif; ?>

        <div class="card">
            <form action="../Controller/ProfileController.php" method="POST">
                
                <div class="form-group">
                    <label>Adresse mail (Non modifiable)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="age">Âge</label>
                    <input type="number" id="age" name="age" value="<?= htmlspecialchars($user['age']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="interests">Mes centres d'intérêt / Passions (séparés par des virgules)</label>
                    <textarea id="interests" name="interests" placeholder="Ex: Informatique, Basket, Jeux vidéo..."><?= htmlspecialchars($user['interests']); ?></textarea>
                    <span style="font-size: 0.75rem; color: #9ca3af;">Ajoute des tags en les séparant par des virgules.</span>
                </div>

                <button type="submit" class="btn-save">Enregistrer les modifications</button>
            </form>
        </div>

    </div>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item">🏠<br>Accueil</a>
        <a href="profile.php" class="nav-item active">👤<br>Mon Profil</a>
        <a href="setting.php" class="nav-item">⚙️<br>Réglages</a>
        <a href="../Controller/LogoutController.php" class="nav-item" style="color: #e74c3c;">🚪<br>Quitter</a>
    </nav>

</body>
</html>