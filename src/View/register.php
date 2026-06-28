<?php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
    $query = $bdd->query("SELECT id, name, city FROM schools ORDER BY name ASC");
    $schools = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $schools = [
        ['id' => 1, 'name' => 'Université d\'Orléans', 'city' => 'Orléans'],
        ['id' => 2, 'name' => 'Épitech', 'city' => 'Paris']
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studou - Inscription</title>
    <link rel="stylesheet" href="../../public/assets/css/register.css">
    <link rel="manifest" href="/manifest.json">

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

    <div class="register-container">
        <div class="header-area">
            <h2>Rejoins Studou</h2>
            <p>Crée ton profil en quelques secondes</p>
        </div>

    
        <form action="../Controller/RegisterController.php" method="POST" id="registerForm">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="firstname">Prénom</label>
                    <input type="text" id="firstname" name="firstname" required>
                </div>
                <div class="form-group">
                    <label for="lastname">Nom</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="etudiant@univ.fr" required>
                </div>
                <div class="form-group" style="flex: 0 0 80px;">
                    <label for="age">Âge</label>
                    <input type="number" id="age" name="age" min="15" max="99" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Minimum 6 caractères" required>
            </div>

            <div class="form-group">
                <label for="school_id">Futur établissement d'étude</label>
                <select id="school_id" name="school_id" required>
                    <option value="">-- Choisis ton école / fac --</option>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?= $school['id']; ?>">
                            <?= htmlspecialchars($school['name']) . " (" . htmlspecialchars($school['city']) . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="interests">Tes centres d'intérêt (séparés par une virgule)</label>
                <input type="text" id="interests" name="interests" placeholder="Ex: Sport, Jeux Vidéo, Musique, Cinéma">
            </div>
            
            <div class="form-group">
                <label for="instagram">Ton instagram</label>
                <input type="text" id="instagram" name="instagram" placeholder="Ex: raphael_crrd">
            </div>

            <button type="submit" class="btn-submit">Créer mon compte</button>
        </form>

        <div class="footer-link">
            Déjà inscrit ? <a href="./login.php">Connecte-toi ici</a>
        </div>
    </div>

    <script src="/js/RegisterValidator.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const validator = new RegisterValidator('registerForm');
        });
    </script>
</body>
</html>