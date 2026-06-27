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
    <title>Student Link - Inscription</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .register-container {
            background-color: #ffffff;
            width: 100%;
            max-width: 450px;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .header-area {
            text-align: center;
            margin-bottom: 25px;
        }

        .header-area h2 {
            color: #4f46e5;
            font-size: 1.8rem;
            margin-bottom: 6px;
        }

        .header-area p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-row {
            display: flex;
            gap: 12px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #4f46e5;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #4338ca;
        }

        .footer-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .footer-link a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>

    <div class="register-container">
        <div class="header-area">
            <h2>Rejoins Student Link</h2>
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