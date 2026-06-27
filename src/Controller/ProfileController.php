<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age = intval($_POST['age']);
    $interests = htmlspecialchars(trim($_POST['interests']));
    $instagram = htmlspecialchars(trim($_POST['instagram'] ?? ''));

    if ($age > 0) {
        try {
            $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE users SET age = :age, interests = :interests, instagram = :instagram WHERE id = :id";
            $stmt = $bdd->prepare($sql);
            $stmt->execute([
                'age' => $age,
                'interests' => $interests,
                'instagram' => $instagram,
                'id' => $_SESSION['user_id']
            ]);

            header('Location: ../View/profile.php?update=success');
            exit();

        } catch (Exception $e) {
            if (isset($bdd)) {
                try {
                    $logSql = "INSERT INTO logs (action_type, description) VALUES ('SYSTEM_ERROR', :desc)";
                    $logStmt = $bdd->prepare($logSql);
                    $logStmt->execute(['desc' => "Erreur SQL Mon Profil : " . $e->getMessage()]);
                } catch (Exception $ignored) {}
            }
            die('Une erreur technique est survenue.');
        }
    } else {
        die('Veuillez entrer un âge valide.');
    }
} else {
    header('Location: ../View/profile.php');
    exit();
}